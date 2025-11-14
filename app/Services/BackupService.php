<?php

namespace App\Services;

use App\Models\RespaldoDato;
use Carbon\Carbon;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

class BackupService
{
    /**
     * Genera el archivo SQL o SQLite del respaldo.
     *
     * @return array{
     *     estado: string,
     *     mensaje: string,
     *     fileName: string,
     *     fullPath: string|null,
     *     relativePath: string,
     *     timestamp: \Carbon\Carbon,
     *     code: int,
     * }
     */
    public function generateBackupSql(): array
    {
        $timestamp = now();
        $connectionName = config('database.default');
        $connection = config("database.connections.{$connectionName}");
        $driver = is_array($connection) ? Arr::get($connection, 'driver') : null;

        $fileName = 'backup_' . $timestamp->format('Y_m_d_His') . '.sql';
        $relativePath = 'storage/backups/' . $fileName;
        $storageDirectory = storage_path('backups');
        $fullPath = $storageDirectory . DIRECTORY_SEPARATOR . $fileName;

        if (is_string($driver)) {
            $driver = strtolower($driver);
        }

        if (!is_string($driver)) {
            return [
                'estado' => 'Fallido',
                'mensaje' => 'No se encontró la configuración de la conexión a la base de datos.',
                'fileName' => $fileName,
                'fullPath' => null,
                'relativePath' => $relativePath,
                'timestamp' => $timestamp,
                'code' => Response::HTTP_UNPROCESSABLE_ENTITY,
            ];
        }

        if (!in_array($driver, ['mysql', 'mariadb', 'sqlite'], true)) {
            return [
                'estado' => 'Fallido',
                'mensaje' => 'La copia de seguridad solo está disponible para conexiones MySQL, MariaDB o SQLite.',
                'fileName' => $fileName,
                'fullPath' => null,
                'relativePath' => $relativePath,
                'timestamp' => $timestamp,
                'code' => Response::HTTP_UNPROCESSABLE_ENTITY,
            ];
        }

        $extension = $driver === 'sqlite' ? 'sqlite' : 'sql';
        $fileName = 'backup_' . $timestamp->format('Y_m_d_His') . '.' . $extension;
        $relativePath = 'storage/backups/' . $fileName;
        $fullPath = $storageDirectory . DIRECTORY_SEPARATOR . $fileName;

        if (!File::isDirectory($storageDirectory)) {
            File::makeDirectory($storageDirectory, 0755, true);
        }

        $estado = 'Correcto';
        $mensaje = 'La copia de seguridad se generó correctamente.';

        try {
            if ($driver === 'sqlite') {
                $this->generateSqliteBackup(is_array($connection) ? $connection : [], $fullPath);
            } else {
                $dump = $this->generateMysqlDump($connectionName);
                File::put($fullPath, $dump);
            }
        } catch (\Throwable $exception) {
            $estado = 'Fallido';
            $mensaje = 'No se pudo generar la copia de seguridad.';
            $fullPath = null;

            Log::error('Error al generar la copia de seguridad', [
                'exception' => $exception->getMessage(),
            ]);

            if (!empty($storageDirectory) && File::exists($storageDirectory)) {
                $archivoFallido = $storageDirectory . DIRECTORY_SEPARATOR . $fileName;

                if (File::exists($archivoFallido)) {
                    File::delete($archivoFallido);
                }
            }
        }

        return [
            'estado' => $estado,
            'mensaje' => $mensaje,
            'fileName' => $fileName,
            'fullPath' => $fullPath,
            'relativePath' => $relativePath,
            'timestamp' => $timestamp,
            'code' => $estado === 'Correcto' ? Response::HTTP_CREATED : Response::HTTP_INTERNAL_SERVER_ERROR,
        ];
    }

    /**
     * Sube el respaldo al disco de Google Drive.
     *
     * @return array{estado: string, mensaje: string, ruta: string|null}
     */
    public function uploadBackupToDrive(string $fullPath, string $fileName): array
    {
        $estado = 'Correcto';
        $mensaje = 'Respaldo subido correctamente a Google Drive.';
        $ruta = null;

        try {
            $disk = Storage::disk('google');
            $path = 'backups/' . $fileName;

            $stream = fopen($fullPath, 'r');
            $disk->put($path, $stream);

            if (is_resource($stream)) {
                fclose($stream);
            }

            $ruta = $this->resolveGoogleDrivePath($disk, $path);
        } catch (\Throwable $exception) {
            $estado = 'Fallido';
            $mensaje = 'La copia de seguridad local se generó, pero no se pudo subir a Google Drive.';
            $ruta = null;

            Log::error('Error al subir la copia de seguridad a Google Drive', [
                'exception' => $exception->getMessage(),
            ]);
        }

        return compact('estado', 'mensaje', 'ruta');
    }

    /**
     * Registra el respaldo en la base de datos.
     */
    public function registerBackup(Carbon $fecha, string $nombreArchivo, ?string $rutaArchivo, string $estado): RespaldoDato
    {
        return RespaldoDato::create([
            'fecha_respaldo' => $fecha,
            'nombre_archivo' => $nombreArchivo,
            'ruta_archivo' => $rutaArchivo ?? '',
            'estado' => $estado,
        ]);
    }

    /**
     * Elimina los respaldos locales con más de 30 días de antigüedad.
     */
    public function cleanOldLocalBackups(): void
    {
        $directorio = storage_path('backups');

        if (!File::isDirectory($directorio)) {
            return;
        }

        $limite = now()->subDays(30)->getTimestamp();

        foreach (File::files($directorio) as $archivo) {
            if ($archivo->getMTime() < $limite) {
                File::delete($archivo->getPathname());
            }
        }
    }

    /**
     * Descarga o redirige según el origen del respaldo.
     */
    public function downloadBackup(RespaldoDato $respaldo): Response
    {
        $ruta = (string) $respaldo->ruta_archivo;

        if (Str::startsWith($ruta, 'storage/')) {
            $rutaRelativa = Str::after($ruta, 'storage/backups/');

            if ($rutaRelativa === $ruta) {
                $rutaRelativa = Str::after($ruta, 'storage/');
            }

            if (!Storage::disk('backups')->exists($rutaRelativa)) {
                abort(Response::HTTP_NOT_FOUND, 'El archivo de respaldo no está disponible en el almacenamiento local.');
            }

            return Storage::disk('backups')->download($rutaRelativa, $respaldo->nombre_archivo);
        }

        $fileId = $this->extractDriveFileId($ruta);

        if ($fileId === null) {
            try {
                $disk = Storage::disk('google');
                $ruta = (string) $this->resolveGoogleDrivePath($disk, $ruta);
                $fileId = $this->extractDriveFileId($ruta);
            } catch (\Throwable $exception) {
                Log::debug('No se pudo resolver el archivo en Google Drive durante la descarga.', [
                    'exception' => $exception->getMessage(),
                ]);
            }
        }

        if ($fileId === null) {
            abort(Response::HTTP_NOT_FOUND, 'No se encontró la ruta del respaldo.');
        }

        $url = sprintf('https://drive.google.com/uc?export=download&id=%s', $fileId);

        return new RedirectResponse($url);
    }

    /**
     * Ejecuta todo el flujo de respaldo.
     *
     * @return array{
     *     estado: string,
     *     mensaje: string,
     *     respaldo: \App\Models\RespaldoDato,
     *     code: int,
     * }
     */
    public function handleBackup(): array
    {
        $resultado = $this->generateBackupSql();
        $estado = $resultado['estado'];
        $mensaje = $resultado['mensaje'];
        $rutaRegistro = $resultado['relativePath'];
        $respaldo = null;

        if ($estado === 'Correcto' && $resultado['fullPath']) {
            $drive = $this->uploadBackupToDrive($resultado['fullPath'], $resultado['fileName']);

            if ($drive['estado'] === 'Correcto' && !empty($drive['ruta'])) {
                $rutaRegistro = $drive['ruta'];
            } else {
                $estado = 'Fallido';
                $mensaje = $drive['mensaje'];
            }
        }

        try {
            $respaldo = $this->registerBackup(
                $resultado['timestamp'],
                $resultado['fileName'],
                $rutaRegistro,
                $estado,
            );
        } catch (\Throwable $exception) {
            $estado = 'Fallido';
            $mensaje = 'No se pudo registrar la copia de seguridad.';

            Log::error('Error al registrar el respaldo en la base de datos', [
                'exception' => $exception->getMessage(),
            ]);

            $respaldo = RespaldoDato::make([
                'fecha_respaldo' => $resultado['timestamp'],
                'nombre_archivo' => $resultado['fileName'],
                'ruta_archivo' => $rutaRegistro,
                'estado' => $estado,
            ]);
        } finally {
            $this->cleanOldLocalBackups();
        }

        $codigo = $resultado['code'] ?? Response::HTTP_INTERNAL_SERVER_ERROR;

        if ($estado !== 'Correcto' && $codigo === Response::HTTP_CREATED) {
            $codigo = Response::HTTP_INTERNAL_SERVER_ERROR;
        }

        return [
            'estado' => $estado,
            'mensaje' => $mensaje,
            'respaldo' => $respaldo,
            'code' => $codigo,
        ];
    }

    /**
     * Obtiene el contenido del volcado MySQL/MariaDB.
     */
    protected function generateMysqlDump(string $connectionName): string
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
                    implode(",\n", $values->all()),
                );
            }
        }

        $lines[] = 'COMMIT;';
        $lines[] = 'SET FOREIGN_KEY_CHECKS=1;';

        return implode("\n\n", $lines) . "\n";
    }

    /**
     * Copia el archivo SQLite a la ruta indicada.
     */
    protected function generateSqliteBackup(array $connection, string $destination): void
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
     * Escapa valores para los inserts MySQL.
     */
    protected function quoteMysqlValue(mixed $value): string
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
            (string) $value,
        );

        return "'{$escaped}'";
    }

    /**
     * Obtiene la ruta pública o el ID del archivo en Google Drive.
     */
    protected function resolveGoogleDrivePath(Filesystem $disk, string $path): ?string
    {
        $adapter = method_exists($disk, 'getAdapter') ? $disk->getAdapter() : null;

        if ($adapter && method_exists($adapter, 'getMetadata')) {
            $metadata = $adapter->getMetadata($path);

            if (is_array($metadata)) {
                if (!empty($metadata['file_id'])) {
                    return (string) $metadata['file_id'];
                }

                if (!empty($metadata['path'])) {
                    return (string) $metadata['path'];
                }
            }
        }

        if (method_exists($disk, 'url')) {
            try {
                return $disk->url($path);
            } catch (\Throwable $exception) {
                Log::debug('No se pudo obtener la URL del archivo en Google Drive.', [
                    'exception' => $exception->getMessage(),
                ]);
            }
        }

        return $path;
    }

    /**
     * Obtiene el ID de un archivo de Google Drive a partir de la ruta almacenada.
     */
    protected function extractDriveFileId(string $ruta): ?string
    {
        if (Str::startsWith($ruta, 'http')) {
            $componentes = parse_url($ruta);
            $query = $componentes['query'] ?? '';
            parse_str($query, $params);

            if (!empty($params['id'])) {
                return $params['id'];
            }

            if (!empty($params['fileId'])) {
                return $params['fileId'];
            }
        }

        if (preg_match('/[-\w]{25,}/', $ruta, $coincidencias)) {
            return $coincidencias[0];
        }

        return null;
    }
}
