<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalidaDetalleTemporal extends Model
{
    use HasFactory;
    protected $table = 'salida_detalle_temporal';
    public $timestamps = false;
}
