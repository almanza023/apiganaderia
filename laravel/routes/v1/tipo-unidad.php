<?php

use App\Http\Controllers\V1\TipoUnidadController;
use Illuminate\Support\Facades\Route;

        Route::get( 'tipo-unidades', [TipoUnidadController::class, 'index']);
        Route::post('tipo-unidades', [TipoUnidadController::class, 'store']);
        Route::get('tipo-unidades/{id}', [TipoUnidadController::class, 'show']);
        Route::patch('tipo-unidades/{id}', [TipoUnidadController::class, 'update']);
        Route::delete('tipo-unidades/{id}', [TipoUnidadController::class, 'destroy']);
        Route::post('tipo-unidades/cambiarEstado', [TipoUnidadController::class, 'cambiarEstado']);
        Route::get('tipo-unidades-activos', [TipoUnidadController::class, 'activos']);

?>
