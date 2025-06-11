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
        return self::with('producto')->orderByDesc('id')->get();
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











}
