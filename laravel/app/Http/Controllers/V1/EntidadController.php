<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\Entidad;
use Illuminate\Http\Request;
use JWTAuth;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;

class EntidadController extends Controller
{
    protected $model;

    public function __construct(Request $request)
    {
        $this->model=Entidad::class;
    }

    public function index()
    {
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
    public function store(Request $request)
    {
        $data = $request->only('nombre', 'codigo', 'nit',
    'resolucion', 'documentos','limite', 'fechaActivacion', 'fechaCorte');
        $validator = Validator::make($data, [
            'nombre' => 'required|max:200|string',
            'codigo' => 'required|max:200|string',
            'nit' => 'required|max:200|string',
            'resolucion' => 'required|max:200|string',
            'documentos' => 'required|max:200|string',
            'limite' => 'required|max:200|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], 400);
        }

        $objeto = $this->model::create([
            'nombre'=>strtoupper($request->nombre),
            'codigo'=>($request->codigo),
            'nit'=>($request->nit),
            'resolucion'=>($request->resolucion),
            'documentos'=>($request->documentos),
            'limite'=>($request->limite),
            'fechaActivacion'=>($request->fechaActivacion),
            'fechaCorte'=>($request->fechaCorte),
        ]);

        return response()->json([
            'code'=>200,
            'isSuccess'=>true,
            'message' => 'Entidad Creada Exitosamente',
        ], Response::HTTP_OK);
    }

    public function show($id)
    {
        $objeto = $this->model::find($id);

        if (!$objeto) {
            return response()->json([
                'code'=>200,
                'isSuccess'=>true,
                'message' => 'Registro no encontrado en la base de datos.'
            ], 404);
        }
        return response()->json([
            'code'=>200,
            'isSuccess'=>false,
            'data' => $objeto
        ], Response::HTTP_OK);
    }

    public function update(Request $request, $id)
    {
        $data = $request->only('nombre', 'codigo', 'nit',
    'resolucion', 'documentos','limite', 'fechaActivacion', 'fechaCorte');
        $validator = Validator::make($data, [
            'nombre' => 'required|max:200|string',
            'codigo' => 'required|max:200|string',
            'nit' => 'required|max:200|string',
            'resolucion' => 'required|max:200|string',
            'documentos' => 'required|max:200|string',
            'limite' => 'required|max:200|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], 400);
        }

        $objeto = $this->model::findOrfail($id);

        $objeto->update([
            'nombre'=>strtoupper($request->nombre),
            'codigo'=>($request->codigo),
            'nit'=>($request->nit),
            'resolucion'=>($request->resolucion),
            'documentos'=>($request->documentos),
            'limite'=>($request->limite),
            'fechaActivacion'=>($request->fechaActivacion),
            'fechaCorte'=>($request->fechaCorte),
        ]);

        return response()->json([
            'code'=>200,
            'isSuccess'=>true,
            'message' => 'Entidad Actualizada Exitosamente',
        ], Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $objeto = $this->model::findOrfail($id);

        $objeto->delete();

        return response()->json([
            'code'=>200,
            'isSuccess'=>true,
            'message' => 'Registro Eliminado Exitosamente'
        ], Response::HTTP_OK);
    }

    public function cambiarEstado(Request $request)
    {
        $data = $request->only('id');
        $validator = Validator::make($data, [
            'id' => 'required'           ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], 400);
        }

        $objeto = $this->model::findOrfail($request->id);

        if($objeto->estado==1){
            $objeto->estado=2;
            $objeto->save();
        }else{
            $objeto->estado=1;
            $objeto->save();
        }

        return response()->json([
            'code'=>200,
            'isSuccess'=>true,
            'message' => 'Estado Actualizado Extiosamente',
        ], Response::HTTP_OK);
    }
    public function activos()
    {
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



}
