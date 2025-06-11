<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetalleCompra extends Model
{
    //

    protected $table = 'detalles_compras';

    protected $fillable = ['animal_id', 'compra_id', 'valor', 'numero',
     'total', 'peso', 'estado'];

     public function animal()
     {
         return $this->belongsTo('App\Models\Animal', 'animal_id', 'id');
     }
     public function compra()
     {
         return $this->belongsTo('App\Models\AnimalCompra', 'compra_id', 'id');
     }

     public static function getTotal($id){
         $suma=DetalleCompra::where('compra_id', $id)->where('estado',1)->sum('valor');
         if($suma>0){
             return $suma;
         }else{
             return 0;
         }

     }

     public static function getTotalParcial($id){
        $suma=DetalleCompra::where('numero', $id)->sum('total');
        if($suma>0){
            return $suma;
        }else{
            return 0;
        }

    }

     public static function getDetalles($id){
        return DetalleCompra::where('numero', $id)->get();

    }

    public static function getDetalleByCompra($id){
        return DetalleCompra::where('compra_id', $id)
        ->with('animal')->get();
    }

}
