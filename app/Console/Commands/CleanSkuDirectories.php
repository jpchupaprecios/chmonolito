<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;

class CleanSkuDirectories extends Command
{
    protected $signature = 'clean:sku-directories';
    protected $description = 'Eliminar carpetas y registros de SKU';

    public function handle()
    {
        $basePath = storage_path('app/skus');

        // Verificar si la carpeta base existe
        if (!File::exists($basePath)) {
            $this->error("El directorio base no existe: {$basePath}");
            return 1;
        }

        // Listar carpetas disponibles
        $folders = array_filter(File::directories($basePath), function ($folder) {
            return basename($folder); // Filtrar carpetas válidas
        });

        if (empty($folders)) {
            $this->info("No se encontraron carpetas para eliminar.");
            return 0;
        }

        // Preguntar al usuario si desea eliminar todo
        $deleteAll = $this->confirm('¿Deseas eliminar todas las carpetas y registros de SKU?');

        if ($deleteAll) {
            // Eliminar todas las carpetas y registros
            foreach ($folders as $folder) {
                $this->deleteFolderAndRecord(basename($folder), $basePath);
            }
            $this->info('Todas las carpetas y registros de SKU fueron eliminados.');
        } else {
            // Mostrar carpetas disponibles
            $sku = $this->choice(
                'Selecciona la carpeta padre que deseas eliminar',
                array_map('basename', $folders),
                0
            );

            // Eliminar carpeta y registros correspondientes
            $this->deleteFolderAndRecord($sku, $basePath);
            $this->info("La carpeta y los registros asociados al SKU '{$sku}' fueron eliminados.");
        }

        return 0;
    }

    private function deleteFolderAndRecord($sku, $basePath)
    {
        $folderPath = "{$basePath}/{$sku}";

        // Intentar eliminar la carpeta
        if (File::deleteDirectory($folderPath)) {
            $this->info("Carpeta eliminada: {$folderPath}");
        } else {
            $this->error("No se pudo eliminar la carpeta: {$folderPath}");
        }

        // Eliminar registros en la base de datos
        $deletedRows = DB::table('product_tries')->where('sku', $sku)->delete();
        if ($deletedRows) {
            $this->info("Registros eliminados de la tabla 'product_tries' para el SKU: {$sku}");
        } else {
            $this->error("No se encontraron registros en la tabla 'product_tries' para el SKU: {$sku}");
        }
    }


}
