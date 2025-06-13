<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class DetalleVenta extends Model
{
    //

    protected $table = 'detalles_ventas';

    protected $fillable = ['animal_id', 'venta_id', 'valor',
     'valorkg', 'peso', 'edad','estado'];

     public function animal()
     {
         return $this->belongsTo('App\Models\Animal', 'animal_id', 'id');
     }

     public static function getTotal($id){
         $suma=DetalleVenta::where('venta_id', $id)->where('estado',1)->sum('valor');
         if($suma>0){
             return $suma;
         }else{
             return 0;
         }

     }

     public static function getDetalleByVenta($id){
        return DB::table('detalles_ventas as dv')
            ->join('animales as a', 'a.id', '=', 'dv.animal_id')
            ->join('detalles_compras as dc', 'dc.animal_id', '=', 'a.id')
            ->select(
                'dv.id',
                'a.id as animal_id',
                'a.nombre as animal_nombre',
                'a.numero as animal_numero',
                'dc.peso as peso_compra',
                'dv.peso as peso_venta',
                DB::raw('(dv.peso - dc.peso) as peso_dif'),
                DB::raw('(dc.valor / dc.peso) as valor_kg_compra'),
                'dv.valorkg as valor_kg_venta',
                DB::raw('(dv.valorkg - (dc.valor / dc.peso)) as valor_kg_dif'),
                'dv.valor as subtotal'
            )
            ->where('dv.venta_id', $id)
            ->where('dv.estado', 1)
            ->get();

    }

    public static function getDetalleByVentaByFecha($fechainicio, $fechafin){
        $items= DB::table('detalles_ventas as dv')
            ->join('animales as a', 'a.id', '=', 'dv.animal_id')
            ->join('animales_ventas as av', 'av.id', '=', 'dv.venta_id')
            ->join('detalles_compras as dc', 'dc.animal_id', '=', 'a.id')
            ->join('animales_compras as ac', 'ac.id', '=', 'dc.compra_id')
            ->select(
                'dv.id',
                'a.id as animal_id',
                'av.comprador',
                'av.total as total_venta',
                'ac.total as total_compra',
                'ac.vendedor',
                'a.id as animal_id',
                'a.nombre as animal_nombre',
                'a.numero as animal_numero',
                'dc.peso as peso_compra',
                'dv.peso as peso_venta',
                DB::raw('(dv.peso - dc.peso) as peso_dif'),
                'dv.valorkg as valor_kg_venta',
                'dc.valor as valor_kg_compra',
                DB::raw('(dv.valorkg - dc.valor) as valor_kg_dif'),
                'dv.valor as subtotal'
            )
            ->whereBetween('av.fecha', [$fechainicio, $fechafin])
            ->where('dv.estado', 1)
            ->get();
        foreach ($items as $item) {
            $insumos=Insumo::getTotalGastadoPorAnimal($item->animal_id);
            if($insumos){
                $item->total_insumos = $insumos->total_dinero;
            }else{
                $item->total_insumos =0;
            }
            $item->utilidad =$item->total_venta - ($item->total_compra + $item->total_insumos );

        }
        return $items;

    }

    public static function getDetalleByVentaByAnimal($animal_id){
        $items= DB::table('detalles_ventas as dv')
            ->join('animales as a', 'a.id', '=', 'dv.animal_id')
            ->join('animales_ventas as av', 'av.id', '=', 'dv.venta_id')
            ->join('detalles_compras as dc', 'dc.animal_id', '=', 'a.id')
            ->join('animales_compras as ac', 'ac.id', '=', 'dc.compra_id')
            ->select(
                'dv.id',
                'a.id as animal_id',
                'av.comprador',
                'av.total as total_venta',
                'ac.total as total_compra',
                'ac.vendedor',
                'a.id as animal_id',
                'a.nombre as animal_nombre',
                'a.numero as animal_numero',
                'dc.peso as peso_compra',
                'dv.peso as peso_venta',
                DB::raw('(dv.peso - dc.peso) as peso_dif'),
                'dv.valorkg as valor_kg_venta',
                'dc.valor as valor_kg_compra',
                DB::raw('(dv.valorkg - dc.valor) as valor_kg_dif'),
                'dv.valor as subtotal'
            )
            ->where('dv.animal_id', $animal_id)
            ->where('dv.estado', 1)
            ->get();
        foreach ($items as $item) {
            $item->total_insumos =0;
            $item->insumos =[];
            $insumos=Insumo::getTotalGastadoPorAnimalDetallado($item->animal_id);
            if($insumos->count() > 0){
                $item->insumos = $insumos;
                foreach ($insumos as $insumo) {
                    $item->total_insumos += $insumo->total_dinero;
                }

            }
            $item->utilidad =$item->total_venta - ($item->total_compra + $item->total_insumos );

        }
        return $items;

    }

}
