<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\AperturaCaja;
use App\Models\CajaMenor;
use App\Models\Gasto;
use App\Models\Pedido;
use App\Models\Potrero;
use App\Models\Producto;
use App\Models\Venta;
use Illuminate\Http\Request;
use JWTAuth;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;

class PotreroController extends Controller
{
    protected $user;
    protected $model;

    public function __construct(Request $request)
    {
        $this->model = Potrero::class;
    }

    public function index()
    {
        // Listamos todas las potreros
        $objeto = $this->model::get();
        if ($objeto) {
            return response()->json([
                'code' => 200,
                'isSuccess' => true,
                'data' => $objeto
            ], Response::HTTP_OK);
        } else {
            return response()->json([
                'code' => 200,
                'isSuccess' => false,
                'data' => []
            ], Response::HTTP_OK);
        }
    }

    public function store(Request $request)
    {
        // Validamos los datos
        $data = $request->only('nombre', 'area', 'cercas', 'maleza');
        $validator = Validator::make($data, [
            'nombre' => 'required',
            'area' => 'required',
            'cercas' => 'required',
            'maleza' => 'required',
        ]);

        // Si falla la validación
        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], 400);
        }

        // Creamos la potrero en la BD
        $objeto = $this->model::create([
            'nombre'=>strtoupper($request->nombre),
            'area'=>$request->area,
            'cercas'=>$request->cercas,
            'maleza'=>$request->maleza,
        ]);

        // Respuesta en caso de que todo vaya bien.
        return response()->json([
            'code' => 200,
            'isSuccess' => true,
            'message' => 'Potrero creado exitosamente',
        ], Response::HTTP_OK);
    }

    public function show($id)
    {
        // Buscamos la mesa
        $objeto = $this->model::find($id);

        // Si la mesa no existe devolvemos error no encontrado
        if (!$objeto) {
            return response()->json([
                'code' => 200,
                'isSuccess' => false,
                'message' => 'Registro no encontrado en la base de datos.'
            ], 404);
        }

        return response()->json([
            'code' => 200,
            'isSuccess' => true,
            'data' => $objeto
        ], Response::HTTP_OK);
    }

    public function update(Request $request, $id)
    {
        // Validación de datos
        $data = $request->only('nombre', 'area', 'cercas', 'maleza');
        $validator = Validator::make($data, [
            'nombre' => 'required',
            'area' => 'required',
            'cercas' => 'required',
            'maleza' => 'required',
        ]);

        // Si falla la validación error.
        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], 400);
        }

        // Buscamos la potrero
        $objeto = $this->model::findOrFail($id);

        // Actualizamos la potrero
        $objeto->update([
            'nombre'=>strtoupper($request->nombre),
            'area'=>$request->area,
            'cercas'=>$request->cercas,
            'maleza'=>$request->maleza,
        ]);
       // Respuesta en caso de que todo vaya bien.
       return response()->json([
        'code' => 200,
        'isSuccess' => true,
        'message' => 'Potrero Actualizado exitosamente',
    ], Response::HTTP_OK);
    }

    public function destroy($id)
    {
        // Buscamos la potrero
        $objeto = $this->model::findOrFail($id);

        // Eliminamos la potrero
        $objeto->delete();

        // Devolvemos la respuesta
        return response()->json([
            'code' => 200,
            'isSuccess' => true,
            'message' => 'Potrero Eliminada Exitosamente'
        ], Response::HTTP_OK);
    }

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
        }        // Buscamos la potrero
        $objeto = $this->model::findOrFail($request->id);
        // Cambiamos el estado
        $objeto->estado = ($objeto->estado == 1) ? 2 : 1;
        $objeto->save();

        // Devolvemos la respuesta
        return response()->json([
            'code' => 200,
            'isSuccess' => true,
            'message' => 'Estado Actualizado Exitosamente',
        ], Response::HTTP_OK);
    }

    public function activos()
    {
        // Listamos todos los registros activos
        $objeto = $this->model::where('estado', 1)->get();
        if ($objeto) {
            return response()->json([
                'code' => 200,
                'data' => $objeto
            ], Response::HTTP_OK);
        } else {
            return response()->json([
                'code' => 200,
                'data' => []
            ], Response::HTTP_OK);
        }
    }






}
