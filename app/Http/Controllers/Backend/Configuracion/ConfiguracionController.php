<?php

namespace App\Http\Controllers\Backend\Configuracion;

use App\Http\Controllers\Controller;
use App\Models\Libros;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ConfiguracionController extends Controller
{
    public function __construct(){
        $this->middleware('auth');
    }


    // **************** LIBROS *********************************
    public function indexLibros(){
        return view('backend.admin.configuracion.libros.vistalibro');
    }

    public function tablaLibros(){

        $listado = Libros::orderBy('nombre', 'ASC')->get();

        foreach ($listado as $item) {
            $item->desdeFormat = date("d-m-Y", strtotime($item->fecha_desde));
            $item->hastaFormat = date("d-m-Y", strtotime($item->fecha_hasta));
        }

        return view('backend.admin.configuracion.libros.tablalibro', compact('listado'));
    }


    public function nuevoLibro(Request $request){

        $regla = array(
            'nombre' => 'required',
            'desde' => 'required',
            'hasta' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        $registro = new Libros();
        $registro->nombre = $request->nombre;
        $registro->fecha_desde = $request->desde;
        $registro->fecha_hasta = $request->hasta;

        if($registro->save()){
            return ['success' => 1];
        }else{
            return ['success' => 2];
        }
    }


    public function informacionLibro(Request $request){

        $regla = array(
            'id' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        if($info = Libros::where('id', $request->id)->first()){

            return ['success' => 1, 'info' => $info];
        }else{
            return ['success' => 2];
        }
    }


    public function editarLibro(Request $request){

        $regla = array(
            'id' => 'required',
            'nombre' => 'required',
            'desde' => 'required',
            'hasta' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        if(Libros::where('id', $request->id)->first()){

            Libros::where('id', $request->id)->update([
                'nombre' => $request->nombre,
                'fecha_desde' => $request->desde,
                'fecha_hasta' => $request->hasta,
            ]);

            return ['success' => 1];
        }else{
            return ['success' => 2];
        }
    }

}
