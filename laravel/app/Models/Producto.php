<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;


class Producto extends Model
{
    protected $table = 'productos';

    protected $fillable = [
        'id_tipo_producto',
        'id_tipo_unidad',
        'nombre',
        'descripcion',
        'contenido',
        'lote',
        'fecha_vencimiento',
        'estado'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function tipoProducto()
    {
        return $this->belongsTo(TipoProducto::class, 'id_tipo_producto');
    }

    public function tipoUnidad()
    {
        return $this->belongsTo(TipoUnidad::class, foreignKey: 'id_tipo_unidad');
    }

    public static function getAll(){
        return self::with('tipoProducto', 'tipoUnidad')->orderByDesc('id')->get();
    }

    public static function getActive(){
        return self::with('tipoProducto', 'tipoUnidad')
        ->where('estado', 1)->orderByDesc('id')->get();
    }










}
