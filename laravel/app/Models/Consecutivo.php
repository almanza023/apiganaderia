<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Consecutivo extends Model
{
    protected $table = 'consecutivos';
    protected $fillable = ['nombre',  'numero', 'estado'];

     public static  function getConsecutivo($nombre){
         $objeto= self::where('nombre',$nombre)->first();
         if($objeto){
            return $objeto->nombre.$objeto->numero;
         }
         return '';
     }
}
