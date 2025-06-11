<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Potrero extends Model
{
    protected $table = 'potreros';
    protected $fillable = ['nombre', 'area', 'cercas', 'maleza', 'observaciones',  'estado'];

    public static function search($search)
     {
         return empty($search) ? static::query()
             : static::query()->where('id', 'like', '%'.$search.'%')
                 ->orWhere('nombre', 'like', '%'.$search.'%')
                 ->orWhere('descripcion', 'like', '%'.$search.'%')
                 ->orWhere('nit', 'like', '%'.$search.'%');
    }

     public static function getActive(){
        return Potrero::where('estado',1)->get();
    }

    public static function get(){
        return self::orderBy('id', 'asc')->get();
    }
}
