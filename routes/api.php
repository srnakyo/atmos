<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SyncController;
use App\Http\Controllers\RegistroComprasController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\CompraController;
use App\Http\Controllers\DropController;
use App\Http\Controllers\TasksController;

Route::middleware('cors-extension')->group(function () {
    Route::post('/sync-status', [SyncController::class, 'update']);
    Route::get('/contas-status/{id}', [SyncController::class, 'getContasStatus']);
    Route::get('/get-tasks', [TasksController::class, 'getTasks']);
    Route::post('/registrar-compra', [RegistroComprasController::class, 'storeApi']);

    // Cookies
    Route::get('/cookies/get/{quantidade?}', [CompraController::class, 'distribuirCookie']);


    Route::middleware('check.api.token')->group(function () {
        Route::post('/criar-conta', [LoginController::class, 'criarConta']);
        Route::post('/contas-cartoes', [CompraController::class, 'listarComprasPorSku']);
        Route::post('/restock', [CompraController::class, 'listarComprasPorSkuRestock']);
        
        // Sistema de Cookies
        Route::post('/cookies/store', [CompraController::class, 'inserirCookie']);
        Route::get('/cookies/usados', [CompraController::class, 'listarCookiesUsados']);
    });
});
