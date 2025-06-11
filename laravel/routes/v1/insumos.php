<?php

use App\Http\Controllers\V1\InsumoController;
use Illuminate\Support\Facades\Route;

        Route::get('insumos', [InsumoController::class, 'index']);
        Route::post('insumos', [InsumoController::class, 'store']);
        Route::post('insumos-detalles', [InsumoController::class, 'storeDetalles']);
        Route::get('insumos/{id}', [InsumoController::class, 'show']);
        Route::patch('insumos/{id}', [InsumoController::class, 'update']);
        Route::delete('insumos/{id}', [InsumoController::class, 'destroy']);
        Route::post('insumos/cambiarEstado', [InsumoController::class, 'cambiarEstado']);
        Route::get('insumos-activos', [InsumoController::class, 'activos']);
        Route::post('insumos-filter', [InsumoController::class, 'filter']);
        Route::get('insumos-animal', [InsumoController::class, 'insumosAnimal']);
        Route::get('historial-aplicaciones/{id}', [InsumoController::class, 'historialAPlicaciones']);





?>
