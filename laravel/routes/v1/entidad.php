<?php


use App\Http\Controllers\V1\EntidadController;
use Illuminate\Support\Facades\Route;

        Route::get('entidades', [EntidadController::class, 'index']);
        Route::post('entidades', [EntidadController::class, 'store']);
        Route::get('entidades/{id}', [EntidadController::class, 'show']);
        Route::patch('entidades/{id}', [EntidadController::class, 'update']);
        Route::delete('entidades/{id}', [EntidadController::class, 'destroy']);
        Route::post('entidades/cambiarEstado', [EntidadController::class, 'cambiarEstado']);
        Route::get('entidades-activos', [EntidadController::class, 'activos']);

?>
