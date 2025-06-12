<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\Animal;
use App\Models\AnimalCompra;
use App\Models\CajaMenor;
use App\Models\Compra;
use App\Models\DetalleCompra;
use App\Models\MovimientoInventario;
use App\Models\Producto;
use App\Models\ProductoBodega;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use JWTAuth;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;

class AnimalCompraController extends Controller
{
    protected $user;
    protected $model;

    public function __construct(Request $request)
    {
        $this->model = AnimalCompra::class;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Listamos todas las compras
        $objeto = $this->model::getAll();
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
        $data = $request->only( 'numero', 'fechaCompra',
        'vendedor');
        $validator = Validator::make($data, [
            'numero' => 'required',
            'fechaCompra' => 'required',
            'vendedor' => 'required',
        ]);

        // Si falla la validación
        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], 400);
        }

        // Creamos la compra en la BD
        $objeto = $this->model::updateOrCreate(
            ['numero' => $request->numero],
            [
                'fechaCompra' => $request->fechaCompra,
                'vendedor' => strtoupper($request->vendedor),
                'estado' => 0
            ]
        );

        // Respuesta en caso de que todo vaya bien.
        return response()->json([
            'code' => 200,
            'isSuccess' => true,
            'message' => 'Compra Creada Exitosamente',
            'data'=>$objeto
        ], Response::HTTP_OK);
    }

    public function storeDetalles(Request $request)
    {
        // Validamos los datos
        $data = $request->only( 'compra_id', 'valor', 'numero',
     'total', 'peso', 'animal');
        $validator = Validator::make($data, [
            'compra_id' => 'required',
            'valor' => 'required',
            'numero' => 'required',
            'total' => 'required',
            'peso' => 'required',
        ]);

        // Si falla la validación
        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], 400);
        }
        try {
            DB::transaction(function () use ($request) {
                $animal = Animal::create([
                    'nombre' => strtoupper($request->animal["nombre"]),
                    'codigo' => $request->numero,
                    'numero' => $request->animal["numero"],
                    'sexo' => ($request->animal["sexo"]),
                    'etapa' => ($request->animal["etapa"]),
                    'fechaNacimiento' => ($request->animal["fechaNacimiento"]),
                    'peso' => ($request->animal["peso"]),
                    'observaciones' => '',
                ]);

                DetalleCompra::create([
                    'animal_id' => $animal->id,
                    'compra_id' => $request->compra_id,
                    'valor' => $request->valor,
                    'numero' => $request->numero,
                    'total' => $request->total,
                    'peso' => $request->peso
                ]);


            });
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'isSuccess' => false,
                'message' => 'Error al procesar la transacción: ' . $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        $detalles=DetalleCompra::getDetalleByCompra($request->compra_id);
            // Respuesta en caso de que todo vaya bien.
            return response()->json([
                'code' => 200,
                'isSuccess' => true,
                'message' => 'Animal Agregado Exitosamente',
                'data'=>$detalles
            ], Response::HTTP_OK);
    }

    public function show($id)
    {
        // Buscamos la compra
        $objeto = $this->model::find($id);
        $data=[];

        // Si la compra no existe devolvemos error no encontrado
        if (!$objeto) {

            return response()->json([
                'code' => 200,
                'isSuccess' => false,
                'message' => 'Registro no encontrado en la base de datos.'
            ], 404);
        }

        $detalles=DetalleCompra::getDetalleByCompra($objeto->id);
        $data=[
            'compra'=>$objeto,
            'detalles'=>$detalles
        ];

        return response()->json([
            'code' => 200,
            'isSuccess' => true,
            'data' => $data
        ], Response::HTTP_OK);
    }

    public function showByNumero(Request $request)
    {
        // Buscamos la compra
        $objeto = $this->model::where('numero',$request->numero)->first();
        $data=[];

        // Si la compra no existe devolvemos error no encontrado
        if (!$objeto) {

            return response()->json([
                'code' => 200,
                'isSuccess' => false,
                'message' => 'Registro no encontrado en la base de datos.'
            ], 404);
        }

        $detalles=DetalleCompra::getDetalleByCompra($objeto->id);
        $data=[
            'compra'=>$objeto,
            'detalles'=>$detalles
        ];

        return response()->json([
            'code' => 200,
            'isSuccess' => true,
            'data' => $data
        ], Response::HTTP_OK);
    }

    public function update(Request $request, $id)
    {
        // Validación de datos
        $data = $request->only('total', 'numero');
        $validator = Validator::make($data, [
            'total' => 'required',
            'numero' => 'required',
        ]);

        // Si falla la validación error.
        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], 400);
        }

        // Buscamos la compra
        $objeto = $this->model::findOrFail($id);
        if($objeto){
            $objeto->estado=1;
            $objeto->total=$request->total;
            $objeto->save();
            // Devolvemos los datos actualizados.
            return response()->json([
                'code' => 200,
                'isSuccess' => true,
                'message' => 'Compra Finalizada Exitosamente',
            ], Response::HTTP_OK);


        }else{
            return response()->json([
                'code' => 200,
                'isSuccess' => false,
                'message' => 'No se encontro el número de la Compra ',
            ], Response::HTTP_OK);
        }



    }


    public function destroy($id)
    {
        // Buscamos la mesa
        $objeto = $this->model::findOrFail($id);

        // Eliminamos la mesa
        $objeto->delete();

        // Devolvemos la respuesta
        return response()->json([
            'code' => 200,
            'isSuccess' => true,
            'message' => 'Compra Eliminada Exitosamente'
        ], Response::HTTP_OK);
    }

    public function destroyDetalle($id)
    {
        // Buscamos el detalle
        $objeto = DetalleCompra::findOrFail($id);

        // Eliminamos el detalle
        $objeto->delete();

        // Devolvemos la respuesta
        return response()->json([
            'code' => 200,
            'isSuccess' => true,
            'message' => 'Producto Eliminado Exitosamente'
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
        }

        // Buscamos la mesa
        $objeto = $this->model::findOrFail($request->id);

        // Cambiamos el estado
        $objeto->estado = 2;
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

    public function filter(Request $request)
    {
        // Listamos todas las compras
        $fechaInicio=Carbon::parse($request->fecha_inicio)->format('Y-m-d');
        $fechaFin=Carbon::parse($request->fecha_fin)->format('Y-m-d');


        $objeto = $this->model::consultarFechas($fechaInicio,
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

    public function getCodigo()
    {
        // Listamos todos los registros activos
        $objeto = $this->model::getCodigo();
        if ($objeto) {
            return response()->json([
                'code' => 200,
                'data' => $objeto
            ], Response::HTTP_OK);
        } else {
            return response()->json([
                'code' => 200,
                'data' => ''
            ], Response::HTTP_OK);
        }
    }

}
