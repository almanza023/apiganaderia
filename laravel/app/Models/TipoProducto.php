<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TipoProducto extends Model
{
    protected $table = 'tipo_productos';
    protected $fillable = ['nombre',  'descripcion'];

    public static  function getActive(){
         return self::where('estado',1)->get();
     }

     public static function get(){
        return self::orderBy('id', 'desc')->get();
     }
}
