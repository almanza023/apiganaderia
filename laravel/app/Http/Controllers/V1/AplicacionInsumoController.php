<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\Aplicacion;
use App\Models\Insumo;
use App\Models\Producto;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;

class AplicacionInsumoController extends Controller
{
    protected $model;

    public function __construct()
    {
        $this->model = Aplicacion::class;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Listamos todos los productos
        $productos = $this->model::getAll();
        return response()->json([
            'code' => 200,
            'isSuccess' => true,
            'data' => $productos
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
        $data = $request->only('insumo_id', 'animal_id', 'cantidad_aplicada', 'observaciones' );
        $validator = Validator::make($data, [
            'insumo_id' => 'required|exists:insumos,id',
            'animal_id' => 'required|exists:animales,id',
            'cantidad_aplicada' => 'required|numeric',
        ]);

        // Si falla la validación
        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], 400);
        }
        $insumo=Insumo::find($request->insumo_id);
        if(empty($insumo)){
            return response()->json([
                'code' => 404,
                'isSuccess' => false,
                'message' => 'Insumo no encontrado'
            ], 404);
        }

        if($request->cantidad_aplicada > $insumo->contenido_restante){
            return response()->json([
                'code' => 400,
                'isSuccess' => false,
                'message' => 'No hay suficiente insumo'
            ], 400);
        }
        $insumo->contenido_restante=$insumo->contenido_restante-$request->cantidad_aplicada;
        $insumo->contenido_aplicado=$insumo->contenido_aplicado+$request->cantidad_aplicada;
        $insumo->save();

        $aplicacion=$this->model::create([
            'insumo_id'=>$request->insumo_id,
            'animal_id'=>$request->animal_id,
            'fecha'=>Carbon::now(),
            'cantidad_aplicada'=>$request->cantidad_aplicada,
            'observaciones'=>$request->observaciones,
        ]);

        // Respuesta en caso de que todo vaya bien

            return response()->json([
                'code' => 200,
                'isSuccess' => true,
                'message' => 'Aplicacion Creada Exitosamente',
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
        $aplicacion = $this->model::find($id);

        // Si el producto no existe devolvemos error no encontrado
        if (!$aplicacion) {
            return response()->json([
                'code' => 404,
                'isSuccess' => false,
                'message' => 'Producto no encontrado'
            ], 404);
        }

        return response()->json([
            'code' => 200,
            'isSuccess' => true,
            'data' => $aplicacion
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
        $data = $request->only('insumo_id', 'animal_id', 'fecha', 'cantidad_aplicada', 'observaciones' );
            $validator = Validator::make($data, [
                'insumo_id' => 'required|exists:insumos,id',
                'animal_id' => 'required|exists:animales,id',
                'fecha' => 'required|date',
                'cantidad_aplicada' => 'required|numeric',
            ]);
        // Buscamos el producto
        $aplicacion = $this->model::findOrFail($id);
        $insumo=Insumo::find($request->insumo_id);
        if(empty($insumo)){
            return response()->json([
                'code' => 404,
                'isSuccess' => false,
                'message' => 'Insumo no encontrado'
            ], Response::HTTP_OK);
        }

        if($request->cantidad_aplicada > $insumo->contenido_restante){
            return response()->json([
                'code' => 400,
                'isSuccess' => false,
                'message' => 'No hay suficiente insumo'
            ], Response::HTTP_OK);
        }

        if($request->cantidad_aplicada > $aplicacion->cantidad_aplicada){
            $insumo->contenido_restante -= ($request->cantidad_aplicada - $aplicacion->cantidad_aplicada);
        } elseif ($request->cantidad_aplicada < $aplicacion->cantidad_aplicada) {
            $insumo->contenido_restante += ($aplicacion->cantidad_aplicada - $request->cantidad_aplicada);
        }else{
            $insumo->contenido_restante -= ($request->cantidad_aplicada - $aplicacion->cantidad_aplicada);
        }
        $insumo->contenido_aplicado= $insumo->total_contenido - $insumo->contenido_restante ;

        $insumo->save();

                $aplicacion->update([
                    'insumo_id'=>$request->insumo_id,
                    'animal_id'=>$request->animal_id,
                    'fecha'=>$request->fecha,
                    'cantidad_aplicada'=>$request->cantidad_aplicada,
                    'observaciones'=>$request->observaciones,
                ]);


            return response()->json([
                'code' => 200,
                'isSuccess' => true,
                'message' => 'Aplicación Actualizada Exitosamente',
            ], Response::HTTP_OK);
        }

    public function destroy($id)
    {
        // Buscamos el producto
        $aplicacion = $this->model::findOrFail($id);
        // Eliminamos el producto
        $aplicacion->delete();
        // Devolvemos la respuesta
        return response()->json([
            'code' => 200,
            'isSuccess' => true,
            'message' => 'Aplicación Eliminada Exitosamente'
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
        $aplicacion = $this->model::findOrFail($request->id);
        $insumo=Insumo::find($aplicacion->insumo_id);
        if(empty($insumo)){
            return response()->json([
                'code' => 404,
                'isSuccess' => false,
                'message' => 'Insumo no encontrado'
            ], Response::HTTP_OK);
        }
        if($aplicacion->estado==1){
            $insumo->contenido_restante += $aplicacion->cantidad_aplicada;
            $insumo->contenido_aplicado -= $aplicacion->cantidad_aplicada;
            $insumo->save();
        }
        if($aplicacion->estado==2){
            if($aplicacion->cantidad_aplicada>$insumo->contenido_restante){
                return response()->json([
                    'code' => 400,
                    'isSuccess' => false,
                    'message' => 'No hay suficiente insumo'
                ], Response::HTTP_OK);
            }
            $insumo->contenido_restante -= $aplicacion->cantidad_aplicada;
            $insumo->contenido_aplicado += $aplicacion->cantidad_aplicada;
            $insumo->save();
        }
        // Cambiamos el estado
        $aplicacion->estado = ($aplicacion->estado == 1) ? 2 : 1;
        $aplicacion->save();

        // Devolvemos la respuesta
        return response()->json([
            'code' => 200,
            'isSuccess' => true,
            'message' => 'Estado de la Aplicación Actualizado Exitosamente',
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
        $aplicaciones = $this->model::getActive();
        if ($aplicaciones) {
            return response()->json([
                'code' => 200,
                'data' => $aplicaciones
            ], Response::HTTP_OK);
        } else {
            return response()->json([
                'code' => 200,
                'data' => []
            ], Response::HTTP_OK);
        }
    }





}
