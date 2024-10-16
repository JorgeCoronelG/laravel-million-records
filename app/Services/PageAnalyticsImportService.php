<?php

namespace App\Services;

use App\Jobs\AnalyticsImportJob;
use Illuminate\Support\Facades\Bus;

class PageAnalyticsImportService
{
    /**
     * @throws \Throwable
     */
    public function import(array $data): string
    {
        $batch = Bus::batch([])->dispatch();

        $filePath = $data['file']->getRealPath();

        foreach($this->chunkAsGenerator($filePath) as $chunk)
        {
            $batch->add(new AnalyticsImportJob($chunk));
        }

        return $batch->id;
    }

    public function chunkAsGenerator($filePath): \Generator
    {
        // Abrir el fichero
        $handle = fopen($filePath, 'r');

        if ($handle !== false) {
            // fgetcsv — Obtiene una línea de un puntero a un fichero y la analiza en busca de campos CSV
            fgetcsv($handle, 0, ',');

            $chunkData = [];
            $chunkSize = 0;

            while(($row = fgetcsv($handle, 0, ',')) !== false) {
                $chunkData[] = $row;
                $chunkSize++;

                if($chunkSize >= 500) {
                    yield $chunkData;
                    $chunkData = [];
                    $chunkSize = 0;
                }
            }

            if (!empty($chunkData)) {
                yield $chunkData;
            }

            // Cierra un puntero a un archivo abierto
            fclose($handle);
        }
    }
}
