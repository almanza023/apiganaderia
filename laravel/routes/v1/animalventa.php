<?php

use App\Http\Controllers\V1\AnimalVentaController;
use Illuminate\Support\Facades\Route;

        Route::get('animalventas', [AnimalVentaController::class, 'index']);
        Route::post('animalventas', [AnimalVentaController::class, 'store']);
        Route::post('animalventas-detalles', [AnimalVentaController::class, 'storeDetalles']);
        Route::get('animalventas/{id}', [AnimalVentaController::class, 'show']);
        Route::patch('animalventas/{id}', [AnimalVentaController::class, 'update']);
        Route::delete('animalventas/{id}', [AnimalVentaController::class, 'destroy']);
        Route::post('animalventas/cambiarEstado', [AnimalVentaController::class, 'cambiarEstado']);
        Route::get('animalventas-activos', [AnimalVentaController::class, 'activos']);
        Route::post('animalventas-filter', [AnimalVentaController::class, 'filter']);
        Route::get('animalventas-empresa', [AnimalVentaController::class, 'obtenerDatosEmpresa']);
        Route::get('animalventas-getCodigo', [AnimalVentaController::class, 'getCodigo']);
        Route::post('animalventas-getByNumero', [AnimalVentaController::class, 'showByNumero']);
        Route::get('animalventas-insumos/{id}', [AnimalVentaController::class, 'getInsumosPorAnimal']);
        Route::delete('animalventas-detalles/{id}', [AnimalVentaController::class, 'destroyDetalle']);
        Route::post('animalventas-consolidado-general', [AnimalVentaController::class, 'reporteConsolidadoGeneral']);
        Route::post('animalventas-individual', [AnimalVentaController::class, 'reporteAnimal']);





?>
