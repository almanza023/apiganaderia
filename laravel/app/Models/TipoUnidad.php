<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TipoUnidad extends Model
{
    protected $table = 'tipo_unidades';
    protected $fillable = ['nombre',  'descripcion'];



     public static  function getActive(){
         return self::where('estado',1)->get();
     }

     public static function get(){
        return self::orderBy('id', 'desc')->get();
     }
}
