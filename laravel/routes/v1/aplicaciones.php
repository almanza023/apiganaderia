<?php

use App\Http\Controllers\V1\AplicacionInsumoController;
use Illuminate\Support\Facades\Route;

        Route::get('aplicaciones', [AplicacionInsumoController::class, 'index']);
        Route::post('aplicaciones', [AplicacionInsumoController::class, 'store']);
        Route::get('aplicaciones/{id}', [AplicacionInsumoController::class, 'show']);
        Route::patch('aplicaciones/{id}', [AplicacionInsumoController::class, 'update']);
        Route::delete('aplicaciones/{id}', [AplicacionInsumoController::class, 'destroy']);
        Route::post('aplicaciones/cambiarEstado', [AplicacionInsumoController::class, 'cambiarEstado']);
        Route::get('aplicaciones-activos', [AplicacionInsumoController::class, 'activos']);

?>
