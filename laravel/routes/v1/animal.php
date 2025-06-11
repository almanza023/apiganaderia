<?php

use App\Http\Controllers\V1\AnimalController;
use Illuminate\Support\Facades\Route;

        Route::get('animales', [AnimalController::class, 'index']);
        Route::post('animales', [AnimalController::class, 'store']);
        Route::get('animales/{id}', [AnimalController::class, 'show']);
        Route::patch('animales/{id}', [AnimalController::class, 'update']);
        Route::delete('animales/{id}', [AnimalController::class, 'destroy']);
        Route::post('animales/cambiarEstado', [AnimalController::class, 'cambiarEstado']);
        Route::get('animales-activos', [AnimalController::class, 'activos']);
        Route::get('animales-compra', [AnimalController::class, 'getAnimalesCompra']);

?>
