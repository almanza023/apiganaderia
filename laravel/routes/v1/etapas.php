<?php

use App\Http\Controllers\V1\EtapaController;
use Illuminate\Support\Facades\Route;

        Route::get('etapas', [EtapaController::class, 'index']);
        Route::post('etapas', [EtapaController::class, 'store']);
        Route::get('etapas/{id}', [EtapaController::class, 'show']);
        Route::patch('etapas/{id}', [EtapaController::class, 'update']);
        Route::delete('etapas/{id}', [EtapaController::class, 'destroy']);
        Route::post('etapas/cambiarEstado', [EtapaController::class, 'cambiarEstado']);
        Route::get('etapas-activos', [EtapaController::class, 'activos']);

?>
