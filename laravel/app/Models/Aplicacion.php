<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;


class Aplicacion extends Model
{
    protected $table = 'aplicaciones_insumos';

    protected $fillable = [
        'insumo_id',
        'animal_id',
        'fecha',
        'cantidad_aplicada',
        'observaciones',
        'estado'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function insumo()
    {
        return $this->belongsTo(Insumo::class, 'insumo_id');
    }

    public function animal()
    {
        return $this->belongsTo(Animal::class, foreignKey: 'animal_id');
    }

    public static function getAll(){
        return self::with(['insumo.producto', 'animal'])->orderByDesc('id')->get();
    }

    public static function getActive(){
        return self::with('insumo', 'animal')
        ->where('estado', 1)->orderByDesc('id')->get();
    }










}
