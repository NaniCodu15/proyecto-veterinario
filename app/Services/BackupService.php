<?php

namespace App\Services;

use App\Models\RespaldoDato;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class BackupService
{
    private bool $legacyDirectoryCleaned = false;

    /**
     * Genera una copia de seguridad y devuelve la respuesta estandarizada.
     */
    public function generate(): array
    {
        $this->cleanupLegacyBackupsDirectory();

        $connectionName = config('database.default');
        $connection = config("database.connections.{$connectionName}");
        $driver = is_array($connection) ? ($connection['driver'] ?? null) : null;

        if (!is_string($driver)) {
            return [
                'status' => 422,
                'data' => [
                    'message' => 'No se encontró la configuración de la conexión a la base de datos.',
                ],
            ];
        }

        $driver = strtolower($driver);

        if (!in_array($driver, ['mysql', 'mariadb', 'sqlite'], true)) {
            return [
                'status' => 422,
                'data' => [
                    'message' => 'La copia de seguridad solo está disponible para conexiones MySQL, MariaDB o SQLite.',
                ],
            ];
        }

        $timestamp = now();
        $extension = $driver === 'sqlite' ? 'sqlite' : 'sql';
        $fileName = 'backup_' . $timestamp->format('Y_m_d_His') . '.' . $extension;
        $backupDirectory = 'D:\\RespaldosVeterinario\\';
        $fullPath = $backupDirectory . $fileName;

        if (!File::isDirectory($backupDirectory)) {
            File::makeDirectory($backupDirectory, 0755, true);
        }

        $estado = 'Correcto';
        $mensaje = 'La copia de seguridad se generó correctamente.';

        try {
            if ($driver === 'sqlite') {
                $this->generateSqliteBackup($connection, $fullPath);
            } else {
                $dump = $this->generateMysqlDump($connectionName);
                File::put($fullPath, $dump);
            }
        } catch (\Throwable $exception) {
            $estado = 'Fallido';
            $mensaje = 'No se pudo generar la copia de seguridad.';
            Log::error('Error al generar la copia de seguridad', [
                'exception' => $exception->getMessage(),
            ]);

            if (File::exists($fullPath)) {
                File::delete($fullPath);
            }
        }

        $respaldo = RespaldoDato::create([
            'fecha_respaldo' => $timestamp,
            'nombre_archivo' => $fileName,
            'ruta_archivo' => $fullPath,
            'estado' => $estado,
        ]);

        return [
            'status' => $estado === 'Correcto' ? 201 : 500,
            'data' => [
                'message' => $mensaje,
                'respaldo' => [
                    'id' => $respaldo->getKey(),
                    'id_respaldo' => $respaldo->getAttribute('id_respaldo') ?? $respaldo->getKey(),
                    'fecha_respaldo' => optional($respaldo->fecha_respaldo)->toIso8601String(),
                    'nombre_archivo' => $respaldo->nombre_archivo,
                    'ruta_archivo' => $respaldo->ruta_archivo,
                    'estado' => $respaldo->estado,
                ],
            ],
        ];
    }

    /**
     * Limpia el directorio legacy de respaldos.
     */
    public function cleanupLegacyBackupsDirectory(): void
    {
        if ($this->legacyDirectoryCleaned) {
            return;
        }

        $legacyDirectory = storage_path('backups');

        if (!File::exists($legacyDirectory)) {
            $this->legacyDirectoryCleaned = true;

            return;
        }

        try {
            File::deleteDirectory($legacyDirectory);
        } catch (\Throwable $exception) {
            Log::warning('No se pudo limpiar la carpeta legacy de respaldos.', [
                'ruta' => $legacyDirectory,
                'exception' => $exception->getMessage(),
            ]);
        }

        $this->legacyDirectoryCleaned = true;
    }

    /**
     * Genera una copia de seguridad para conexiones MySQL/MariaDB.
     */
    private function generateMysqlDump(string $connectionName): string
    {
        $connection = DB::connection($connectionName);

        $lines = [
            '-- Respaldo generado el ' . now()->toDateTimeString(),
            'SET FOREIGN_KEY_CHECKS=0;',
            'SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";',
            'START TRANSACTION;',
        ];

        $tables = collect($connection->select('SHOW FULL TABLES'))
            ->filter(function ($row) {
                $values = array_values((array) $row);

                return ($values[1] ?? 'BASE TABLE') === 'BASE TABLE';
            })
            ->map(fn ($row) => (string) array_values((array) $row)[0])
            ->values();

        foreach ($tables as $table) {
            $createResult = (array) $connection->selectOne("SHOW CREATE TABLE `{$table}`");
            $createSql = $createResult['Create Table'] ?? (array_values($createResult)[1] ?? null);

            if (!$createSql) {
                continue;
            }

            $lines[] = sprintf('DROP TABLE IF EXISTS `%s`;', $table);
            $lines[] = $createSql . ';';

            $rows = $connection->table($table)->get();

            if ($rows->isEmpty()) {
                continue;
            }

            $columns = array_keys((array) $rows->first());
            $escapedColumns = array_map(fn ($column) => sprintf('`%s`', $column), $columns);

            foreach ($rows->chunk(100) as $chunk) {
                $values = $chunk->map(function ($row) use ($columns) {
                    $rowArray = (array) $row;
                    $rowValues = [];

                    foreach ($columns as $column) {
                        $rowValues[] = $this->quoteMysqlValue($rowArray[$column] ?? null);
                    }

                    return '(' . implode(', ', $rowValues) . ')';
                });

                $lines[] = sprintf(
                    'INSERT INTO `%s` (%s) VALUES %s;',
                    $table,
                    implode(', ', $escapedColumns),
                    implode(",\n", $values->all())
                );
            }
        }

        $lines[] = 'COMMIT;';
        $lines[] = 'SET FOREIGN_KEY_CHECKS=1;';

        return implode("\n\n", $lines) . "\n";
    }

    /**
     * Copia el archivo de base de datos SQLite a la ruta indicada.
     */
    private function generateSqliteBackup(array $connection, string $destination): void
    {
        $databasePath = $connection['database'] ?? null;

        if (!$databasePath || $databasePath === ':memory:') {
            throw new \RuntimeException('No se puede respaldar una base de datos SQLite en memoria.');
        }

        if (!File::exists($databasePath)) {
            $guessedPath = base_path($databasePath);

            if (File::exists($guessedPath)) {
                $databasePath = $guessedPath;
            }
        }

        if (!File::exists($databasePath)) {
            throw new \RuntimeException('No se encontró el archivo de base de datos SQLite.');
        }

        File::copy($databasePath, $destination);
    }

    /**
     * Escapa los valores para los inserts en el respaldo MySQL.
     */
    private function quoteMysqlValue(mixed $value): string
    {
        if ($value === null) {
            return 'NULL';
        }

        if (is_bool($value)) {
            return $value ? '1' : '0';
        }

        if (is_int($value) || is_float($value)) {
            return (string) $value;
        }

        $escaped = str_replace(
            ["\\", "\0", "\n", "\r", "'", '"', "\x1a"],
            ["\\\\", "\\0", "\\n", "\\r", "\\'", '\\"', "\\Z"],
            (string) $value
        );

        return "'{$escaped}'";
    }
}
