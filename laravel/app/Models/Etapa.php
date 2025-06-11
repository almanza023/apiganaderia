<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Etapa extends Model
{
    protected $table = 'etapas';
    protected $fillable = ['nombre',  'estado'];



     public static  function getActive(){
         return Etapa::where('estado',1)->get();
     }
}
