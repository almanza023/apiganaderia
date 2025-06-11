<?php

use App\Http\Controllers\V1\PotreroController;
use Illuminate\Support\Facades\Route;

        Route::get('potreros', [PotreroController::class, 'index']);
        Route::post('potreros', [PotreroController::class, 'store']);
        Route::get('potreros/{id}', [PotreroController::class, 'show']);
        Route::patch('potreros/{id}', [PotreroController::class, 'update']);
        Route::delete('potreros/{id}', [PotreroController::class, 'destroy']);
        Route::post('potreros/cambiarEstado', [PotreroController::class, 'cambiarEstado']);
        Route::get('potreros-activos', [PotreroController::class, 'activos']);

?>
