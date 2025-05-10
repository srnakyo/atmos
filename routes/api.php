<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SyncController;
use App\Http\Controllers\RegistroComprasController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\CompraController;

Route::middleware('cors-extension')->group(function () {
    Route::post('/sync-status', [SyncController::class, 'update']);
    Route::get('/contas-status', [SyncController::class, 'getContasStatus']);
    
    Route::middleware('check.api.token')->group(function () {
        Route::post('/registrar-compra', [RegistroComprasController::class, 'storeApi']);
        Route::post('/criar-conta', [LoginController::class, 'criarConta']);
        Route::post('/contas-cartoes', [CompraController::class, 'listarComprasPorSku']);
        
    });
});
