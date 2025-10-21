<?php

namespace App\Http\Controllers\Backend\Materiales;

use App\Http\Controllers\Controller;
use App\Models\Color;
use App\Models\Encargado;
use App\Models\Entradas;
use App\Models\EntradasDetalle;
use App\Models\Marca;
use App\Models\Materiales;
use App\Models\Normativa;
use App\Models\Retorno;
use App\Models\Talla;
use App\Models\UnidadMedida;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MaterialesController extends Controller
{

    public function __construct(){
        $this->middleware('auth');
    }

    //*****************  REGISTRO DE MATERIALES   **********************************


    public function indexMateriales(){
        $arrayUnidades = UnidadMedida::orderBy('nombre', 'ASC')->get();
        $arrayMarcas = Marca::orderBy('nombre', 'ASC')->get();
        $arrayNormativa = Normativa::orderBy('nombre', 'ASC')->get();
        $arrayColor = Color::orderBy('nombre', 'ASC')->get();
        $arrayTalla = Talla::orderBy('nombre', 'ASC')->get();

        return view('backend.admin.materiales.vistamateriales', compact('arrayUnidades',
            'arrayMarcas', 'arrayNormativa', 'arrayColor', 'arrayTalla'));
    }

    public function tablaMateriales(){

        $lista = Materiales::orderBy('nombre', 'ASC')->get();

        foreach ($lista as $fila) {

            $infoUnidad = UnidadMedida::where('id', $fila->id_medida)->first();
            $fila->unidadMedida = $infoUnidad->nombre;

            $infoMarca = Marca::where('id', $fila->id_marca)->first();
            $fila->marca = $infoMarca->nombre;

            $infoNormativa = Normativa::where('id', $fila->id_normativa)->first();
            $fila->normativa = $infoNormativa->nombre;


            $talla = "";
            $color = "";
            if($infoColor = Color::where('id', $fila->id_color)->first()){
                $color = $infoColor->nombre;
            }

            if($infoTalla = Talla::where('id', $fila->id_talla)->first()){
                $talla = $infoTalla->nombre;
            }

            $fila->color = $color;
            $fila->talla = $talla;


            if($fila->fecha_cambio != null){
                $fila->fechaFormat = date("d-m-Y", strtotime($fila->fecha_cambio));
            }



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
            'marca' => 'required',
            'normativa' => 'required',
        );

        // codigo, otros, color, talla, fecha

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        $registro = new Materiales();
        $registro->id_medida = $request->unidad;
        $registro->id_marca = $request->marca;
        $registro->id_normativa = $request->normativa;
        $registro->id_color = $request->color;
        $registro->id_talla = $request->talla;
        $registro->nombre = $request->nombre;
        $registro->codigo = $request->codigo;
        $registro->otros = $request->otros;
        $registro->meses_cambio = $request->fecha;

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
            $arrayMarca = Marca::orderBy('nombre', 'ASC')->get();
            $arrayNormativa = Normativa::orderBy('nombre', 'ASC')->get();
            $arrayColor = Color::orderBy('nombre', 'ASC')->get();
            $arrayTalla = Talla::orderBy('nombre', 'ASC')->get();

            return ['success' => 1, 'material' => $lista, 'unidad' => $arrayUnidad,
                'marca' => $arrayMarca, 'normativa' => $arrayNormativa,
                'color' => $arrayColor, 'talla' => $arrayTalla];
        }else{
            return ['success' => 2];
        }
    }

    public function editarMaterial(Request $request){

        $regla = array(
            'nombre' => 'required',
            'unidad' => 'required',
            'marca' => 'required',
            'normativa' => 'required',
        );

        // codigo, COLOR Y TALLA, otros, fecha

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        Materiales::where('id', $request->id)->update([
            'id_medida' => $request->unidad,
            'id_marca' => $request->marca,
            'id_normativa' => $request->normativa,
            'id_color' => $request->color,
            'id_talla' => $request->talla,
            'nombre' => $request->nombre,
            'codigo' => $request->codigo,
            'otros' => $request->otros,
            'meses_cambio' => $request->fecha
        ]);

        return ['success' => 1];
    }




    public function buscadorMaterialCodigoCategoria(Request $request){

        if($request->get('query')){
            $query = $request->get('query');
            $arrayMateriales = Materiales::where('nombre', 'LIKE', "%{$query}%")
                ->orWhere('codigo', 'LIKE', "%{$query}%")
                ->get();

            $output = '<ul class="dropdown-menu" style="display:block; position:relative; overflow: auto; ">';
            $tiene = true;
            foreach($arrayMateriales as $row){


                $nombreCompleto = "(" . $row->codigo . ") " . $row->nombre;

                // si solo hay 1 fila, No mostrara el hr, salto de linea
                if(count($arrayMateriales) == 1){
                    if(!empty($row)){
                        $tiene = false;
                        $output .= '
                 <li class="cursor-pointer" onclick="modificarValor(this)" id="'.$row->codigo.'"><a href="#" style="margin-left: 3px; color: black">'.$nombreCompleto .'</a></li>
                ';
                    }
                }

                else{
                    if(!empty($row)){
                        $tiene = false;
                        $output .= '
                 <li class="cursor-pointer" onclick="modificarValor(this)" id="'.$row->codigo.'"><a href="#" style="margin-left: 3px; color: black">'.$nombreCompleto .'</a></li>
                   <hr>
                ';
                    }
                }
            }
            $output .= '</ul>';
            if($tiene){
                $output = '';
            }
            echo $output;
        }
    }






    //************************************************************


    public function vistaDetalleMaterial($id){
        return view('backend.admin.materiales.detalle.vistadetallematerial', compact('id'));
    }


    public function tablaDetalleMaterial($idmaterial){

        $listado = EntradasDetalle::where('id_material', $idmaterial)
            ->whereColumn('cantidad_entregada', '<', 'cantidad')
            ->get();

        foreach ($listado as $fila) {
            $infoEntrada = Entradas::where('id', $fila->id_entradas)->first();
            $fila->fechaFormat = date("d-m-Y", strtotime($infoEntrada->fecha));

            $fila->descripcion = $infoEntrada->descripcion;
            $fila->lote  = $infoEntrada->lote;

            $fila->cantidadDisponible = ($fila->cantidad - $fila->cantidad_entregada);
        }

        return view('backend.admin.materiales.detalle.tabladetallematerial', compact('listado'));
    }




    //**********************  MOVIMIENTOS  **************************************


    public function vistaMovimientosDetalleMaterial($id){ // id entrada_detalle
        return view('backend.admin.materiales.detalle.movimientos.vistamovimientos', compact('id'));
    }


    public function tablaMovimientosDetalleMaterial($id){

        // id entrada_detalle

        $listado = Retorno::where('id_entrada_detalle', $id)
            ->orderBy('fecha', 'ASC')
            ->get();


        foreach ($listado as $fila) {
            $fila->fechaFormat = date("d-m-Y", strtotime($fila->fecha));

            //0: REINGRESO
            //1: DESCARTADO

            if($infoPersona = Encargado::where('id', $fila->id_encargado)->first()){
                $fila->personaEntrego = $infoPersona->nombre;
            }else{
                $fila->personaEntrego = "";
            }

            $fila->cantidadDisponible = ($fila->cantidad - $fila->cantidad_entregada);
        }

        return view('backend.admin.materiales.detalle.movimientos.tablamovimientos', compact('listado'));
    }


}
