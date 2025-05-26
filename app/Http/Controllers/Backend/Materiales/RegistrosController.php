<?php

namespace App\Http\Controllers\backend\materiales;

use App\Http\Controllers\Controller;
use App\Models\Distrito;
use App\Models\Encargado;
use App\Models\Entradas;
use App\Models\EntradasDetalle;
use App\Models\Materiales;
use App\Models\Normativa;
use App\Models\Salidas;
use App\Models\SalidasDetalle;
use App\Models\UnidadMedida;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class RegistrosController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }


    //************** REGISTRO DE INGRESO DE MATERIALES  *****************************

    public function indexRegistroEntrada(){
        return view('backend.admin.registros.entradas.vistaentradaregistro');
    }

    public function buscadorMaterialGlobal(Request $request){

        if($request->get('query')){
            $query = $request->get('query');
            $arrayMateriales = Materiales::where('nombre', 'LIKE', "%{$query}%")
                ->orWhere('codigo', 'LIKE', "%{$query}%")
                ->get();

            $output = '<ul class="dropdown-menu" style="display:block; position:relative; overflow: auto; ">';
            $tiene = true;
            foreach($arrayMateriales as $row){

                $medida = "";
                $code = "";

                if($info = UnidadMedida::where('id', $row->id_medida)->first()){
                    $medida = "- " . $info->nombre;
                }

                if($row->codigo != null){
                    $code = "- " . $row->codigo;
                }

                // si solo hay 1 fila, No mostrara el hr, salto de linea
                if(count($arrayMateriales) == 1){
                    if(!empty($row)){
                        $tiene = false;
                        $output .= '
                 <li class="cursor-pointer" onclick="modificarValor(this)" id="'.$row->id.'"><a href="#" style="margin-left: 3px; color: black">'.$row->nombre . '  ' .$medida . ' ' .$code .'</a></li>
                ';
                    }
                }

                else{
                    if(!empty($row)){
                        $tiene = false;
                        $output .= '
                 <li class="cursor-pointer" onclick="modificarValor(this)" id="'.$row->id.'"><a href="#" style="margin-left: 3px; color: black">'.$row->nombre . ' ' .$medida . ' ' .$code .'</a></li>
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

    // GUARDAR ENTRADAS
    public function guardarEntrada(Request $request){

        $rules = array(
            'fecha' => 'required',
        );

        // descripcion, lote

        $validator = Validator::make($request->all(), $rules);
        if ( $validator->fails()){
            return ['success' => 0];
        }

        DB::beginTransaction();

        try {

            $datosContenedor = json_decode($request->contenedorArray, true);

            $idusuario = Auth::id();

            $registro = new Entradas();
            $registro->id_usuario = $idusuario;
            $registro->fecha = $request->fecha;
            $registro->descripcion = $request->observacion;
            $registro->lote = $request->lote;
            $registro->save();

            // idMaterial    infoCantidad

            // SUMAR CANTIDAD
            foreach ($datosContenedor as $filaArray) {

                $detalle = new EntradasDetalle();
                $detalle->id_entradas = $registro->id;
                $detalle->id_material = $filaArray['idMaterial'];
                $detalle->cantidad = $filaArray['infoCantidad'];
                $detalle->cantidad_entregada = 0;
                $detalle->save();
            }

            // ENTRADA COMPLETADA

            DB::commit();
            return ['success' => 1];

        }catch(\Throwable $e){
            Log::info("error: " . $e);
            DB::rollback();
            return ['success' => 99];
        }
    }








    // ****************************** SALIDAS *****************************************


    public function indexRegistroSalida(){

        $arrayEncargado = Encargado::orderBy('nombre')->get();
        $arrayDistrito = Distrito::orderBy('nombre')->get();

        return view('backend.admin.registros.salidas.vistasalidaregistro', compact('arrayEncargado', 'arrayDistrito'));
    }

    public function buscadorMaterialDisponible(Request $request){

        if($request->get('query')){

            $query = $request->get('query');

            $pilaArrayIdMaterial = array();


            // TODOS LOS ID MATERIALES QUE COINCIDA CON LA BUSQUEDA
            $arrayMateriales = Materiales::where('nombre', 'LIKE', "%{$query}%")
                ->orWhere('codigo', 'LIKE', "%{$query}%")
                ->get();

            foreach ($arrayMateriales as $fila) {
                array_push($pilaArrayIdMaterial, $fila->id);
            }


            // SOLO MATERIAL DISPONIBLE
            $listado = EntradasDetalle::whereIn('id_material', $pilaArrayIdMaterial)
                ->whereColumn('cantidad_entregada', '<', 'cantidad')
                ->orderBy('id') // Ordenar para obtener el primer registro de cada material
                ->get()
                ->unique('id_material') // Filtrar en PHP si la consulta no lo resuelve
                ->values();


            $output = '<ul class="dropdown-menu" style="display:block; position:relative; overflow: auto; max-height: 300px; width: 550px">';
            $tiene = true;
            foreach ($listado as $row) {

                $infoMaterial = Materiales::where('id', $row->id_material)->first();
                $infoMedida = UnidadMedida::where('id', $infoMaterial->id_medida)->first();
                $infoNormativa = Normativa::where('id', $infoMaterial->id_normativa)->first();

                $nombreCompleto = $infoMaterial->nombre . " (" . $infoMedida->nombre . ")" . " (" . $infoNormativa->nombre . ")";

                // si solo hay 1 fila, No mostrara el hr, salto de linea
                if (count($listado) == 1) {
                    if (!empty($row)) {
                        $tiene = false;
                        $output .= '
                 <li class="cursor-pointer" onclick="modificarValor(this)" id="' . $row->id . '">' .$nombreCompleto . '</li>
                ';
                    }
                } else {
                    if (!empty($row)) {
                        $tiene = false;
                        $output .= '
                 <li class="cursor-pointer" onclick="modificarValor(this)" id="' . $row->id . '">' . $nombreCompleto . '</li>
                   <hr>
                ';
                    }
                }
            }
            $output .= '</ul>';
            if ($tiene) {
                $output = '';
            }
            echo $output;
        }
    }


    // UTILIZADO PARA LLENAR EL ARRAY DEL MODAL DE UN MATERIAL
    public function infoBodegaMaterialDetalleFila(Request $request)
    {
        $regla = array(
            'id' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        $infoEntradaDeta = EntradasDetalle::where('id', $request->id)->first();
        $infoMaterial = Materiales::where('id', $infoEntradaDeta->id_material)->first();


        // BUSCAR SOLO DE LAS 'ENTRADAS' DEL PROYECTO
        $pilaArrayIdEntradas = array();
        $arrayEntradas = Entradas::all();
        foreach ($arrayEntradas as $fila) {
            array_push($pilaArrayIdEntradas, $fila->id);
        }

        $listado = EntradasDetalle::whereIn('id_entradas', $pilaArrayIdEntradas)
            ->where('id_material', $infoEntradaDeta->id_material)
            ->whereColumn('cantidad_entregada', '<', 'cantidad')
            ->get();

        foreach ($listado as $fila){
            $infoPadre = Entradas::where('id', $fila->id_entradas)->first();
            $fila->lote = $infoPadre->lote;

            // cantidad actual que hay
            $resta = $fila->cantidad - $fila->cantidad_entregada;
            $fila->cantidadActual = $resta;

            $fecha = date("d-m-Y", strtotime($infoPadre->fecha));
            $fila->fechaIngreso = $fecha;
        }

        $disponible = 0;
        if ($listado->isEmpty()) {
            $disponible = 1;
        }

        return ['success' => 1, 'nombreMaterial' => $infoMaterial->nombre,
            'arrayIngreso' => $listado, 'disponible' => $disponible];
    }





    public function guardarSalidaMateriales(Request  $request)
    {
        Log::info("llegaaaa");
        $regla = array(
            'fecha' => 'required',
            'idencargado' => 'required',
            'iddistrito' => 'required',
        );

        //  descripcion, (infoIdEntradaDeta, infoCantidad, infoRetorno)

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        DB::beginTransaction();

        try {

            $datosContenedor = json_decode($request->contenedorArray, true);

            // EVITAR QUE VENGA VACIO
            if($datosContenedor == null){
                return ['success' => 1];
            }


            $reg = new Salidas();
            $reg->fecha = $request->fecha;
            $reg->id_encargado = $request->idencargado;
            $reg->id_distrito = $request->iddistrito;
            $reg->descripcion = $request->descripcion;
            $reg->save();

            // infoIdEntradaDetalle, filaCantidadSalida
            $filaContada = 0;
            foreach ($datosContenedor as $filaArray) {
                $filaContada++;

                // verificar cantidad que hay en la entrada_detalla
                $infoFilaEntradaDetalle = EntradasDetalle::where('id', $filaArray['infoIdEntradaDeta'])->first();

                // VERIFICACION:NO SUPERAR LA CANTIDAD_ENTREGADA TOTAL DE ESE MATERIAL-LOTE
                // SEA MAYOR A LA CANTIDAD INGRESADA POR EL BODEGUERO DE ESE MATERIAL-LOTE
                $suma1 = $infoFilaEntradaDetalle->cantidad_entregada + $filaArray['infoCantidad'];
                if($suma1 > $infoFilaEntradaDetalle->cantidad){
                    return ['success' => 2, 'fila' => $filaContada];
                }

                // Pasa validaciones

                // GUARDAR SALIDA DETALLE
                $detalle = new SalidasDetalle();
                $detalle->id_salida = $reg->id;
                $detalle->id_entrada_detalle = $infoFilaEntradaDetalle->id;
                $detalle->cantidad_salida = $filaArray['infoCantidad'];
                $detalle->retorno = $filaArray['infoRetorno'];
                $detalle->save();

                // ACTUALIZAR CANTIDADES DE SALIDA
                EntradasDetalle::where('id', $filaArray['infoIdEntradaDeta'])->update([
                    'cantidad_entregada' => ($filaArray['infoCantidad'] + $infoFilaEntradaDetalle->cantidad_entregada)
                ]);
            }

            DB::commit();
            return ['success' => 10];
        }catch(\Throwable $e){
            Log::info('error ' . $e);
            DB::rollback();
            return ['success' => 99];
        }
    }

}
