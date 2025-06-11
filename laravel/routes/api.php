<?php

use App\Http\Controllers\V1\AuthController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    //Prefijo V1, todo lo que este dentro de este grupo se accedera escribiendo v1 en el navegador, es decir /api/v1/*

    require __DIR__ . '/v1/auth.php';

    //Route::group(['middleware' => ['jwt.verify']], function() {
    //Todo lo que este dentro de este grupo requiere verificaci��n de usuario.

    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('cambiar-clave', [AuthController::class, 'cambiarClave']);
    Route::post('get-user', [AuthController::class, 'getUser']);
    //animales
    require __DIR__ . '/v1/animal.php';
    require __DIR__ . '/v1/potrero.php';
    require __DIR__ . '/v1/entidad.php';
    require __DIR__ . '/v1/animalcompras.php';
    require __DIR__ . '/v1/animalventa.php';
    require __DIR__ . '/v1/etapas.php';
    require __DIR__ . '/v1/tipo-producto.php';
    require __DIR__ . '/v1/tipo-unidad.php';
    require __DIR__ . '/v1/productos.php';
    require __DIR__ . '/v1/insumos.php';
    require __DIR__ . '/v1/aplicaciones.php';
    require __DIR__ . '/v1/estadisticas.php';
    require __DIR__ . '/v1/usuarios.php';
});
