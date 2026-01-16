<?php

namespace App\Http\Controllers\backend\materiales;

use App\Http\Controllers\Controller;
use App\Models\Cargo;
use App\Models\Color;
use App\Models\Distrito;
use App\Models\Empleado;
use App\Models\Entradas;
use App\Models\EntradasDetalle;
use App\Models\JefeInmediato;
use App\Models\Marca;
use App\Models\Materiales;
use App\Models\Normativa;
use App\Models\Proveedor;
use App\Models\Retorno;
use App\Models\SalidaDetalleTemporal;
use App\Models\Salidas;
use App\Models\SalidasDetalle;
use App\Models\SalidaTemporal;
use App\Models\Talla;
use App\Models\UnidadEmpleado;
use App\Models\UnidadMedida;
use Carbon\Carbon;
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

        $arrayProveedor = Proveedor::orderBy('nombre', 'ASC')->get();

        return view('backend.admin.registros.entradas.vistaentradaregistro', compact('arrayProveedor'));
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
                $marca = "";
                $normativa = "";
                $color = "";
                $talla = "";

                if($info = UnidadMedida::where('id', $row->id_medida)->first()){
                    $medida = "(" . $info->nombre . ")";
                }

                if($info = Marca::where('id', $row->id_marca)->first()){
                    $marca = "(" . $info->nombre . ")";
                }

                if($info = Normativa::where('id', $row->id_normativa)->first()){
                    $normativa = "(" . $info->nombre . ")";
                }

                if($info = Color::where('id', $row->id_color)->first()){
                    $color = "(" . $info->nombre . ")";
                }

                if($info = Talla::where('id', $row->id_talla)->first()){
                    $talla = "(" . $info->nombre . ")";
                }



                $nombreCompleto = $row->nombre . '  ' .$medida . '  ' .$marca . '  ' .$normativa . '  ' .$color . '  ' .$talla;


                // si solo hay 1 fila, No mostrara el hr, salto de linea
                if(count($arrayMateriales) == 1){
                    if(!empty($row)){
                        $tiene = false;
                        $output .= '
                 <li class="cursor-pointer" onclick="modificarValor(this)" id="'.$row->id.'"><a href="#" style="margin-left: 3px; color: black">'.$nombreCompleto .'</a></li>
                ';
                    }
                }

                else{
                    if(!empty($row)){
                        $tiene = false;
                        $output .= '
                 <li class="cursor-pointer" onclick="modificarValor(this)" id="'.$row->id.'"><a href="#" style="margin-left: 3px; color: black">'.$nombreCompleto .'</a></li>
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
            'proveedor' => 'required',
        );

        // descripcion, lote

        // idMaterial, infoCantidad, infoPrecio

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
            $registro->id_proveedor = $request->proveedor;
            $registro->save();

            // idMaterial    infoCantidad

            // SUMAR CANTIDAD
            foreach ($datosContenedor as $filaArray) {

                $detalle = new EntradasDetalle();
                $detalle->id_entradas = $registro->id;
                $detalle->id_material = $filaArray['idMaterial'];
                $detalle->cantidad = $filaArray['infoCantidad']; // ESTO PODRA MODIFICARSE POR DESCARTADO
                $detalle->cantidad_entregada = 0;
                $detalle->cantidad_inicial = $filaArray['infoCantidad'];
                $detalle->precio = $filaArray['infoPrecio'];
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

        $arrayDistritos = Distrito::orderBy('nombre', 'ASC')->get();

        return view('backend.admin.registros.salidas.vistasalidaregistro', compact('arrayDistritos'));
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


            $output = '<ul class="dropdown-menu" style="display:block; position:relative; overflow: auto; max-height: 300px; width: 800px">';
            $tiene = true;
            foreach ($listado as $row) {

                $infoMaterial = Materiales::where('id', $row->id_material)->first();
                $infoMarca = Marca::where('id', $infoMaterial->id_marca)->first();
                $infoMedida = UnidadMedida::where('id', $infoMaterial->id_medida)->first();
                $infoNormativa = Normativa::where('id', $infoMaterial->id_normativa)->first();


                $color = "";
                $talla = "";
                if($info = Color::where('id', $infoMaterial->id_color)->first()){
                    $color = $info->nombre;
                }
                if($info = Talla::where('id', $infoMaterial->id_talla)->first()){
                    $talla = $info->nombre;
                }

                $nombreCompleto = $infoMaterial->nombre . " (" . $infoMedida->nombre . ")" .
                    " (" . $infoMarca->nombre . ")" . " (" . $infoNormativa->nombre . ")" .
                    " (" . $color . ")" . " (" . $talla . ")"
                ;

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

        $infoMedida = UnidadMedida::where('id', $infoMaterial->id_medida)->first();
        $infoMarca = Marca::where('id', $infoMaterial->id_marca)->first();
        $infoNormativa = Normativa::where('id', $infoMaterial->id_normativa)->first();

        $color = "";
        $talla = "";
        if($info = Color::where('id', $infoMaterial->id_color)->first()){
            $color = $info->nombre;
        }

        if($info = Talla::where('id', $infoMaterial->id_talla)->first()){
            $talla = $info->nombre;
        }


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

            $proveedor = "";
            if($infoProveedor = Proveedor::where('id', $infoPadre->id_proveedor)->first()){
                $proveedor = $infoProveedor->nombre;
            }
            $fila->proveedor = $proveedor;

            // cantidad actual que hay
            $resta = $fila->cantidad - $fila->cantidad_entregada;
            $fila->cantidadActual = $resta;

            $fecha = date("d-m-Y", strtotime($infoPadre->fecha));
            $fila->fechaIngreso = $fecha;

            $precioFormat = "$" . number_format($fila->precio, 2, '.', ',');
            $fila->precioFormat = $precioFormat;


            // Meses reemplazo
            $fila->mesesreemplazo = $infoMaterial->meses_cambio;
        }

        $disponible = 0;
        if ($listado->isEmpty()) {
            $disponible = 1;
        }

        return ['success' => 1, 'nombreMaterial' => $infoMaterial->nombre,
            'nombreMarca' => $infoMarca->nombre,
            'nombreNormativa' => $infoNormativa->nombre,
            'nombreMedida' => $infoMedida->nombre,
            'nombreColor' => $color,
            'nombreTalla' => $talla,

            'arrayIngreso' => $listado,
            'disponible' => $disponible
        ];
    }





    public function guardarSalidaMateriales(Request  $request)
    {
        $regla = array(
            'fecha' => 'required',
            'empleado' => 'required',
        );

        //  descripcion, (infoIdEntradaDeta, infoCantidad, infoReemplazo, infoRecomendacion, infoMesReemplazo)

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        DB::beginTransaction();

        try {

            $infoEmpleado = Empleado::where('id', $request->empleado)->first();
            $infoUnidad = UnidadEmpleado::where('id', $infoEmpleado->id_unidad_empleado)->first();

            $infoCargo = Cargo::where('id', $infoEmpleado->id_cargo)->first();
            $cargo = $infoCargo->nombre;

            $jefeInmediato = "";

            // JEFE INMEDIATO
            if($unid = UnidadEmpleado::where('id_empleado', $infoEmpleado->id)->first()){ // es el mismo empleado
                $infoEm = Empleado::where('id', $unid->id_empleado_inmediato)->first();
                $jefeInmediato = $infoEm->nombre;

            }else{
                // JEFE DE LA UNIDAD
                if($infoJefe = Empleado::where('id_unidad_empleado', $infoEmpleado->id_unidad_empleado)
                    ->where('jefe', 1)->first()){
                    $jefeInmediato = $infoJefe->nombre;
                }
            }


            $datosContenedor = json_decode($request->contenedorArray, true);

            // EVITAR QUE VENGA VACIO
            if($datosContenedor == null){
                return ['success' => 1];
            }

            $reg = new Salidas();
            $reg->fecha = $request->fecha;
            $reg->id_empleado = $request->empleado;
            $reg->descripcion = $request->descripcion;

            $reg->area = $infoUnidad->nombre;
            $reg->cargo = $cargo;
            $reg->colaborador = $infoEmpleado->nombre;
            $reg->jefe_inmediato = $jefeInmediato;


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
                $detalle->tipo_regresa = 0;
                $detalle->reemplazo = $filaArray['infoReemplazo'];
                $detalle->recomendacion = $filaArray['infoRecomendacion'];
                $detalle->mes_reemplazo = $filaArray['infoMesReemplazo'];
                $detalle->completado = 0;
                $detalle->save();

                // ACTUALIZAR CANTIDADES DE SALIDA
                EntradasDetalle::where('id', $filaArray['infoIdEntradaDeta'])->update([
                    'cantidad_entregada' => ($filaArray['infoCantidad'] + $infoFilaEntradaDetalle->cantidad_entregada)
                ]);
            }

            DB::commit();
            return ['success' => 10, 'idsalida' => $reg->id];
        }catch(\Throwable $e){
            Log::info('error ' . $e);
            DB::rollback();
            return ['success' => 99];
        }
    }


    //********** PARA PDF SALIDA TEMPORAL *************

    public function guardarSalidaMaterialesTemporal(Request  $request)
    {
        $regla = array(
            'fecha' => 'required',
            'empleado' => 'required',
        );

        //  descripcion, (infoIdEntradaDeta, infoCantidad, infoReemplazo, infoRecomendacion)

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        DB::beginTransaction();

        try {

            $datosContenedor = json_decode($request->contenedorArray, true);

            // EVITAR QUE VENGA VACIO
            if($datosContenedor == null){
                return ['success' => 1];
            }

            // GUARDAR SIEMPRE EN LA MISMA POR SER TEMPORAL


                SalidaTemporal::where('id', 1)->update([
                    'fecha' => $request->fecha,
                    'id_empleado' => $request->empleado,
                    'descripcion' => $request->descripcion
                ]);


            DB::table('salida_detalle_temporal')->delete();

            foreach ($datosContenedor as $filaArray) {

                $infoFilaEntradaDetalle = EntradasDetalle::where('id', $filaArray['infoIdEntradaDeta'])->first();

                // GUARDAR SALIDA DETALLE
                $detalle = new SalidaDetalleTemporal();
                $detalle->id_salida = 1;
                $detalle->id_entrada_detalle = $infoFilaEntradaDetalle->id;
                $detalle->cantidad_salida = $filaArray['infoCantidad'];
                $detalle->reemplazo = $filaArray['infoReemplazo'];
                $detalle->recomendacion = $filaArray['infoRecomendacion'];
                $detalle->save();
            }

            DB::commit();
            return ['success' => 10];
        }catch(\Throwable $e){
            Log::info('error ' . $e);
            DB::rollback();
            return ['success' => 99];
        }
    }


    // **** GENERAR PDF TEMPORAL ****

    public function generarPdfTemporal(){

        $infoSalida = SalidaTemporal::where('id', 1)->first();

        $infoEmpleado = Empleado::where('id', $infoSalida->id_empleado)->first();
        $infoUnidad = UnidadEmpleado::where('id', $infoEmpleado->id_unidad_empleado)->first();

        $jefeInmediato = "";

        // JEFE INMEDIATO
        if($unid = UnidadEmpleado::where('id_empleado', $infoEmpleado->id)->first()){ // es el mismo empleado
            $infoEm = Empleado::where('id', $unid->id_empleado_inmediato)->first();
            $jefeInmediato = $infoEm->nombre;

        }else{
            // JEFE DE LA UNIDAD
            if($infoJefe = Empleado::where('id_unidad_empleado', $infoEmpleado->id_unidad_empleado)
                ->where('jefe', 1)->first()){
                $jefeInmediato = $infoJefe->nombre;
            }
        }


        $infoCargo = Cargo::where('id', $infoEmpleado->id_cargo)->first();
        $cargo = $infoCargo->nombre;

        $fechaFormat = date("d-m-Y", strtotime($infoSalida->fecha));

        $arraySalidasDetalle = SalidaDetalleTemporal::where('id_salida', 1)->get();

        $totalColumna = 0;

        foreach ($arraySalidasDetalle as $item) {

            $infoEntradaDetalle = EntradasDetalle::where('id', $item->id_entrada_detalle)->first();
            $infoMaterial = Materiales::where('id', $infoEntradaDetalle->id_material)->first();


            // precio unitario
            $item->precioFormat = "$" . number_format($infoEntradaDetalle->precio, 2, '.', ',');

            $multiplicado = $item->cantidad_salida * $infoEntradaDetalle->precio;
            $totalColumna += $multiplicado;
            $item->multiplicado = "$" . number_format($multiplicado, 2, '.', ',');

            $item->nombreMaterial = $infoMaterial->nombre;

            $textoRecomendacion = "NO";
            $textoReemplazo = "NO";
            if($item->reemplazo == 1){
                $textoReemplazo = "SI";
            }
            if($item->recomendacion == 1){
                $textoRecomendacion = "SI";
            }

            $item->recomendacion = $textoRecomendacion;
            $item->reemplazo = $textoReemplazo;
        }

        $totalColumnaValor = "$" . number_format($totalColumna, 2, '.', ',');


        // $mpdf = new \Mpdf\Mpdf(['format' => 'LETTER']);
        $mpdf = new \Mpdf\Mpdf(['tempDir' => sys_get_temp_dir(), 'format' => 'LETTER']);

        $mpdf->SetTitle('Reporte Temporal');

        // mostrar errores
        $mpdf->showImageErrors = false;

        $logoalcaldia = 'images/gobiernologo.jpg';
        $logosantaana = 'images/logo.png';


        $tabla = "
            <table style='width: 100%; border-collapse: collapse;'>
                <tr>
                    <!-- Logo izquierdo -->
                    <td style='width: 15%; text-align: left;'>
                        <img src='$logosantaana' alt='Santa Ana Norte' style='max-width: 100px; height: auto;'>
                    </td>
                    <!-- Texto centrado -->
                    <td style='width: 60%; text-align: center;'>
                        <h1 style='font-size: 15px; margin: 0; color: #003366; text-transform: uppercase;'>
                        ALCALDÍA MUNICIPAL DE SANTA ANA NORTE</h1>
                        <h1 style='font-size: 15px; margin: 0; color: #003366; text-transform: uppercase;'>UNIDAD DE SEGURIDAD Y SALUD OCUPACIONAL.</h1>
                        <h2 style='font-size: 13px; margin: 0; color: #003366; text-transform: uppercase;'></h2>
                    </td>
                    <!-- Logo derecho -->
                    <td style='width: 10%; text-align: right;'>
                        <img src='$logoalcaldia' alt='Gobierno de El Salvador' style='max-width: 60px; height: auto;'>
                    </td>
                </tr>
            </table>
            <hr style='border: none; border-top: 2px solid #003366; margin: 0;'>
            ";

        $tabla .= "

            <div style='text-align: center; margin-top: 25px;'>
                <h1 style='font-size: 12px; margin: 0; color: #000;'>Ficha de entrega de Equipo de Protección Personal (E.P.P.)</h1>
            </div>

             <div style='text-align: left; margin-top: 10px;'>
                <p style='font-size: 10px; margin: 0; color: #000;'>Fecha: <strong>$fechaFormat</strong>.</p>
            </div>

            <div style='text-align: left; margin-top: 10px;'>
                <p style='font-size: 10px; margin: 0; color: #000;'>Área: <strong>$infoUnidad->nombre</strong>; Cargo: <strong>$cargo</strong></p>
            </div>
             <div style='text-align: left; margin-top: 10px;'>
                <p style='font-size: 10px; margin: 0; color: #000;'>Se entrega al colaborador(a); <strong>$infoEmpleado->nombre</strong></p>
            </div>


            <div style='text-align: left; margin-top: 10px;'>
                <p style='font-size: 10px; margin: 0; color: #000;'>Jefe inmediato: <strong>$jefeInmediato</strong></p>
            </div>


             <div style='text-align: left; margin-top: 10px;'>
                <p style='font-size: 10px; margin: 0; color: #000;'>Por medio de la presente hace constar el detalle siguiente:</p>
            </div>
      ";


        $tabla  .= "<table width='100%' id='tablaFor' style='margin-top:15px; border-collapse:collapse'>";
        $tabla .= "
            <thead>
                <tr>
                    <th style='text-align:center; font-size:8px; width:10%;  font-weight:bold; border:1px solid #000' rowspan='2'>CANTIDAD</th>
                    <th style='text-align:center; font-size:8px; width:25%;  font-weight:bold; border:1px solid #000' rowspan='2'>DESCRIPCION DE E.P.P.</th>
                    <th style='text-align:center; font-size:8px; width:11%;  font-weight:bold; border:1px solid #000' colspan='2'>REEMPLAZO</th>
                    <th style='text-align:center; font-size:8px; width:11%;  font-weight:bold; border:1px solid #000' rowspan='2'>VALOR</th>
                    <th style='text-align:center; font-size:8px; width:11%;  font-weight:bold; border:1px solid #000' rowspan='2'>VALOR TOTAL</th>
                    <th style='text-align:center; font-size:8px; width:18%;  font-weight:bold; border:1px solid #000' rowspan='2'>
                        RECOMENDACIONES SOBRE EL USO Y MANTENIMIENTO DEL E.P.P. OTORGADO
                    </th>
                </tr>
                <tr>
                    <th style='text-align:center; font-size:8px; font-weight:bold; border:1px solid #000; width:5.5%'>SI</th>
                    <th style='text-align:center; font-size:8px; font-weight:bold; border:1px solid #000; width:5.5%'>NO</th>
                </tr>
            </thead>
            <tbody>";
        foreach ($arraySalidasDetalle as $fila) {
            $esSi = (is_numeric($fila->reemplazo) ? (intval($fila->reemplazo) === 1) : (strtoupper(trim($fila->reemplazo)) === 'SI'));
            $si = $esSi ? 'X' : '';
            $no = $esSi ? '' : 'X';

            $tabla .= "
    <tr>
        <td style='font-size:10px; border:1px solid #000; text-align:center'>{$fila->cantidad_salida}</td>
        <td style='font-size:10px; border:1px solid #000'>{$fila->nombreMaterial}</td>
        <td style='font-size:10px; border:1px solid #000; text-align:center'>{$si}</td>
        <td style='font-size:10px; border:1px solid #000; text-align:center'>{$no}</td>
        <td style='font-size:10px; border:1px solid #000; text-align:right'>{$fila->precioFormat}</td>
        <td style='font-size:10px; border:1px solid #000; text-align:right'>{$fila->multiplicado}</td>
        <td style='font-size:10px; border:1px solid #000'>{$fila->recomendacion}</td>
    </tr>";
        }

        $tabla .= "
            <tr>
                <td style='font-size:11px; border:1px solid #000'></td>
                <td style='font-size:11px; border:1px solid #000'></td>
                <td style='font-size:11px; border:1px solid #000'></td>  <!-- REEMPLAZO: SI -->
                <td style='font-size:11px; border:1px solid #000'></td>  <!-- REEMPLAZO: NO -->
                <td style='font-size:11px; border:1px solid #000; font-weight:bold; text-align:right'></td>
                <td style='font-size:11px; border:1px solid #000; font-weight:bold; text-align:right'>{$totalColumnaValor}</td>
                <td style='font-size:11px; border:1px solid #000'></td>
            </tr>




</tbody>
</table>";


        $texto1 = "Esperando que dicho Equipo de protección personal cumpla con lo requerido, tendiendo un total de inversión de; " . "<strong>" .$totalColumnaValor . "</strong>" . " sea utilizado de la mejor manera. Yo me comprometo a utilizar el E.P.P. dentro de las horas laborales que me correspondes, correré con el total de la inversión para su reposición echa a mi persona cuando se me compruebe la venta de este equipo, el mal uso, la perdida, el deterioro por negligencia. El cual firmo la presente para constancia de recibido.";


        $tabla .= "
            <div style='text-align: justify; margin-top: 35px; font-family: tahoma, arial, sans-serif;'>
                <p style='font-size: 12px; margin: 0; color: #000;'>$texto1</p>
            </div>
      ";




        $tabla .= "
            <table width='100%' style='margin-top: 30px; border-collapse: collapse; font-family: tahoma, arial, sans-serif; font-size: 12px;'>
                <tr>
                    <!-- Columna izquierda -->
                    <td style='width: 40%; text-align: center; vertical-align: top; padding: 10px;'>
                        <p style='margin: 0; font-weight: bold;'>Firma de Entregado.</p>
                        <p style='margin: 0;'>José Rigoberto Pinto Córdova</p>
                        <p style='margin: 0;'>Jefe de la unidad de S.S.O.</p>
                    </td>

                    <!-- Columna derecha -->
<td style='width: 60%; text-align: left; vertical-align: top; padding: 10px; padding-left: 120px; padding-right: 40px;'>
    <p style='margin: 0; font-weight: bold;'>FIRMA DE RECIBIDO</p>
    <p style='margin: 0;'>
        <span style='font-weight: bold;'>DUI #</span> $infoEmpleado->dui
    </p>
</td>

                </tr>
            </table>
        ";



        $texto2 = "<span style='text-decoration: underline;'>CAPITULO ll INFRACCIONES DE PARTE DE LOS TRABAJADORES Art. 85.</span> – serán objeto de sanción conforme a la legislación vigente, los trabajadores que violen las siguientes medidas de seguridad e higiene: 1) Incumplir las ordenes e instrucciones dadas para garantizar su propia seguridad y salud, las de sus compañeros de trabajo y de terceras personas que se encuentren en el entorno. <span style='background-color: yellow;'>2) No utilizar correctamente los medios y equipos de protección personal facilitados por el empleador, de acuerdo con las instrucciones y regulaciones recibidas por este. </span> 3) No haber información inmediatamente a su jefe inmediato de cualquier situación que a su juicio pueda implicar un riesgo grave e inminente para la seguridad y salud ocupacional, así como de los defectos que hubiere comprobado en los sistemas de protección. Los trabajadores que violen estas disposiciones serán objeto de sanción, de conformidad a los estipulado en el Reglamento Interno de Trabajo de la Empresa, y si la contravención es manifestada y reiterada podrá el empleador dar por terminado su contrato de trabajo, de conformidad al artículo 50 número 17 del código de trabajo.";

        $tabla .= "
            <div style='text-align: justify; margin-top: 35px; font-family: tahoma, arial, sans-serif;'>
                <p style='font-size: 11px; margin: 0; color: #000;'>$texto2</p>
            </div>
      ";


        $stylesheet = file_get_contents('css/cssbodega.css');
        $mpdf->WriteHTML($stylesheet,1);

        $mpdf->setFooter("Página: " . '{PAGENO}' . "/" . '{nb}');
        $mpdf->WriteHTML($tabla,2);

        $mpdf->Output();
    }


    public function actualizarCampos(){

        $arraySalidas = Salidas::all();

        foreach ($arraySalidas as $item) {

            $infoEmpleado = Empleado::where('id', $item->id_empleado)->first();
            $infoUnidad = UnidadEmpleado::where('id', $infoEmpleado->id_unidad_empleado)->first();

            $jefeInmediato = "";

            // JEFE INMEDIATO
            if($unid = UnidadEmpleado::where('id_empleado', $infoEmpleado->id)->first()){ // es el mismo empleado
                $infoEm = Empleado::where('id', $unid->id_empleado_inmediato)->first();
                $jefeInmediato = $infoEm->nombre;

            }else{
                // JEFE DE LA UNIDAD
                if($infoJefe = Empleado::where('id_unidad_empleado', $infoEmpleado->id_unidad_empleado)
                    ->where('jefe', 1)->first()){
                    $jefeInmediato = $infoJefe->nombre;
                }
            }

            $infoCargo = Cargo::where('id', $infoEmpleado->id_cargo)->first();
            $cargo = $infoCargo->nombre;

            Salidas::where('id', $item->id)->update([
                'area' => $infoUnidad->nombre,
                'cargo' =>$cargo,
                'colaborador' => $infoEmpleado->nombre,
                'jefe_inmediato' => $jefeInmediato,
            ]);
        }


        return "completado";

    }


    public function generarPdfSalida($idsalida){

        $infoSalida = Salidas::where('id', $idsalida)->first();

        $infoEmpleado = Empleado::where('id', $infoSalida->id_empleado)->first();
        $infoUnidad = UnidadEmpleado::where('id', $infoEmpleado->id_unidad_empleado)->first();

        $jefeInmediato = "";

        // JEFE INMEDIATO
        if($unid = UnidadEmpleado::where('id_empleado', $infoEmpleado->id)->first()){ // es el mismo empleado
            $infoEm = Empleado::where('id', $unid->id_empleado_inmediato)->first();
            $jefeInmediato = $infoEm->nombre;

        }else{
            // JEFE DE LA UNIDAD
            if($infoJefe = Empleado::where('id_unidad_empleado', $infoEmpleado->id_unidad_empleado)
                ->where('jefe', 1)->first()){
                $jefeInmediato = $infoJefe->nombre;
            }
        }


        $infoCargo = Cargo::where('id', $infoEmpleado->id_cargo)->first();
        $cargo = $infoCargo->nombre;

        $fechaFormat = date("d-m-Y", strtotime($infoSalida->fecha));

        $arraySalidasDetalle = SalidasDetalle::where('id_salida', $idsalida)->get();

        $totalColumna = 0;

        foreach ($arraySalidasDetalle as $item) {

            $infoEntradaDetalle = EntradasDetalle::where('id', $item->id_entrada_detalle)->first();
            $infoMaterial = Materiales::where('id', $infoEntradaDetalle->id_material)->first();


            // precio unitario
            $item->precioFormat = "$" . number_format($infoEntradaDetalle->precio, 2, '.', ',');

            $multiplicado = $item->cantidad_salida * $infoEntradaDetalle->precio;
            $totalColumna += $multiplicado;
            $item->multiplicado = "$" . number_format($multiplicado, 2, '.', ',');

            $item->nombreMaterial = $infoMaterial->nombre;

            $textoRecomendacion = "NO";
            $textoReemplazo = "NO";
            if($item->reemplazo == 1){
                $textoReemplazo = "SI";
            }
            if($item->recomendacion == 1){
                $textoRecomendacion = "SI";
            }

            $item->recomendacion = $textoRecomendacion;
            $item->reemplazo = $textoReemplazo;
        }

        $totalColumnaValor = "$" . number_format($totalColumna, 2, '.', ',');


        // $mpdf = new \Mpdf\Mpdf(['format' => 'LETTER']);
        $mpdf = new \Mpdf\Mpdf(['tempDir' => sys_get_temp_dir(), 'format' => 'LETTER']);

        $mpdf->SetTitle('Reporte Temporal');

        // mostrar errores
        $mpdf->showImageErrors = false;

        $logoalcaldia = 'images/gobiernologo.jpg';
        $logosantaana = 'images/logo.png';


        $tabla = "
            <table style='width: 100%; border-collapse: collapse;'>
                <tr>
                    <!-- Logo izquierdo -->
                    <td style='width: 15%; text-align: left;'>
                        <img src='$logosantaana' alt='Santa Ana Norte' style='max-width: 100px; height: auto;'>
                    </td>
                    <!-- Texto centrado -->
                    <td style='width: 60%; text-align: center;'>
                        <h1 style='font-size: 15px; margin: 0; color: #003366; text-transform: uppercase;'>
                        ALCALDÍA MUNICIPAL DE SANTA ANA NORTE</h1>
                        <h1 style='font-size: 15px; margin: 0; color: #003366; text-transform: uppercase;'>UNIDAD DE SEGURIDAD Y SALUD OCUPACIONAL.</h1>
                        <h2 style='font-size: 13px; margin: 0; color: #003366; text-transform: uppercase;'></h2>
                    </td>
                    <!-- Logo derecho -->
                    <td style='width: 10%; text-align: right;'>
                        <img src='$logoalcaldia' alt='Gobierno de El Salvador' style='max-width: 60px; height: auto;'>
                    </td>
                </tr>
            </table>
            <hr style='border: none; border-top: 2px solid #003366; margin: 0;'>
            ";

        $tabla .= "

            <div style='text-align: center; margin-top: 25px;'>
                <h1 style='font-size: 12px; margin: 0; color: #000;'>Ficha de entrega de Equipo de Protección Personal (E.P.P.)</h1>
            </div>

             <div style='text-align: left; margin-top: 10px;'>
                <p style='font-size: 10px; margin: 0; color: #000;'>Fecha: <strong>$fechaFormat</strong>.</p>
            </div>

            <div style='text-align: left; margin-top: 10px;'>
                <p style='font-size: 10px; margin: 0; color: #000;'>Área: <strong>$infoSalida->area</strong>; Cargo: <strong>$infoSalida->cargo</strong></p>
            </div>
             <div style='text-align: left; margin-top: 10px;'>
                <p style='font-size: 10px; margin: 0; color: #000;'>Se entrega al colaborador(a); <strong>$infoSalida->colaborador</strong></p>
            </div>


            <div style='text-align: left; margin-top: 10px;'>
                <p style='font-size: 10px; margin: 0; color: #000;'>Jefe inmediato: <strong>$infoSalida->jefe_inmediato</strong></p>
            </div>


             <div style='text-align: left; margin-top: 10px;'>
                <p style='font-size: 10px; margin: 0; color: #000;'>Por medio de la presente hace constar el detalle siguiente:</p>
            </div>
      ";


        $tabla  .= "<table width='100%' id='tablaFor' style='margin-top:15px; border-collapse:collapse'>";
        $tabla .= "
            <thead>
                <tr>
                    <th style='text-align:center; font-size:8px; width:10%;  font-weight:bold; border:1px solid #000' rowspan='2'>CANTIDAD</th>
                    <th style='text-align:center; font-size:8px; width:25%;  font-weight:bold; border:1px solid #000' rowspan='2'>DESCRIPCION DE E.P.P.</th>
                    <th style='text-align:center; font-size:8px; width:11%;  font-weight:bold; border:1px solid #000' colspan='2'>REEMPLAZO</th>
                    <th style='text-align:center; font-size:8px; width:11%;  font-weight:bold; border:1px solid #000' rowspan='2'>VALOR</th>
                    <th style='text-align:center; font-size:8px; width:11%;  font-weight:bold; border:1px solid #000' rowspan='2'>VALOR TOTAL</th>
                    <th style='text-align:center; font-size:8px; width:18%;  font-weight:bold; border:1px solid #000' rowspan='2'>
                        RECOMENDACIONES SOBRE EL USO Y MANTENIMIENTO DEL E.P.P. OTORGADO
                    </th>
                </tr>
                <tr>
                    <th style='text-align:center; font-size:8px; font-weight:bold; border:1px solid #000; width:5.5%'>SI</th>
                    <th style='text-align:center; font-size:8px; font-weight:bold; border:1px solid #000; width:5.5%'>NO</th>
                </tr>
            </thead>
            <tbody>";
        foreach ($arraySalidasDetalle as $fila) {
            $esSi = (is_numeric($fila->reemplazo) ? (intval($fila->reemplazo) === 1) : (strtoupper(trim($fila->reemplazo)) === 'SI'));
            $si = $esSi ? 'X' : '';
            $no = $esSi ? '' : 'X';

            $tabla .= "
    <tr>
        <td style='font-size:10px; border:1px solid #000; text-align:center'>{$fila->cantidad_salida}</td>
        <td style='font-size:10px; border:1px solid #000'>{$fila->nombreMaterial}</td>
        <td style='font-size:10px; border:1px solid #000; text-align:center'>{$si}</td>
        <td style='font-size:10px; border:1px solid #000; text-align:center'>{$no}</td>
        <td style='font-size:10px; border:1px solid #000; text-align:right'>{$fila->precioFormat}</td>
        <td style='font-size:10px; border:1px solid #000; text-align:right'>{$fila->multiplicado}</td>
        <td style='font-size:10px; border:1px solid #000'>{$fila->recomendacion}</td>
    </tr>";
        }

        $tabla .= "
            <tr>
                <td style='font-size:11px; border:1px solid #000'></td>
                <td style='font-size:11px; border:1px solid #000'></td>
                <td style='font-size:11px; border:1px solid #000'></td>  <!-- REEMPLAZO: SI -->
                <td style='font-size:11px; border:1px solid #000'></td>  <!-- REEMPLAZO: NO -->
                <td style='font-size:11px; border:1px solid #000; font-weight:bold; text-align:right'></td>
                <td style='font-size:11px; border:1px solid #000; font-weight:bold; text-align:right'>{$totalColumnaValor}</td>
                <td style='font-size:11px; border:1px solid #000'></td>
            </tr>




</tbody>
</table>";


        $texto1 = "Esperando que dicho Equipo de protección personal cumpla con lo requerido, tendiendo un total de inversión de; " . "<strong>" .$totalColumnaValor . "</strong>" . " sea utilizado de la mejor manera. Yo me comprometo a utilizar el E.P.P. dentro de las horas laborales que me correspondes, correré con el total de la inversión para su reposición echa a mi persona cuando se me compruebe la venta de este equipo, el mal uso, la perdida, el deterioro por negligencia. El cual firmo la presente para constancia de recibido.";


        $tabla .= "
            <div style='text-align: justify; margin-top: 35px; font-family: tahoma, arial, sans-serif;'>
                <p style='font-size: 12px; margin: 0; color: #000;'>$texto1</p>
            </div>
      ";




        $tabla .= "
            <table width='100%' style='margin-top: 75px; border-collapse: collapse; font-family: tahoma, arial, sans-serif; font-size: 12px;'>
                <tr>
                    <!-- Columna izquierda -->
                    <td style='width: 40%; text-align: center; vertical-align: top; padding: 10px;'>
                        <p style='margin: 0; font-weight: bold;'>Firma de Entregado.</p>
                        <p style='margin: 0;'>José Rigoberto Pinto Córdova</p>
                        <p style='margin: 0;'>Jefe de la unidad de S.S.O.</p>
                    </td>

                    <!-- Columna derecha -->
                        <td style='width: 60%; text-align: left; vertical-align: top; padding: 10px; padding-left: 120px; padding-right: 40px;'>
                            <p style='margin: 0; font-weight: bold;'>FIRMA DE RECIBIDO</p>
                            <p style='margin: 0;'>
                                <span style='font-weight: bold;'>DUI #</span> $infoEmpleado->dui
                            </p>
                        </td>

                </tr>
            </table>
        ";



        $texto2 = "<span style='text-decoration: underline;'>CAPITULO ll INFRACCIONES DE PARTE DE LOS TRABAJADORES Art. 85.</span> – serán objeto de sanción conforme a la legislación vigente, los trabajadores que violen las siguientes medidas de seguridad e higiene: 1) Incumplir las ordenes e instrucciones dadas para garantizar su propia seguridad y salud, las de sus compañeros de trabajo y de terceras personas que se encuentren en el entorno. <span style='background-color: yellow;'>2) No utilizar correctamente los medios y equipos de protección personal facilitados por el empleador, de acuerdo con las instrucciones y regulaciones recibidas por este. </span> 3) No haber información inmediatamente a su jefe inmediato de cualquier situación que a su juicio pueda implicar un riesgo grave e inminente para la seguridad y salud ocupacional, así como de los defectos que hubiere comprobado en los sistemas de protección. Los trabajadores que violen estas disposiciones serán objeto de sanción, de conformidad a los estipulado en el Reglamento Interno de Trabajo de la Empresa, y si la contravención es manifestada y reiterada podrá el empleador dar por terminado su contrato de trabajo, de conformidad al artículo 50 número 17 del código de trabajo.";

        $tabla .= "
            <div style='text-align: justify; margin-top: 35px; font-family: tahoma, arial, sans-serif;'>
                <p style='font-size: 11px; margin: 0; color: #000;'>$texto2</p>
            </div>
      ";


        $stylesheet = file_get_contents('css/cssbodega.css');
        $mpdf->WriteHTML($stylesheet,1);

        $mpdf->setFooter("Página: " . '{PAGENO}' . "/" . '{nb}');
        $mpdf->WriteHTML($tabla,2);

        $mpdf->Output();
    }

























    //******************** RETORNO *************************************************************


    public function vistaRetorno()
    {
        $arrayEncargado = Encargado::orderBy('nombre', 'asc')->get();

        return view('backend.admin.registros.retornos.vistaretorno', compact('arrayEncargado'));
    }

    public function tablaRetorno()
    {
        $lista = DB::table('salidas_detalle AS sa')
            ->join('entradas_detalle AS deta', 'sa.id_entrada_detalle', '=', 'deta.id')
            ->select('sa.cantidad_salida', 'deta.id_material', 'sa.id', 'sa.id_salida', 'deta.id_entradas')
            ->where('sa.cantidad_salida', '>', 0) // SOLO CANTIDAD FUERA MAYOR A 0
            ->where('sa.tipo_regresa', 1) // VERIFICAR LOS QUE REGRESARAN A BODEGA
            ->get();

        foreach ($lista as $fila){

            $infoMaterial = Materiales::where('id', $fila->id_material)->first();
            $fila->nombreMaterial = $infoMaterial->nombre;

            $infoSalida = Salidas::where('id', $fila->id_salida)->first();
            $fila->fechaSalida =date("d-m-Y", strtotime($infoSalida->fecha));

            $infoEntrada = Entradas::where('id', $fila->id_entradas)->first();
            $fila->lote = $infoEntrada->lote;

            $infoEncargado = Encargado::where('id', $infoSalida->id_encargado)->first();
            $fila->nombreEncargado = $infoEncargado->nombre;

            $infoDistrito = Distrito::where('id', $infoSalida->id_distrito)->first();
            $fila->nombreDistrito = $infoDistrito->nombre;

            $infoMedida = UnidadMedida::where('id', $infoMaterial->id_medida)->first();
            $fila->nombreMedida = $infoMedida->nombre;


            $fila->nombreOtros = $infoMaterial->otros;


            $infoMarca = Marca::where('id', $infoMaterial->id_marca)->first();
            $fila->nombreMarca = $infoMarca->nombre;

            $infoNormativa = Normativa::where('id', $infoMaterial->id_normativa)->first();
            $fila->nombreNormativa = $infoNormativa->nombre;

            $color = "";
            $talla = "";
            if($info = Color::where('id', $infoMaterial->id_color)->first()){
                $color = $info->nombre;
            }
            if($info = Talla::where('id', $infoMaterial->id_talla)->first()){
                $talla = $info->nombre;
            }

            $fila->nombreColor = $color;
            $fila->nombreTalla = $talla;
        }

        return view('backend.admin.registros.retornos.tablaretorno', compact('lista'));
    }


    public function informacionRetorno(Request $request)
    {
        $regla = array(
            'id' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        if($info = SalidasDetalle::where('id', $request->id)->first()){
            return ['success' => 1, 'info' => $info];
        }else{
            return ['success' => 2];
        }
    }


    public function registrarRetorno(Request $request)
    {
        $regla = array(
            'id' => 'required', // salidas_detalle
            'fecha' => 'required',
            'retorno' => 'required',
            'encargado' => 'required',
        );

        // descripcion

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        DB::beginTransaction();

        try {

            $infoSalidaDetalle = SalidasDetalle::where('id', $request->id)->first();

            // REGISTRAR RETORNO
            $nuevo = new Retorno();
            $nuevo->fecha = $request->fecha;
            $nuevo->id_entrada_detalle = $infoSalidaDetalle->id_entrada_detalle;
            $nuevo->id_salida_detalle = $request->id;
            $nuevo->id_encargado = $request->encargado;
            $nuevo->observacion = $request->descripcion;
            $nuevo->tipo_retorno = 0; // 0: Retorno 1: Descarte
            $nuevo->cantidad_reingreso = $request->retorno;
            $nuevo->cantidad_descarto = 0; // SOLO CUANDO SE DESCARTA
            $nuevo->save();


            $resta = $infoSalidaDetalle->cantidad_salida - $request->retorno;

            if($resta < 0){
                // ERROR NO DEBERIA SER MENOR
                return ['success' => 99];
            }

            // SE RESTARA LA CANTIDAD ENTREGADA
            SalidasDetalle::where('id', $request->id)->update([
                'cantidad_salida' => $resta
            ]);

            $infoEntradaDeta = EntradasDetalle::where('id', $infoSalidaDetalle->id_entrada_detalle)->first();
            // SE RESTARA TAMBIEN CANTIDAD ENTREGADA
            $restaEntradaDeta = $infoEntradaDeta->cantidad_entregada - $request->retorno;

            if($restaEntradaDeta < 0){
                // ERROR NO DEBERIA SER MENOR
                return ['success' => 99];
            }

            EntradasDetalle::where('id', $infoEntradaDeta->id)->update([
                'cantidad_entregada' => $restaEntradaDeta
            ]);


            DB::commit();
            return ['success' => 1];
        }catch(\Throwable $e){
            Log::info('error ' . $e);
            DB::rollback();
            return ['success' => 99];
        }
    }



    public function registrarDescarte(Request $request)
    {
        $regla = array(
            'id' => 'required', // salidas_detalle
            'fecha' => 'required',
            'descarto' => 'required',
            'descripcion' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        DB::beginTransaction();

        try {

            $infoSalidaDetalle = SalidasDetalle::where('id', $request->id)->first();

            // REGISTRAR RETORNO
            $nuevo = new Retorno();
            $nuevo->fecha = $request->fecha;
            $nuevo->id_entrada_detalle = $infoSalidaDetalle->id_entrada_detalle;
            $nuevo->id_salida_detalle = $request->id;
            $nuevo->id_encargado = null;
            $nuevo->observacion = $request->descripcion;
            $nuevo->tipo_retorno = 1; // 0: Retorno 1: Descarte
            $nuevo->cantidad_reingreso = 0; // SOLO PARA REINGRESO
            $nuevo->cantidad_descarto = $request->descarto;
            $nuevo->save();

            // SE DEBERA ACTUALIZAR SALIDAS DETALLE

            $resta = $infoSalidaDetalle->cantidad_salida - $request->descarto;

            if($resta < 0){
                // ERROR NO DEBERIA SER MENOR
                return ['success' => 99];
            }

            // SE RESTARA LA CANTIDAD ENTREGADA
            SalidasDetalle::where('id', $request->id)->update([
                'cantidad_salida' => $resta
            ]);

            $infoEntradaDeta = EntradasDetalle::where('id', $infoSalidaDetalle->id_entrada_detalle)->first();
            // SE RESTARA TAMBIEN CANTIDAD DESCARTAR
            $restaEntradaDeta = $infoEntradaDeta->cantidad_entregada - $request->descarto;
            $restaCantidad = $infoEntradaDeta->cantidad - $request->descarto;

            if($restaEntradaDeta < 0){
                // ERROR NO DEBERIA SER MENOR
                return ['success' => 99];
            }

            if($restaCantidad < 0){
                // ERROR NO DEBERIA SER MENOR
                return ['success' => 99];
            }

            EntradasDetalle::where('id', $infoEntradaDeta->id)->update([
                'cantidad_entregada' => $restaEntradaDeta,
                'cantidad' => $restaCantidad, // SE BAJARA LA CANTIDAD ACTUAL DE ESE PRODUCTO - ENTRADA
            ]);


            DB::commit();
            return ['success' => 1];
        }catch(\Throwable $e){
            Log::info('error ' . $e);
            DB::rollback();
            return ['success' => 99];
        }
    }


    public function borrarRetornoDescarte(Request $request)
    {

        $regla = array(
            'id' => 'required', // id retorno
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        DB::beginTransaction();

        try {

            if($infoRetorno = Retorno::where('id', $request->id)->first()){

                // IDENTIFICAR EL TIPO SI FUE RETORNO O DESCARTE
                $infoSalidaDetalle = SalidasDetalle::where('id', $infoRetorno->id_salida_detalle)->first();


                if($infoRetorno->tipo_retorno == 0){
                    // TIPO REINGRESO

                    $suma = $infoSalidaDetalle->cantidad_salida + $infoRetorno->cantidad_reingreso;

                    // SE SUMARA LA CANTIDAD ENTREGADA
                    SalidasDetalle::where('id', $infoSalidaDetalle->id)->update([
                        'cantidad_salida' => $suma
                    ]);

                    $infoEntradaDeta = EntradasDetalle::where('id', $infoRetorno->id_entrada_detalle)->first();
                    // SE RESTARA TAMBIEN CANTIDAD ENTREGADA
                    $sumaEntradaDeta = $infoEntradaDeta->cantidad_entregada + $infoRetorno->cantidad_reingreso;

                    EntradasDetalle::where('id', $infoEntradaDeta->id)->update([
                        'cantidad_entregada' => $sumaEntradaDeta
                    ]);

                    Retorno::where('id', $request->id)->delete();
                }
                else if($infoRetorno->tipo_retorno == 1){

                    // TIPO DESCARTE

                    $suma = $infoSalidaDetalle->cantidad_salida + $infoRetorno->cantidad_descarto;

                    // SE SUMARA LA CANTIDAD ENTREGADA
                    SalidasDetalle::where('id', $infoSalidaDetalle->id)->update([
                        'cantidad_salida' => $suma
                    ]);
                    $infoEntradaDetalle = EntradasDetalle::where('id', $infoRetorno->id_entrada_detalle)->first();

                    $sumaEntradaDeta = $infoEntradaDetalle->cantidad_entregada + $infoRetorno->cantidad_descarto;
                    // sumar de nuevo
                    $sumaCantidad = $infoEntradaDetalle->cantidad + $infoRetorno->cantidad_descarto;


                    EntradasDetalle::where('id', $infoEntradaDetalle->id)->update([
                        'cantidad_entregada' => $sumaEntradaDeta,
                        'cantidad' => $sumaCantidad,
                    ]);

                    Retorno::where('id', $request->id)->delete();
                }
                else{
                    // ERROR
                    return ['success' => 99];
                }
            }


            DB::commit();
            return ['success' => 1];
        }catch(\Throwable $e){
            Log::info('error ' . $e);
            DB::rollback();
            return ['success' => 99];
        }
    }





}
