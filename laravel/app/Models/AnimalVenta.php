<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class AnimalVenta extends Model
{
    //

    protected $table = 'animales_ventas';

    protected $fillable = ['fecha', 'codigo', 'comprador', 'documento',
     'total', 'telefono', 'tipo',   'estado'];

     public static function search($fecha1, $fecha2)
     {
         return empty($fecha1) && empty($fecha2)  ? static::query()
             : static::query()->whereBetween('fecha', [$fecha2, $fecha1]);
    }

    public static function detalles($id)
    {
        return DetalleVenta::where('venta_id', $id)->where('estado', 1)->get();
    }

    public static function getActive(){
        return AnimalVenta::where('estado',1)->get();
    }

    public static function totalPagos(){
        $total = DB::table('animales_ventas')
            ->where('estado', 1)
            ->sum('total');
        return $total;
    }

    public static function getCodigo(){
        $obj=AnimalVenta::latest('id')->where('estado',1)->first();
        if(!empty($obj)){
            return 'FV'.($obj->id+1);
        }
        return '';
    }

    public static function buscar($codigo){
        return AnimalVenta::where('codigo',$codigo)->orWhere('id', $codigo)->first();
    }

    public static function consultarFechas($fecha1, $fecha2){

        return AnimalVenta::where('fecha','>=', $fecha1)
        ->where('fecha','<=', $fecha2)->orderBy('fecha', 'asc')->get();

    }

    public static function getUtilidades($fecha1, $fecha2){

        return DB::table('animales_ventas as av')
        ->join('detalles_ventas as dv', 'av.id', '=', 'dv.venta_id')
        ->join('animales as a', 'a.id', '=', 'dv.animal_id')
        ->join('detalles_compras as dc', 'dc.animal_id', '=', 'a.id')
        ->join('animales_compras as ac', 'dc.compra_id', '=', 'ac.id')
        ->select('a.nombre', 'ac.fechaCompra', 'av.fecha', 'dc.peso', 'dc.valor as valorcompra', 'dc.total', 'dv.peso as pesoventa', 'dv.valorkg', 'dv.valor')
        ->where('av.fecha','>=', $fecha1)
        ->where('av.fecha','<=', $fecha2)->orderBy('av.fecha', 'asc')->get();

    }

    public static function getAll(){
        return self::get();
    }

public static function getInsumosPorAnimal($animalId)
{
    return DB::table('aplicaciones_insumos as ai')
        ->join('insumos as i', 'i.id', '=', 'ai.insumo_id')
        ->join('productos as p', 'p.id', '=', 'i.id_producto')
        ->where('ai.animal_id', $animalId)
        ->where('ai.estado', 1)
        ->select(
            'p.nombre as insumo',
            'i.id as insumo_id',
            DB::raw('SUM(ai.cantidad_aplicada) as total_aplicado'),
            DB::raw('(SUM(ai.cantidad_aplicada) / i.total_contenido) * 100 as porcentaje_contenido'),
            DB::raw('(SUM(ai.cantidad_aplicada) * i.total / i.total_contenido) as costo_aplicado')
        )
        ->groupBy('i.id', 'p.nombre', 'i.total_contenido', 'i.total')
        ->get();
}








}
