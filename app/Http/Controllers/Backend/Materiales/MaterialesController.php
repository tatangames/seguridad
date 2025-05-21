<?php

namespace App\Http\Controllers\Backend\Materiales;

use App\Http\Controllers\Controller;
use App\Models\Materiales;
use App\Models\UnidadMedida;
use Illuminate\Http\Request;

class MaterialesController extends Controller
{

    public function __construct(){
        $this->middleware('auth');
    }



    //*****************  REGISTRO DE MATERIALES   **********************************


    public function indexMateriales(){
        $arrayUnidades = UnidadMedida::orderBy('nombre', 'ASC')->get();

        return view('backend.admin.materiales.vistamateriales', compact('arrayUnidades'));
    }

    public function tablaMateriales(){

        $lista = Materiales::orderBy('nombre', 'ASC')->get();

        foreach ($lista as $fila) {

            $infoUnidad = UnidadMedida::where('id', $fila->id_medida)->first();
            $fila->medida = $infoUnidad->nombre;

            // CANTIDAD GLOBAL QUE TENGO DE ESE PRODUCTO
            $totalCantidadMate = EntradasDetalle::where('id_material', $fila->id)->sum('cantidad');
            $totalCantidadEntregada = EntradasDetalle::where('id_material', $fila->id)->sum('cantidad_entregada');

            $fila->cantidadGlobal = ($totalCantidadMate - $totalCantidadEntregada);
        }

        return view('backend.admin.materiales.tablamateriales', compact('lista'));
    }

    public function nuevoMaterial(Request $request){

        $regla = array(
            'nombre' => 'required',
            'unidad' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        $registro = new Materiales();
        $registro->id_medida = $request->unidad;
        $registro->nombre = $request->nombre;
        $registro->codigo = $request->codigo;

        if($registro->save()){
            return ['success' => 1];
        }else{
            return ['success' => 2];
        }
    }

    public function informacionMaterial(Request $request){
        $regla = array(
            'id' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        if($lista = Materiales::where('id', $request->id)->first()){

            $arrayUnidad = UnidadMedida::orderBy('nombre', 'ASC')->get();

            return ['success' => 1, 'material' => $lista, 'unidad' => $arrayUnidad];
        }else{
            return ['success' => 2];
        }
    }

    public function editarMaterial(Request $request){

        $regla = array(
            'nombre' => 'required',
            'unidad' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        Materiales::where('id', $request->id)->update([
            'id_medida' => $request->unidad,
            'nombre' => $request->nombre,
            'codigo' => $request->codigo
        ]);

        return ['success' => 1];
    }

}
