<?php

use App\Http\Controllers\V1\EstadisticaController;
use Illuminate\Support\Facades\Route;

        Route::get('estadisticas', [EstadisticaController::class, 'index']);


?>
