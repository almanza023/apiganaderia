<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\Animal;
use App\Models\AnimalVenta;
use App\Models\AperturaCaja;
use App\Models\Consecutivo;
use App\Models\DetalleVenta;
use App\Models\Empresa;
use App\Models\MovimientoInventario;
use App\Models\ProductoBodega;
use App\Models\Venta;
use App\Models\VentaTipoPago;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use JWTAuth;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;

class AnimalVentaController extends Controller
{
    protected $user;
    protected $model;

    public function __construct(Request $request)
    {
        $this->model = AnimalVenta::class;
    }

    public function index()
    {
        // Listamos todas las mesas
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
        $data = $request->only( 'fecha', 'codigo', 'comprador', 'documento',
     'telefono', 'tipo');
        $validator = Validator::make($data, [
            'fecha' => 'required',
            'codigo' => 'required',
            'comprador' => 'required',
            'documento' => 'required',
        ]);

        // Si falla la validación
        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], 400);
        }

        // Creamos la mesa en la BD
        $objeto = $this->model::create([
            'fecha' => $request->fecha,
            'codigo' => $request->codigo,
            'comprador' => strtoupper($request->comprador),
            'documento' => $request->documento,
            'telefono' => $request->telefono,
            'estado' => 0,
        ]);

        //Actualizar Consecutivo
        $consecutivo = Consecutivo::where('nombre', 'FV')->first();
        if ($consecutivo) {
            $consecutivo->numero += 1;
            $consecutivo->save();
        }

        // Respuesta en caso de que todo vaya bien.
        return response()->json([
            'code' => 200,
            'isSuccess' => true,
            'message' => 'Venta Creada Exitosamente',
            'data'=>$objeto
        ], Response::HTTP_OK);
    }

    public function storeDetalles(Request $request)
    {
        // Validamos los datos
        $data = $request->only( 'venta_id', 'valor',
     'valorkg', 'peso', 'edad', 'animal');
        $validator = Validator::make($data, [
            'venta_id' => 'required',
            'valor' => 'required',
            'valorkg' => 'required',
            'peso' => 'required',
            'edad' => 'required',
        ]);

        // Validamos que el animal no este en un detalle
        $detalle= DetalleVenta::where('animal_id', $request->animal["id"])
            ->first();
        if($detalle){
            return response()->json([
                'code' => 200,
                'isSuccess' => false,
                'message' => 'El animal ya se encuentra en la venta',
            ], Response::HTTP_OK);
        }

        // Si falla la validación
        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], 400);
        }

            DetalleVenta::create([
                'animal_id' => $request->animal["id"],
                'venta_id' => $request->venta_id,
                'valor' => $request->valor,
                'valorkg' => $request->valorkg,
                'peso' => $request->peso,
                'edad' => $request->edad,
                'estado' => 1
            ]);
            $data=DetalleVenta::getDetalleByVenta($request->venta_id);
        // Respuesta en caso de que todo vaya bien.
        return response()->json([
            'code' => 200,
            'isSuccess' => true,
            'message' => 'Animal Agregado Exitosamente',
            'data'=>$data
        ], Response::HTTP_OK);
    }


    public function show($id)
    {
        // Buscamos la venta
        $objeto = $this->model::find($id);
        $data=[];

        // Si la venta no existe devolvemos error no encontrado
        if (!$objeto) {

            return response()->json([
                'code' => 200,
                'isSuccess' => false,
                'message' => 'Registro no encontrado en la base de datos.'
            ], 404);
        }
        $detalles=DetalleVenta::getDetalleByVenta($objeto->id);
        $data=[
            'venta'=>$objeto,
            'detalles'=>$detalles,
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
        $objeto = $this->model::where('codigo',$request->numero)->first();
        $data=[];

        // Si la compra no existe devolvemos error no encontrado
        if (!$objeto) {

            return response()->json([
                'code' => 200,
                'isSuccess' => false,
                'message' => 'Registro no encontrado en la base de datos.'
            ], 404);
        }

        $detalles=DetalleVenta::getDetalleByVenta($objeto->id);
        $data=[
            'venta'=>$objeto,
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
        $data = $request->only('total');
        $validator = Validator::make($data, [
            'total' => 'required',
        ]);

        // Si falla la validación error.
        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], 400);
        }
        $objeto = $this->model::findOrFail($id);
        if($objeto){
            $objeto->estado=1;
            $objeto->total=$request->total;
            $objeto->save();

            $detalles=DetalleVenta::getDetalleByVenta($objeto->id);
            foreach ($detalles as $item) {
                $animal=Animal::find($item->animal_id);
                $animal->estado=2;
                $animal->save();
            }

                return response()->json([
                    'code' => 200,
                    'isSuccess' => true,
                    'message' => 'Venta Finalizada'
                ], Response::HTTP_OK);

        }else{
            return response()->json([
                'code' => 200,
                'isSuccess' => false,
                'message' => 'No se encontro el número de Venta',
            ], Response::HTTP_OK);
        }
    }


    public function destroy($id)
    {
        // Buscamos la venta
        $objeto = $this->model::findOrFail($id);

        // Eliminamos la venta
        $objeto->delete();

        // Devolvemos la respuesta
        return response()->json([
            'code' => 200,
            'isSuccess' => true,
            'message' => 'Venta Eliminada Exitosamente'
        ], Response::HTTP_OK);
    }

    public function cambiarEstado(Request $request)
    {
        // Validación de datos
        $data = $request->only('id', 'observaciones');
        $validator = Validator::make($data, [
            'id' => 'required',
        ]);

        // Si falla la validación error.
        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], 400);
        }
        $objeto = $this->model::findOrFail($request->id);
        if($objeto->estado==1){
            $objeto->estado = 2;
            $objeto->save();
            return response()->json([
                'code' => 200,
                'isSuccess' => true,
                'message' => 'Venta N° ' . $objeto->id . ' ANULADA Exitosamente',
            ], Response::HTTP_OK);
        }else{
            return response()->json([
                'code' => 200,
                'isSuccess' => false,
                'message' => 'No se puede Anular la Venta',
            ], Response::HTTP_OK);
        }



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
        $objeto = Consecutivo::getConsecutivo('FV');
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

    public function getInsumosPorAnimal($id)
    {
        // Listamos todos los registros activos
        $objeto = $this->model::getInsumosPorAnimal($id);
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
                'message'=>'No se encontrarón registros',
                'data' => []
            ], Response::HTTP_OK);
        }
    }

     public function destroyDetalle($id)
    {
        // Buscamos la venta
        $objeto = DetalleVenta::findOrFail($id);
        // Eliminamos la venta
        $objeto->delete();
        // Devolvemos la respuesta
        return response()->json([
            'code' => 200,
            'isSuccess' => true,
            'message' => 'Animal Eliminado Exitosamente de la Venta'
        ], Response::HTTP_OK);
    }

    public function reporteConsolidadoGeneral(Request $request)
    {
        // Listamos todas las compras
        $fechaInicio=Carbon::parse($request->fecha_inicio)->format('Y-m-d');
        $fechaFin=Carbon::parse($request->fecha_fin)->format('Y-m-d');


        $objeto = DetalleVenta::getDetalleByVentaByFecha($fechaInicio,
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

    public function reporteAnimal(Request $request)
    {
        // Listamos todas las compras
        $animalId=$request->animal_id;
        $objeto = DetalleVenta::getDetalleByVentaByAnimal($animalId);
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
                'message'=>'No se encontrarón registros',
                'data' => []
            ], Response::HTTP_OK);
        }
    }



}
