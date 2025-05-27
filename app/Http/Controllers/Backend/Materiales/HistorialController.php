<?php

namespace App\Http\Controllers\backend\materiales;

use App\Http\Controllers\Controller;
use App\Models\Distrito;
use App\Models\Encargado;
use App\Models\Salidas;
use Illuminate\Http\Request;

class HistorialController extends Controller
{
    public function __construct(){
        $this->middleware('auth');
    }

    //**************** HISTORIAL DE SALIDAS ************************


    public function indexHistorialSalidas()
    {
        return view('backend.admin.historial.salidas.vistasalidabodega');
    }

    public function tablaHistorialSalidas()
    {
        // viene id proyecto

        $listado = Salidas::orderBy('fecha', 'desc')->get();

        foreach ($listado as $fila) {
            $fila->fechaFormat = date("d-m-Y", strtotime($fila->fecha));

            $infoRecibe = Encargado::where('id', $fila->id_encargado)->first();
            $fila->nombreRecibe = $infoRecibe->nombre;

            $infoDistrito = Distrito::where('id', $fila->id_distrito)->first();
            $fila->nombreDistrito = $infoDistrito->nombre;



        }

        return view('backend.admin.historial.salidas.tablasalidabodega', compact('listado'));
    }





}
