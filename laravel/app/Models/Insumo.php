<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Insumo extends Model
{
    protected $table = 'insumos';

    protected $fillable = [
        'id_producto',
        'fecha',
        'cantidad',
        'total_contenido',
        'contenido_aplicado',
        'contenido_restante',
        'precio',
        'total',
        'destino',
        'estado',

    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'id_producto');
    }

    public static function getAll(){
        return self::with(['producto.tipoUnidad'])->orderByDesc('id')->get();
    }

    public static function getActive(){
        return self::with('producto')
        ->where('estado', 1)
        ->orderByDesc('id')->get();
    }


    public static function getFilter($fechaInicio, $fechaFinal){
        return self::with('producto')
        ->where('fecha', '>=', $fechaInicio)
            ->where('fecha', '<=', $fechaFinal)
        ->orderByDesc('id')->get();
    }

public static function getInsumosPorDestinoAnimal(){
    return self::select('insumos.id', 'productos.nombre as nombre_producto', 'insumos.contenido_restante')
        ->join('productos', 'productos.id', '=', 'insumos.id_producto')
        ->where('insumos.destino', 'A')
        ->where('insumos.estado', 1)
        ->orderByDesc('insumos.id')
        ->get();
}

public static function getHistorialAplicaciones($insumoId) {
    return DB::table('aplicaciones_insumos')
        ->select('aplicaciones_insumos.id', 'aplicaciones_insumos.fecha', 'aplicaciones_insumos.cantidad_aplicada',
                'aplicaciones_insumos.observaciones', 'animales.nombre as animal')
        ->join('animales', 'animales.id', '=', 'aplicaciones_insumos.animal_id')
        ->where('aplicaciones_insumos.insumo_id', $insumoId)
        ->orderBy('aplicaciones_insumos.fecha', 'desc')
        ->get();
}

// Obtener el total gastado por un animal específico
public static function getTotalGastadoPorAnimal($animalId) {
    // Suma la cantidad aplicada y el equivalente en dinero (cantidad_aplicada * precio unitario del insumo)
    return DB::table('aplicaciones_insumos')
        ->join('insumos', 'aplicaciones_insumos.insumo_id', '=', 'insumos.id')
        ->selectRaw('SUM(aplicaciones_insumos.cantidad_aplicada) as total_cantidad, (SUM(aplicaciones_insumos.cantidad_aplicada) * (insumos.total / insumos.total_contenido)) as total_dinero')
        ->where('aplicaciones_insumos.animal_id', $animalId)
        ->first();
}

public static function getTotalGastadoPorAnimalDetallado($animalId) {
    // Suma la cantidad aplicada y el equivalente en dinero (cantidad_aplicada * precio unitario del insumo)
    return DB::table('aplicaciones_insumos')
        ->join('insumos', 'aplicaciones_insumos.insumo_id', '=', 'insumos.id')
        ->join('productos', 'insumos.id_producto', '=', 'productos.id')
        ->join('tipo_unidades', 'productos.id_tipo_unidad', '=', 'tipo_unidades.id')
        ->selectRaw('productos.nombre as nombre_producto, tipo_unidades.nombre as nombre_tipo_unidad, aplicaciones_insumos.cantidad_aplicada as total_cantidad, (aplicaciones_insumos.cantidad_aplicada * (insumos.total / insumos.total_contenido)) as total_dinero')
        ->where('aplicaciones_insumos.animal_id', $animalId)
        ->get();
}












}
