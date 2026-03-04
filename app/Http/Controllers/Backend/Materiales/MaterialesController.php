<?php

namespace App\Http\Controllers\Backend\Materiales;

use App\Http\Controllers\Controller;
use App\Models\Color;
use App\Models\Entradas;
use App\Models\EntradasDetalle;
use App\Models\Marca;
use App\Models\Materiales;
use App\Models\Normativa;
use App\Models\Retorno;
use App\Models\Talla;
use App\Models\UnidadMedida;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
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

    public function tablaMateriales()
    {
        $lista = DB::table('materiales as m')
            ->leftJoin('unidad_medida as um', 'um.id', '=', 'm.id_medida')
            ->leftJoin('marca as ma', 'ma.id', '=', 'm.id_marca')
            ->leftJoin('normativa as no', 'no.id', '=', 'm.id_normativa')
            ->leftJoin('color as co', 'co.id', '=', 'm.id_color')
            ->leftJoin('talla as ta', 'ta.id', '=', 'm.id_talla')

            ->select(
                'm.*',
                'um.nombre as unidadMedida',
                'ma.nombre as marca',
                'no.nombre as normativa',
                'co.nombre as color',
                'ta.nombre as talla',

                // 🔹 Total ingresado (subconsulta)
                DB::raw('(SELECT COALESCE(SUM(cantidad_inicial),0)
                      FROM entradas_detalle
                      WHERE id_material = m.id) as total_ingresado'),

                // 🔹 Total salido (subconsulta)
                DB::raw('(SELECT COALESCE(SUM(sd.cantidad_salida),0)
                      FROM salidas_detalle sd
                      INNER JOIN entradas_detalle ed
                          ON ed.id = sd.id_entrada_detalle
                      WHERE ed.id_material = m.id) as total_salido'),

                // 🔹 Stock real
                DB::raw('(
                (SELECT COALESCE(SUM(cantidad_inicial),0)
                 FROM entradas_detalle
                 WHERE id_material = m.id)
                -
                (SELECT COALESCE(SUM(sd.cantidad_salida),0)
                 FROM salidas_detalle sd
                 INNER JOIN entradas_detalle ed
                     ON ed.id = sd.id_entrada_detalle
                 WHERE ed.id_material = m.id)
            ) as cantidadGlobal')
            )

            ->get();

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
            'meses_cambio' => $request->fecha,
        ]);

        return ['success' => 1];
    }




    public function buscadorMaterialCodigoCategoria(Request $request)
    {
        if (!$request->get('query')) {
            return;
        }

        $query = $request->get('query');

        $materiales = DB::table('materiales as m')
            ->leftJoin('entradas_detalle as ed', 'ed.id_material', '=', 'm.id')
            ->leftJoin('salidas_detalle as sd', 'sd.id_entrada_detalle', '=', 'ed.id')

            ->where(function($q) use ($query) {
                $q->where('m.nombre', 'LIKE', "%{$query}%")
                    ->orWhere('m.codigo', 'LIKE', "%{$query}%");
            })

            ->select(
                'm.id',
                'm.codigo',
                'm.nombre',

                // Total ingresado
                DB::raw('COALESCE(SUM(ed.cantidad_inicial),0) as total_ingresado'),

                // Total salido
                DB::raw('COALESCE(SUM(sd.cantidad_salida),0) as total_salido'),

                // Existencia real
                DB::raw('(
                COALESCE(SUM(ed.cantidad_inicial),0)
                -
                COALESCE(SUM(sd.cantidad_salida),0)
            ) as existencia')
            )

            ->groupBy('m.id', 'm.codigo', 'm.nombre')

            // Solo mostrar si hay stock
            ->havingRaw('existencia > 0')

            ->orderBy('m.nombre')

            ->get();

        if ($materiales->isEmpty()) {
            return;
        }

        $output = '<ul class="dropdown-menu"
                style="display:block; position:relative; overflow:auto; max-height:300px;">';

        foreach ($materiales as $index => $row) {

            $existencia = (int) $row->existencia;

            $nombreCompleto = "({$row->codigo}) {$row->nombre}";

            $badge = "
            <span style='
                background:#198754;
                color:#fff;
                border-radius:4px;
                padding:2px 6px;
                font-size:11px;
                margin-left:10px;
            '>
                Disp: {$existencia}
            </span>
        ";

            $output .= '
            <li class="cursor-pointer"
                onclick="modificarValor(this)"
                id="' . $row->codigo . '"
                style="padding:6px 10px;">
                <div style="display:flex; justify-content:space-between; align-items:center;">
                    <span>' . $nombreCompleto . '</span>
                    ' . $badge . '
                </div>
            </li>
        ';

            if ($index !== $materiales->count() - 1) {
                $output .= '<hr style="margin:2px 0;">';
            }
        }

        $output .= '</ul>';

        echo $output;
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
