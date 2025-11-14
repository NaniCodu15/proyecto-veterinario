<?php

namespace App\Services;

use App\Models\RespaldoDato;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Throwable;

class DatabaseBackupService
{
    public function createBackup(): array
    {
        $connectionName = config('database.default');
        $connection = config("database.connections.{$connectionName}");
        $driver = is_array($connection) ? ($connection['driver'] ?? null) : null;

        if (!is_string($driver)) {
            throw new \RuntimeException('No se encontró la configuración de la conexión a la base de datos.');
        }

        $driver = strtolower($driver);

        if (!in_array($driver, ['mysql', 'mariadb', 'sqlite'], true)) {
            throw new \RuntimeException('La copia de seguridad solo está disponible para conexiones MySQL, MariaDB o SQLite.');
        }

        $timestamp = now();
        $extension = $driver === 'sqlite' ? 'sqlite' : 'sql';
        $fileName = 'backup_' . $timestamp->format('Y_m_d_His') . '.' . $extension;
        $storageDirectory = storage_path('backups');
        $fullPath = $storageDirectory . DIRECTORY_SEPARATOR . $fileName;
        $relativePath = 'storage/backups/' . $fileName;
        $remotePath = null;

        if (!File::isDirectory($storageDirectory)) {
            File::makeDirectory($storageDirectory, 0755, true);
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
        } catch (Throwable $exception) {
            $estado = 'Fallido';
            $mensaje = 'No se pudo generar la copia de seguridad.';
            Log::error('Error al generar la copia de seguridad', [
                'exception' => $exception->getMessage(),
            ]);

            if (File::exists($fullPath)) {
                File::delete($fullPath);
            }
        }

        if ($estado === 'Correcto') {
            try {
                $remotePath = $this->uploadToGoogleDrive($fullPath, $fileName);
            } catch (Throwable $exception) {
                $estado = 'Fallido';
                $mensaje = 'La copia de seguridad se generó, pero no se pudo subir a Google Drive.';
                Log::error('Error al subir la copia de seguridad a Google Drive', [
                    'exception' => $exception->getMessage(),
                ]);
            }
        }

        $respaldo = RespaldoDato::create([
            'fecha_respaldo' => $timestamp,
            'nombre_archivo' => $fileName,
            'ruta_archivo' => $relativePath,
            'ruta_remota' => $remotePath,
            'estado' => $estado,
        ]);

        $this->cleanupOldBackups($storageDirectory);

        return [
            'mensaje' => $mensaje,
            'estado' => $estado,
            'respaldo' => $respaldo,
        ];
    }

    public function resolveLocalPath(RespaldoDato $respaldo): ?string
    {
        $ruta = $respaldo->ruta_archivo;

        if (is_string($ruta) && str_starts_with($ruta, 'storage/')) {
            $relative = substr($ruta, strlen('storage/'));
            $candidate = storage_path($relative);

            if (File::exists($candidate)) {
                return $candidate;
            }
        }

        $byName = storage_path('backups/' . $respaldo->nombre_archivo);

        if (File::exists($byName)) {
            return $byName;
        }

        if (is_string($ruta)) {
            $baseCandidate = base_path($ruta);

            if (File::exists($baseCandidate)) {
                return $baseCandidate;
            }
        }

        return null;
    }

    public function resolveRemotePath(RespaldoDato $respaldo): ?string
    {
        $remote = $respaldo->ruta_remota;

        return $remote ? (string) $remote : null;
    }

    private function uploadToGoogleDrive(string $fullPath, string $fileName): ?string
    {
        if (!File::exists($fullPath)) {
            return null;
        }

        $disk = Storage::disk('google');
        $remoteDirectory = 'backups';
        $remotePath = $remoteDirectory . '/' . $fileName;

        $contents = File::get($fullPath);

        $disk->put($remotePath, $contents);

        return $remotePath;
    }

    private function cleanupOldBackups(string $directory): void
    {
        if (!File::isDirectory($directory)) {
            return;
        }

        $threshold = CarbonImmutable::now()->subDays(30);

        foreach (File::files($directory) as $file) {
            $lastModified = CarbonImmutable::createFromTimestamp(File::lastModified($file));

            if ($lastModified->lessThan($threshold)) {
                File::delete($file->getPathname());
            }
        }
    }

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

        $escaped = addslashes((string) $value);

        return "'{$escaped}'";
    }
}
