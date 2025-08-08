<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UnidadEmpleado extends Model
{
    use HasFactory;

    protected $table = 'unidad_empleado';
    public $timestamps = false;
}
