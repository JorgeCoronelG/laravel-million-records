<?php

namespace App\Http\Controllers;

use App\Services\PageAnalyticsImportService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response as Code;

class PageAnalyticController extends Controller
{
    /**
     * @throws \Throwable
     */
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'file' => ['required', 'file', 'mimes:csv', 'max:61440']
        ]);

        $batchId = (new PageAnalyticsImportService())->import($data);

        return response()->json(['batchId' => $batchId], Code::HTTP_CREATED);
    }
}
