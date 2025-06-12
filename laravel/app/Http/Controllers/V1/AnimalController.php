<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\Animal;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;

class AnimalController extends Controller
{
    protected $model;

    public function __construct(Request $request)
    {
        $this->model=Animal::class;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //Listamos todos las sedes
       $objeto=$this->model::get();
       if($objeto){
        return response()->json([
            'code'=>200,
            'isSuccess'=>true,
            'data' => $objeto
        ], Response::HTTP_OK);
       }else{
        return response()->json([
            'code'=>200,
            'isSuccess'=>false,
            'data' => []
        ], Response::HTTP_OK);
       }
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //Validamos los datos
        $data = $request->only('nombre', 'codigo', 'sexo', 'etapa', 'numero',
     'fechaNacimiento', 'peso', 'observaciones');
        $validator = Validator::make($data, [
            'nombre' => 'required|max:200|string',
            'numero' => 'required|unique:animales,numero',
            'sexo' => 'required|string',
            'etapa' => 'required|string',
            'peso' => 'required|string',
        ]);

        //Si falla la validación
        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], 400);
        }

        //Creamos el producto en la BD
        $objeto = $this->model::create([
            'nombre'=>strtoupper($request->nombre),
            'numero'=>($request->numero),
            'codigo'=>($request->codigo),
            'sexo'=>($request->sexo),
            'etapa'=>($request->etapa),
            'fechaNacimiento'=>($request->fechaNacimiento),
            'peso'=>($request->peso),
            'observaciones'=>($request->observaciones),
        ]);

        //Respuesta en caso de que todo vaya bien.
        return response()->json([
            'code'=>200,
            'isSuccess'=>true,
            'data'=>$objeto,
            'message' => 'Animal Creado Exitosamente',
        ], Response::HTTP_OK);
    }

    public function show($id)
    {
        //Bucamos el producto
        $objeto = $this->model::find($id);

        //Si el producto no existe devolvemos error no encontrado
        if (!$objeto) {
            return response()->json([
                'code'=>200,
                'isSuccess'=>true,
                'message' => 'Registro no encontrado en la base de datos.'
            ], 404);
        }
        return response()->json([
            'code'=>200,
            'isSuccess'=>true,
            'data' => $objeto
        ], Response::HTTP_OK);
    }

    public function update(Request $request, $id)
    {
        //Validamos los datos
        $data = $request->only('nombre', 'codigo', 'sexo', 'etapa', 'numero',
     'fechaNacimiento', 'peso', 'observaciones');
        $validator = Validator::make($data, [
            'nombre' => 'required|max:200|string',
            'sexo' => 'required|string',
            'numero' => 'required|unique:animales,numero,'.$id,
            'etapa' => 'required|string',
            'peso' => 'required|string',
        ]);

        //Si falla la validación error.
        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], 400);
        }

        //Buscamos la Sede
        $objeto = $this->model::findOrfail($id);

        //Actualizamos la sede.
        $objeto->update([
            'nombre'=>strtoupper($request->nombre),
            'codigo'=>$request->codigo,
            'numero'=>$request->numero,
            'sexo'=>$request->sexo,
            'etapa'=>$request->etapa,
            'fechaNacimiento'=>$request->fechaNacimiento,
            'peso'=>$request->peso,
            'observaciones'=>$request->observaciones,
        ]);

        //Devolvemos los datos actualizados.
        return response()->json([
            'code'=>200,
            'isSuccess'=>true,
            'message' => 'Animal Actualizado Exitosamente',
        ], Response::HTTP_OK);
    }

    public function destroy($id)
    {
        //Buscamos el producto
        $objeto = $this->model::findOrfail($id);

        //Eliminamos la Sede
        $objeto->delete();

        //Devolvemos la respuesta
        return response()->json([
            'code'=>200,
            'isSuccess'=>true,
            'message' => 'Registro Eliminado Exitosamente'
        ], Response::HTTP_OK);
    }

    public function cambiarEstado(Request $request)
    {
        //Validación de datos
        $data = $request->only('id');
        $validator = Validator::make($data, [
            'id' => 'required'           ]);

        //Si falla la validación error.
        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], 400);
        }

        //Buscamos el producto
        $objeto = $this->model::findOrfail($request->id);

        if($objeto->estado==1){
            $objeto->estado=2;
            $objeto->save();
        }else{
            $objeto->estado=1;
            $objeto->save();
        }

        //Devolvemos los datos actualizados.
        return response()->json([
            'code'=>200,
            'isSuccess'=>true,
            'message' => 'Estado Actualizado Extiosamente',
        ], Response::HTTP_OK);
    }
    public function activos()
    {
        //Listamos todos los registros activos
       $objeto=$this->model::active();
       if($objeto){
        return response()->json([
            'code'=>200,
            'data' => $objeto
        ], Response::HTTP_OK);
       }else{
        return response()->json([
            'code'=>200,
            'data' => []
        ], Response::HTTP_OK);
       }
    }

    public function getAnimalesCompra()
    {
        //Listamos todos los registros activos
       $objeto=$this->model::getAnimalesCompra();
       if($objeto){
        return response()->json([
            'code'=>200,
            'data' => $objeto
        ], Response::HTTP_OK);
       }else{
        return response()->json([
            'code'=>200,
            'data' => []
        ], Response::HTTP_OK);
       }
    }







}
