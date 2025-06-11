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
                'a.id as animal_id',
                'a.nombre as animal_nombre',
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

}
