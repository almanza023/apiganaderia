<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;

class ProductoController extends Controller
{
    protected $model;

    public function __construct()
    {
        $this->model = Producto::class;
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
        $data = $request->only('id_tipo_producto', 'id_tipo_unidad', 'nombre', 'descripcion', 'contenido', 'lote',
    'fecha_vencimiento' );
        $validator = Validator::make($data, [
            'id_tipo_producto' => 'required|exists:tipo_productos,id',
            'id_tipo_unidad' => 'required|exists:tipo_unidades,id',
            'nombre' => 'required|max:200|string'
        ]);

        // Si falla la validación
        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], 400);
        }
        $producto=$this->model::create([
            'id_tipo_producto'=>$request->id_tipo_producto,
            'id_tipo_unidad'=>$request->id_tipo_unidad,
            'nombre'=>strtoupper($request->nombre),
            'descripcion'=>$request->descripcion,
            'contenido'=>$request->contenido,
            'lote'=>$request->lote,
            'fecha_vencimiento'=>$request->fecha_vencimiento
        ]);

        // Respuesta en caso de que todo vaya bien

            return response()->json([
                'code' => 200,
                'isSuccess' => true,
                'message' => 'Producto Creado Exitosamente',
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
        $producto = $this->model::find($id);

        // Si el producto no existe devolvemos error no encontrado
        if (!$producto) {
            return response()->json([
                'code' => 404,
                'isSuccess' => false,
                'message' => 'Producto no encontrado'
            ], 404);
        }

        return response()->json([
            'code' => 200,
            'isSuccess' => true,
            'data' => $producto
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
        $data = $request->only('id_tipo_producto', 'id_tipo_unidad', 'nombre', 'descripcion', 'contenido', 'lote',
        'fecha_vencimiento' );
            $validator = Validator::make($data, [
                'id_tipo_producto' => 'required|exists:tipo_productos,id',
                'id_tipo_unidad' => 'required|exists:tipo_unidad,id',
                'nombre' => 'required|max:200|string'
            ]);
        // Buscamos el producto
        $producto = $this->model::findOrFail($id);

                $producto->update([
                    'id_tipo_producto'=>$request->id_tipo_producto,
                    'id_tipo_unidad'=>$request->id_tipo_unidad,
                    'nombre'=>strtoupper($request->nombre),
                    'descripcion'=>$request->descripcion,
                    'contenido'=>$request->contenido,
                    'lote'=>$request->lote,
                    'fecha_vencimiento'=>$request->fecha_vencimiento
                ]);


            return response()->json([
                'code' => 200,
                'isSuccess' => true,
                'message' => 'Producto Actualizado Exitosamente',
            ], Response::HTTP_OK);
        }

    public function destroy($id)
    {
        // Buscamos el producto
        $producto = $this->model::findOrFail($id);
        // Eliminamos el producto
        $producto->delete();
        // Devolvemos la respuesta
        return response()->json([
            'code' => 200,
            'isSuccess' => true,
            'message' => 'Producto Eliminado Exitosamente'
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
            'message' => 'Estado del Producto Actualizado Exitosamente',
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





}
