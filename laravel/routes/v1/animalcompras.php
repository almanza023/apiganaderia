<?php

use App\Http\Controllers\V1\AnimalCompraController;
use Illuminate\Support\Facades\Route;

        Route::get('animalcompras', [AnimalCompraController::class, 'index']);
        Route::post('animalcompras', [AnimalCompraController::class, 'store']);
        Route::post('animalcompras-detalles', [AnimalCompraController::class, 'storeDetalles']);
        Route::get('animalcompras/{id}', [AnimalCompraController::class, 'show']);
        Route::patch('animalcompras/{id}', [AnimalCompraController::class, 'update']);
        Route::delete('animalcompras/{id}', [AnimalCompraController::class, 'destroy']);
        Route::post('animalcompras/cambiarEstado', [AnimalCompraController::class, 'cambiarEstado']);
        Route::get('animalcompras-activos', [AnimalCompraController::class, 'activos']);
        Route::delete('animalcompras-detalles/{id}', [AnimalCompraController::class, 'destroyDetalle']);
        Route::post('animalcompras-filter', [AnimalCompraController::class, 'filter']);
        Route::get('animalcompras-getCodigo', [AnimalCompraController::class, 'getCodigo']);
        Route::post('animalcompras-getByNumero', [AnimalCompraController::class, 'showByNumero']);


?>
