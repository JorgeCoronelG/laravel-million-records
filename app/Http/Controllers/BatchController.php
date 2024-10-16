<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Bus;

class BatchController extends Controller
{
    /**
     * Obtener el registro del progreso de importación
     *
     * @param string $batchId
     * @return JsonResponse
     */
    public function status(string $batchId): JsonResponse
    {
        return response()->json([
            'details' => Bus::findBatch($batchId)
        ]);
    }
}
