<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\Proveedor;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;

class ProveedorController extends Controller
{
    protected $model;

    public function __construct()
    {
        $this->model = Proveedor::class;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Listamos todos los proveedores
        $proveedores = $this->model::get();
        return response()->json([
            'code' => 200,
            'isSuccess' => true,
            'data' => $proveedores
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
        $data = $request->only('nombre', 'nombre_visitador', 'telefono');
        $validator = Validator::make($data, [
            'nombre' => 'required|max:200|string',
            'nombre_visitador' => 'required',
        ]);

        // Si falla la validación
        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], 400);
        }

        // Creamos el proveedor en la BD
        $proveedor = $this->model::create([
            'nombre' => strtoupper($request->nombre),
            'nombre_visitador' => strtoupper($request->nombre_visitador),
            'telefono' => $request->telefono,
        ]);

        // Respuesta en caso de que todo vaya bien
        return response()->json([
            'code' => 200,
            'isSuccess' => true,
            'message' => 'Distribuidor Creado Exitosamente',
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
        // Buscamos el proveedor
        $proveedor = $this->model::find($id);

        // Si el proveedor no existe devolvemos error no encontrado
        if (!$proveedor) {
            return response()->json([
                'code' => 404,
                'isSuccess' => false,
                'message' => 'Distribuidor no encontrado'
            ], 404);
        }

        return response()->json([
            'code' => 200,
            'isSuccess' => true,
            'data' => $proveedor
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
        // Validación de datos
        $data = $request->only('nombre', 'nombre_visitador', 'telefono');
        $validator = Validator::make($data, [
            'nombre' => 'required|max:200|string',
            'nombre_visitador' => 'required',
        ]);

        // Si falla la validación error.
        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], 400);
        }

        // Buscamos el proveedor
        $proveedor = $this->model::findOrFail($id);

        // Actualizamos el proveedor.
        $proveedor->update([
            'nombre' => strtoupper($request->nombre),
            'nombre_visitador' => strtoupper($request->nombre_visitador),
            'telefono' => $request->telefono,
        ]);

        // Respuesta
        return response()->json([
            'code' => 200,
            'isSuccess' => true,
            'message' => 'Distribuidor Actualizado Exitosamente',
        ], Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        // Buscamos el proveedor
        $proveedor = $this->model::findOrFail($id);

        // Eliminamos el proveedor
        $proveedor->delete();

        // Devolvemos la respuesta
        return response()->json([
            'code' => 200,
            'isSuccess' => true,
            'message' => 'Distribuidor Eliminado Exitosamente'
        ], Response::HTTP_OK);
    }

    /**
     * Cambiar el estado de un proveedor (Activo/Inactivo)
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

        // Buscamos el proveedor
        $proveedor = $this->model::findOrFail($request->id);

        // Cambiamos el estado
        $proveedor->estado = ($proveedor->estado == 1) ? 2 : 1;
        $proveedor->save();

        // Devolvemos la respuesta
        return response()->json([
            'code' => 200,
            'isSuccess' => true,
            'message' => 'Estado del Distribuidor Actualizado Exitosamente',
        ], Response::HTTP_OK);
    }

    /**
     * Listar todos los proveedores activos.
     *
     * @return \Illuminate\Http\Response
     */
    public function activos()
    {
        // Listamos todos los registros activos
        $proveedores = $this->model::where('estado', 1)->get();
        if ($proveedores) {
            return response()->json([
                'code' => 200,
                'data' => $proveedores
            ], Response::HTTP_OK);
        } else {
            return response()->json([
                'code' => 200,
                'data' => []
            ], Response::HTTP_OK);
        }
    }
}
