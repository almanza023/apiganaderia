<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\Insumo;
use App\Models\Producto;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;

class InsumoController extends Controller
{
    protected $model;

    public function __construct()
    {
        $this->model = Insumo::class;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Listamos todos los productos
        $insumos = $this->model::getAll();
        return response()->json([
            'code' => 200,
            'isSuccess' => true,
            'data' => $insumos
        ], Response::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
                // Validamos los datos
        $data = $request->only('id_producto', 'precio', 'cantidad', 'total', 'destino');
        $validator = Validator::make($data, [
            'id_producto' => 'required|exists:productos,id',
            'precio' => 'required|numeric',
            'cantidad' => 'required|numeric',
            'total' => 'required|numeric',
            'destino' => 'required|string',
        ]);

        // Si falla la validación
        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], 400);
        }

        $producto=Producto::find($request->id_producto);
        if(empty($producto)){
            return response()->json([
                'code' => 404,
                'isSuccess' => false,
                'message' => 'Producto no encontrado'
            ], 404);
        }
        $total_contenido=$producto->contenido * $request->cantidad;
        $insumo=$this->model::create([
            'id_producto'=>$request->id_producto,
            'precio'=>$request->precio,
            'cantidad'=>$request->cantidad,
            'total_contenido'=>$total_contenido,
            'contenido_restante'=>$total_contenido,
            'total'=>$request->total,
            'destino'=>$request->destino,
            'fecha'=>Carbon::now(),
        ]);

        // Respuesta en caso de que todo vaya bien

            return response()->json([
                'code' => 200,
                'isSuccess' => true,
                'message' => 'Insumo Creado Exitosamente',
            ], Response::HTTP_OK);

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        // Buscamos el producto
        $insumo = $this->model::find($id);

        // Si el producto no existe devolvemos error no encontrado
        if (!$insumo) {
            return response()->json([
                'code' => 404,
                'isSuccess' => false,
                'message' => 'Insumo no encontrado'
            ], 404);
        }

        return response()->json([
            'code' => 200,
            'isSuccess' => true,
            'data' => $insumo
        ], Response::HTTP_OK);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        // Validamos los datos
        $data = $request->only('id_producto', 'precio', 'cantidad', 'total', 'destino');
            $validator = Validator::make($data, [
                'id_producto' => 'required|exists:productos,id',
                'precio' => 'required|numeric',
                'cantidad' => 'required|numeric',
                'total' => 'required|numeric',
                'destino' => 'required|string',
            ]);
        // Buscamos el producto
        $insumo = $this->model::findOrFail($id);

                $insumo->update([
                    'id_producto'=>$request->id_producto,
                    'precio'=>$request->precio,
                    'cantidad'=>$request->cantidad,
                    'total'=>$request->total,
                    'destino'=>$request->destino,
                ]);


            return response()->json([
                'code' => 200,
                'isSuccess' => true,
                'message' => 'Insumo Actualizado Exitosamente',
            ], Response::HTTP_OK);
        }

    public function destroy($id)
    {
        // Buscamos el producto
        $insumo = $this->model::findOrFail($id);
        // Eliminamos el producto
        $insumo->delete();
        // Devolvemos la respuesta
        return response()->json([
            'code' => 200,
            'isSuccess' => true,
            'message' => 'Insumo Eliminado Exitosamente'
        ], Response::HTTP_OK);
    }

    /**
     * Cambiar el estado de un producto (Activo/Inactivo)
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function cambiarEstado(Request $request)
    {
        // Validación de datos
        $data = $request->only('id');
        $validator = Validator::make($data, [
            'id' => 'required'
        ]);

        // Si falla la validación error.
        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], 400);
        }

        // Buscamos el producto
        $producto = $this->model::findOrFail($request->id);

        // Cambiamos el estado
        $producto->estado = ($producto->estado == 1) ? 2 : 1;
        $producto->save();

        // Devolvemos la respuesta
        return response()->json([
            'code' => 200,
            'isSuccess' => true,
            'message' => 'Estado del Insumo Actualizado Exitosamente',
        ], Response::HTTP_OK);
    }

    /**
     * Listar todos los productos activos.
     *
     * @return \Illuminate\Http\Response
     */
    public function activos()
    {
        // Listamos todos los registros activos
        $productos = $this->model::getActive();
        if ($productos) {
            return response()->json([
                'code' => 200,
                'data' => $productos
            ], Response::HTTP_OK);
        } else {
            return response()->json([
                'code' => 200,
                'data' => []
            ], Response::HTTP_OK);
        }
    }

    public function filter(Request $request)
    {
        // Listamos todas las compras
        $fechaInicio=Carbon::parse($request->fecha_inicio)->format('Y-m-d');
        $fechaFin=Carbon::parse($request->fecha_fin)->format('Y-m-d');


        $objeto = $this->model::getFilter($fechaInicio,
            $fechaFin);
        if ($objeto->count() > 0) {
            return response()->json([
                'code' => 200,
                'isSuccess' => true,
                'data' => $objeto
            ], Response::HTTP_OK);
        } else {
            return response()->json([
                'code' => 200,
                'isSuccess' => false,
                'message'=>'No se encontrarón registros',
                'data' => []
            ], Response::HTTP_OK);
        }
    }

    public function insumosAnimal()
    {
        // Listamos todos los registros activos
        $productos = $this->model::getInsumosPorDestinoAnimal();
        if ($productos) {
            return response()->json([
                'code' => 200,
                'data' => $productos
            ], Response::HTTP_OK);
        } else {
            return response()->json([
                'code' => 200,
                'data' => []
            ], Response::HTTP_OK);
        }
    }

    public function historialAPlicaciones($id)
    {
        // Listamos todos los registros activos
        $productos = $this->model::getHistorialAplicaciones($id);
        if ($productos) {
            return response()->json([
                'code' => 200,
                'isSuccess' => true,
                'data' => $productos
            ], Response::HTTP_OK);
        } else {
            return response()->json([
                'code' => 200,
                'isSuccess' => false,
                'data' => []
            ], Response::HTTP_OK);
        }
    }






}
