<?php

use App\Http\Controllers\V1\TipoProductoController;
use Illuminate\Support\Facades\Route;

        Route::get( 'tipo-productos', [TipoProductoController::class, 'index']);
        Route::post('tipo-productos', [TipoProductoController::class, 'store']);
        Route::get('tipo-productos/{id}', [TipoProductoController::class, 'show']);
        Route::patch('tipo-productos/{id}', [TipoProductoController::class, 'update']);
        Route::delete('tipo-productos/{id}', [TipoProductoController::class, 'destroy']);
        Route::post('tipo-productos/cambiarEstado', [TipoProductoController::class, 'cambiarEstado']);
        Route::get('tipo-productos-activos', [TipoProductoController::class, 'activos']);

?>
