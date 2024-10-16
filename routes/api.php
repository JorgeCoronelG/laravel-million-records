<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BatchController;
use App\Http\Controllers\PageAnalyticController;

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::get("/import-status/{batchId}", [BatchController::class, 'status']);

    Route::post("/import-analytics", [PageAnalyticController::class, 'store']);
});
