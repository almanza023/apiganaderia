<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class AnimalCompra extends Model
{

    protected $table = 'animales_compras';

    protected $fillable = ['numero', 'fechaCompra',
     'total', 'vendedor', 'ubicacion', 'tipo',   'estado'];



    /**
     * Get all of the comments for the AnimalCompra
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function detalles()
    {
        return $this->hasMany(DetalleCompra::class, 'compra_id', 'id')->with('animal');
    }

    public static function search($fecha1, $fecha2)
     {
         return empty($fecha1) && empty($fecha2)  ? static::query()
             : static::query()->whereBetween('fechaCompra', [$fecha2, $fecha1]);
    }

    public static function getCompra($id){
        $obj=AnimalCompra::where('numero',$id)->first();
        if(!empty($obj)){
            return $obj;
        }
        return '';
    }



    public static function totalPagos(){
        $total = DB::table('animales_compras')
            ->where('estado', 1)
            ->sum('total');
        return $total;
    }

    public static function consultarFechas($fecha1, $fecha2){
        return AnimalCompra::where('fechaCompra','>=', $fecha1)
        ->where('fechaCompra','<=', $fecha2)->orderBy('fechaCompra', 'asc')->get();
    }

    public static function getCodigo(){
        $obj=AnimalCompra::latest('id')->where('estado',1)->first();
        if(!empty($obj)){
            return 'FC'.($obj->id+1);
        }
        return '';
    }

    public static function getAll(){
        return self::get();
    }

   public static function getFilter($fechaInicio, $fechaFinal){
        return self::where('fechaCompra', '>=', $fechaInicio)
            ->where('fechaCompra', '<=', $fechaFinal)
            ->orderBy('id', 'desc')
            ->get();
    }






}
