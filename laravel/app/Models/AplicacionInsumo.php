<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;


class AplicacionInsumo extends Model
{
    protected $table = 'productos';

    protected $fillable = [
        'animal_id',
        'insumo_id',
        'cantidad_aplicada',
        'fecha',
        'observaciones',
        'estado'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function animal()
    {
        return $this->belongsTo(Animal::class, 'animal_id');
    }

    public function insumo()
    {
        return $this->belongsTo(insumo::class, foreignKey: 'insumo_id');
    }










}
