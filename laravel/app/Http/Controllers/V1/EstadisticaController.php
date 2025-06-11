<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\Animal;
use App\Models\AnimalCompra;
use App\Models\AnimalVenta;
use Symfony\Component\HttpFoundation\Response;

class EstadisticaController extends Controller
{
    protected $model;


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Listamos todos los gastos
        $cantidadAnimales= Animal::where('estado',1)->count();
        $cantidadVentas= AnimalVenta::where('estado',1)->count();
        $totalCompras=AnimalCompra::where('estado',1)->sum('total');
        $totalVentas=AnimalVenta::where('estado',1)->sum('total');
        return response()->json([
            'code' => 200,
            'isSuccess' => true,
            'data' => [
                'cantidadAnimales'=>$cantidadAnimales,
                'cantidadVentas'=>$cantidadVentas,
                'totalCompras'=>$totalCompras,
                'totalVentas'=>$totalVentas
            ]
        ], Response::HTTP_OK);
    }


}
