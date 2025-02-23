<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NichoCobros extends Model
{
    use HasFactory;
    protected $table = 'nicho_cobros';
    public $timestamps = false;

    protected $fillable = [
        'id_nichomunicipal_detalle',
        'fecha_ciclo', // Asegúrate de agregar este campo
    ];
}
