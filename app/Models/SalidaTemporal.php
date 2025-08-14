<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalidaTemporal extends Model
{
    use HasFactory;
    protected $table = 'salida_temporal';
    public $timestamps = false;
}
