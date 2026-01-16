<?php

namespace App\Http\Controllers\backend\materiales;

use App\Http\Controllers\Controller;
use App\Models\Cargo;
use App\Models\Color;
use App\Models\Distrito;
use App\Models\Empleado;
use App\Models\Entradas;
use App\Models\EntradasDetalle;
use App\Models\Marca;
use App\Models\Materiales;
use App\Models\Normativa;
use App\Models\Proveedor;
use App\Models\Retorno;
use App\Models\Salidas;
use App\Models\SalidasDetalle;
use App\Models\Talla;
use App\Models\UnidadEmpleado;
use App\Models\UnidadMedida;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

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

            // distrito unidad empleado
            $infoEmpleado = Empleado::where('id',$fila->id_empleado)->first();
            $infoUnidad = UnidadEmpleado::where('id',$infoEmpleado->id_unidad_empleado)->first();
            $infoDistrito = Distrito::where('id',$infoUnidad->id_distrito)->first();

            if($infoEmpleado->jefe == 1){
                $nombreFull = $infoEmpleado->nombre . " (JEFE)";
            }else{
                $nombreFull = $infoEmpleado->nombre;
            }

            $fila->empleado = $nombreFull;
            $fila->unidad = $infoUnidad->nombre;
            $fila->distrito = $infoDistrito->nombre;
        }

        return view('backend.admin.historial.salidas.tablasalidabodega', compact('listado'));
    }


    public function informacionHistorialSalida(Request $request)
    {
        $regla = array(
            'id' => 'required' // id salida
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()) {
            return ['success' => 0];
        }

        $infoSalida = Salidas::where('id', $request->id)->first();

        $infoEmpleado = Empleado::where('id',$infoSalida->id_empleado)->first();
        $infoUniEmpleado = UnidadEmpleado::where('id', $infoEmpleado->id_unidad_empleado)->first();

        $arrayDistrito = Distrito::orderBy('nombre', 'ASC')->get();
        $arrayUnidad = UnidadEmpleado::orderBy('nombre', 'ASC')->get();
        $arrayCargo = Cargo::orderBy('nombre', 'ASC')->get();
        $arrayEmpleados = Empleado::where('id_unidad_empleado', $infoEmpleado->id_unidad_empleado)->get();

        return ['success' => 1, 'info' => $infoSalida, 'arrayDistrito' => $arrayDistrito, 'arrayCargo' => $arrayCargo,
            'arrayUnidad' => $arrayUnidad, 'arrayEmpleados' => $arrayEmpleados, 'infoUniEmpleado' => $infoUniEmpleado];
    }


    public function editarHistorialSalida(Request $request)
    {
        $regla = array(
            'id' => 'required',
            'fecha' => 'required',
        );

        // descripcion, empleado

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()) {
            return ['success' => 0];
        }

        Salidas::where('id', $request->id)->update([
            'fecha' => $request->fecha,
            'descripcion' => $request->descripcion,
            'id_empleado' => $request->empleado,
        ]);

        return ['success' => 1];
    }














    public function indexHistorialSalidasDetalle($id)
    {
        return view('backend.admin.historial.salidas.detalle.vistasalidadetallebodega', compact('id'));
    }

    public function tablaHistorialSalidasDetalle($id){

        $infoSalida = Salidas::where('id', $id)->first();

        $listado = SalidasDetalle::where('id_salida', $id)->get();

        foreach ($listado as $fila) {

            $infoEntraDeta = EntradasDetalle::where('id', $fila->id_entrada_detalle)->first();
            $infoMaterial = Materiales::where('id', $infoEntraDeta->id_material)->first();
            $fila->nombreMaterial = $infoMaterial->nombre;

            $infoMedida = UnidadMedida::where('id', $infoMaterial->id_medida)->first();
            $fila->nombreUnidad = $infoMedida->nombre;


            $marca = "";
            $normativa = "";
            $color = "";
            $talla = "";

            if($info = Marca::where('id', $infoMaterial->id_marca)->first()){
                $marca = $info->nombre;
            }

            if($info = Normativa::where('id', $infoMaterial->id_normativa)->first()){
                $normativa = $info->nombre;
            }

            if($info = Color::where('id', $infoMaterial->id_color)->first()){
                $color = $info->nombre;
            }

            if($info = Talla::where('id', $infoMaterial->id_talla)->first()){
                $talla = $info->nombre;
            }

            $fila->marca = $marca;
            $fila->normativa = $normativa;
            $fila->color = $color;
            $fila->talla = $talla;
        }



        return view('backend.admin.historial.salidas.detalle.tablasalidadetallebodega', compact('listado', 'infoSalida'));
    }



    public function indexHistorialSalidasDetalleMovimientos($id){
        // id salida_detalle
        return view('backend.admin.historial.salidas.detalle.movimientos.vistamovimientosdeta', compact('id'));
    }


    public function tablaHistorialSalidasDetalleMovimientos($id){
        // id salida_detalle

        $listado = Retorno::where('id_salida_detalle', $id)
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

        return view('backend.admin.historial.salidas.detalle.movimientos.tablamovimientosdeta', compact('listado'));
    }




    public function salidaDetalleBorrarItem(Request $request)
    {
        $regla = array(
            'id' => 'required', //tabla: salidas_detalle
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){return ['success' => 0];}

        if($infoSalidaDeta = SalidasDetalle::where('id', $request->id)->first()){

            DB::beginTransaction();

            try {

                $infoBodegaEntraDeta = EntradasDetalle::where('id', $infoSalidaDeta->id_entrada_detalle)->first();
                $resta = $infoBodegaEntraDeta->cantidad_entregada - $infoSalidaDeta->cantidad_salida;

                // SI HUBO DESCARTES SE DEBERAN CUMAR A cantidad DEL ENTRADA_DETALLE
                $conteoDescartados = Retorno::where('id_salida_detalle', $request->id)
                    ->where('tipo_retorno', 0) // DESCARTES
                    ->sum('cantidad_descarto');

                $sumaPorDescarte = $infoBodegaEntraDeta->cantidad + $conteoDescartados;

                EntradasDetalle::where('id', $infoBodegaEntraDeta->id)->update([
                    'cantidad_entregada' => $resta,
                    'cantidad' => $sumaPorDescarte
                ]);

                // BORRAR SALIDAS DETALLE
                SalidasDetalle::where('id', $request->id)->delete();
                // BORRAR SALIDAS (ESTO VERIFICA QUE SINO TIENE DETALLE, ELIMINA EL bodega_salidas)
                Salidas::whereNotIn('id', SalidasDetalle::pluck('id_salida'))->delete();

                DB::commit();
                return ['success' => 1];
            } catch (\Throwable $e) {
                Log::info('ee ' . $e);
                DB::rollback();
                return ['success' => 99];
            }

        }else{
            return ['success' => 99];
        }
    }



    public function indexHistorialEntradas()
    {
        return view('backend.admin.historial.entradas.vistaentradabodega');
    }

    public function tablaHistorialEntradas()
    {
        $listado = Entradas::orderBy('fecha', 'asc')->get();

        foreach ($listado as $fila) {
            $fila->fechaFormat = date("d-m-Y", strtotime($fila->fecha));

            $nombreProveedor = "";
            if($infoProveedor = Proveedor::where('id', $fila->id_proveedor)->first()){
                $nombreProveedor = $infoProveedor->nombre;
            }
            $fila->nombreProveedor = $nombreProveedor;

        }

        return view('backend.admin.historial.entradas.tablaentradabodega', compact('listado'));
    }



    public function informacionHistorialEntrada(Request $request)
    {
        $regla = array(
            'id' => 'required', // id historial entrada
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()) {
            return ['success' => 0];
        }

        $info = Entradas::where('id', $request->id)->first();

        $arrayProveedor = Proveedor::orderBy('nombre', 'asc')->get();

        return ['success' => 1, 'info' => $info, 'arrayProveedor' => $arrayProveedor];
    }




    public function editarHistorialEntrada(Request $request)
    {
        $regla = array(
            'id' => 'required',
            'fecha' => 'required'
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()) {
            return ['success' => 0];
        }

        Entradas::where('id', $request->id)->update([
            'fecha' => $request->fecha,
            'descripcion' => $request->descripcion,
            'lote' => $request->lote,
            'id_proveedor' => $request->proveedor
        ]);

        return ['success' => 1];
    }


    public function indexHistorialEntradasDetalle($id)
    {
        $info = Entradas::where('id', $id)->first();

        return view('backend.admin.historial.entradas.detalle.vistaentradadetallebodega', compact('id', 'info'));
    }

    public function tablaHistorialEntradasDetalle($id){


        $listado = DB::table('entradas_detalle AS bo')
            ->join('materiales AS bm', 'bo.id_material', '=', 'bm.id')
            ->join('unidad_medida AS uni', 'bm.id_medida', '=', 'uni.id')
            ->select('bo.id', 'bo.cantidad', 'bm.nombre', 'uni.nombre AS nombreUnidad',
                'bo.id_entradas', 'bm.id_marca', 'bm.id_normativa', 'bm.id_color', 'bm.id_talla', 'bo.precio')
            ->where('bo.id_entradas', $id)
            ->get();

        foreach ($listado as $fila) {

            $marca = "";
            $normativa = "";
            $color = "";
            $talla = "";

            if($info = Marca::where('id', $fila->id_marca)->first()){
                $marca = $info->nombre;
            }

            if($info = Normativa::where('id', $fila->id_normativa)->first()){
                $normativa = $info->nombre;
            }

            if($info = Color::where('id', $fila->id_color)->first()){
                $color = $info->nombre;
            }

            if($info = Talla::where('id', $fila->id_talla)->first()){
                $talla = $info->nombre;
            }

           $fila->marca = $marca;
           $fila->normativa = $normativa;
           $fila->color = $color;
           $fila->talla = $talla;

           $fila->precioFormat = '$' . number_format((float)$fila->precio, 2, '.', ',');
        }

        return view('backend.admin.historial.entradas.detalle.tablaentradadetallebodega', compact('listado'));
    }


    public function historialEntradaBorrarLote(Request $request)
    {
        $regla = array(
            'id' => 'required', //tabla: entradas
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){return ['success' => 0];}

        // VERIFICAR QUE EXISTA LA ENTRADA
        if(Entradas::where('id', $request->id)->first()){

            DB::beginTransaction();

            try {

                // OBTENER TODOS LOS DETALLES DE ESA ENTRADA
                $arrayEntradaDetalle = EntradasDetalle::where('id_entradas', $request->id)->get();
                $pilaIdEntradaDetalle = array();

                foreach ($arrayEntradaDetalle as $fila) {
                    // GUARDAR ID DE CADA ENTRADA DETALLE
                    array_push($pilaIdEntradaDetalle, $fila->id);
                }

                // BORRAR MOVIMIENTOS
                Retorno::whereIn('id_entrada_detalle', $pilaIdEntradaDetalle)->delete();

                // BORRAR SALIDAS DETALLE
                SalidasDetalle::whereIn('id_entrada_detalle', $pilaIdEntradaDetalle)->delete();
                // BORRAR SALIDAS
                Salidas::whereNotIn('id', SalidasDetalle::pluck('id_salida'))->delete();

                // BORRAR ENTRADAS FINALMENTE
                EntradasDetalle::where('id_entradas', $request->id)->delete();
                Entradas::where('id', $request->id)->delete();

                DB::commit();
                return ['success' => 1];

            } catch (\Throwable $e) {
                Log::info('ee ' . $e);
                DB::rollback();
                return ['success' => 99];
            }
        }else{
            return ['success' => 99];
        }
    }


    public function historialEntradaDetalleBorrarItem(Request $request)
    {
        $regla = array(
            'id' => 'required', //tabla: entradas_detalle
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){return ['success' => 0];}

        if($infoEntradaDeta = EntradasDetalle::where('id', $request->id)->first()){

            DB::beginTransaction();

            try {

                // BORRAR MOVIMIENTOS
                Retorno::where('id_entrada_detalle', $infoEntradaDeta->id)->delete();

                // BORRAR SALIDAS DETALLE
                SalidasDetalle::where('id_entrada_detalle', $infoEntradaDeta->id)->delete();
                // BORRAR SALIDAS
                Salidas::whereNotIn('id', SalidasDetalle::pluck('id_salida'))->delete();

                // BORRAR ENTRADAS FINALMENTE
                EntradasDetalle::where('id', $infoEntradaDeta->id)->delete();

                // SI YA NO HAY ENTRADAS SE DEBERA BORRAR
                Entradas::whereNotIn('id', EntradasDetalle::pluck('id_entradas'))->delete();

                DB::commit();
                return ['success' => 1];

            } catch (\Throwable $e) {
                Log::info('ee ' . $e);
                DB::rollback();
                return ['success' => 99];
            }
        }else{
            return ['success' => 99];
        }
    }


    public function indexNuevoIngresoEntradaDetalle($id)
    {
        // id: es de entradas
        $info = Entradas::where('id', $id)->first();

        return view('backend.admin.historial.entradas.detalle.vistaingresoextra', compact('id', 'info'));
    }

    public function registrarProductosExtras(Request $request)
    {

        $regla = array(
            'identrada' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()) {
            return ['success' => 0];
        }

        DB::beginTransaction();

        try {

            // Obtiene los datos enviados desde el formulario como una cadena JSON y luego decódificala
            $datosContenedor = json_decode($request->contenedorArray, true); // El segundo argumento convierte el resultado en un arreglo

            foreach ($datosContenedor as $filaArray) {

                $detalle = new EntradasDetalle();
                $detalle->id_entradas = $request->identrada;
                $detalle->id_material = $filaArray['infoIdProducto'];
                $detalle->cantidad = $filaArray['infoCantidad'];
                $detalle->cantidad_inicial = $filaArray['infoCantidad'];
                $detalle->precio = $filaArray['infoPrecio'];
                $detalle->cantidad_entregada = 0;
                $detalle->save();
            }

            DB::commit();
            return ['success' => 1];

        } catch (\Throwable $e) {
            Log::info('error ' . $e);
            DB::rollback();
            return ['success' => 99];
        }
    }


    public function indexHistorialRetornos()
    {
        return view('backend.admin.historial.retornos.vistahistorialretornos');
    }

    public function tablaHistorialRetornos()
    {

        $listado = Retorno::orderBy('fecha', 'asc')->get();

        foreach ($listado as $fila) {
            $fila->fechaFormat = date("d-m-Y", strtotime($fila->fecha));

            $infoEntradaDetalle = EntradasDetalle::where('id', $fila->id_entrada_detalle)->first();
            $infoEntrada = Entradas::where('id', $infoEntradaDetalle->id_entradas)->first();
            $infoMaterial = Materiales::where('id', $infoEntradaDetalle->id_material)->first();

           // $infoEncargado = Encargado::where('id', $fila->id_encargado)->first();
            //$infoSalidaDetalle = SalidasDetalle::where('id', $fila->id_salida_detalle)->first();

            $infoUnidadMedida = UnidadMedida::where('id', $infoMaterial->id_medida)->first();
            $fila->nombreMedida = $infoUnidadMedida->nombre;

            $infoMarca = Marca::where('id', $infoMaterial->id_marca)->first();
            $fila->nombreMarca = $infoMarca->nombre;

            $infoNormativa = Normativa::where('id', $infoMaterial->id_normativa)->first();
            $fila->nombreNormativa = $infoNormativa->nombre;

            $color = "";
            if($info = Color::where('id', $infoMaterial->id_color)->first()){
                $color = $info->color;
            }
            $fila->nombreColor = $color;


            $talla = "";
            if($info = Talla::where('id', $infoMaterial->id_talla)->first()){
                $talla = $info->talla;
            }
            $fila->nombreTalla = $talla;

            $fila->lote = $infoEntrada->lote;
            $fila->nombreMaterial = $infoMaterial->nombre;
        }

        return view('backend.admin.historial.retornos.tablahistorialretornos', compact('listado'));
    }






    // =========== REEMPLAZO MES ==============


    public function indexReemplazoMes()
    {
        return view('backend.admin.reemplazo.vistamesreemplazo');
    }

    public function tablaReemplazoMes()
    {
        $arrayListado = SalidasDetalle::where('completado', 0)
            ->get()
            ->map(function ($item) {

                $infoSalida = Salidas::where('id', $item->id_salida)->first();
                $item->fechaSalida = date("d-m-Y", strtotime($infoSalida->fecha));

                // === Calcular fecha de reemplazo ===
                $fechaSalida = Carbon::parse($infoSalida->fecha);
                $mesesReemplazo = (int) $item->mes_reemplazo;

                // Sumar los meses al campo fecha
                $fechaReemplazo = $fechaSalida->copy()->addMonths($mesesReemplazo);

                // Obtener fecha actual de El Salvador
                $fechaActual = Carbon::now('America/El_Salvador');

                // Comparar si ya llegó o pasó la fecha de reemplazo
                if ($fechaActual->greaterThanOrEqualTo($fechaReemplazo)) {
                    $item->venceHoyOVencido = 1;
                } else {
                    $item->venceHoyOVencido = 0;
                }

                // (opcional) agregar formato legible de fecha de reemplazo
                $item->fechaReemplazo = $fechaReemplazo->format('d-m-Y');

                return $item;
            });


        return $arrayListado;

        return view('backend.admin.reemplazo.tablamesreemplazo');
    }



    public function indexExistencias()
    {
        return view('backend.admin.reportes.existencia.vistaexistencias');
    }



    public function reportePdfExistencias()
    {

        $mpdf = new \Mpdf\Mpdf([
            'tempDir' => sys_get_temp_dir(),
            'format' => 'LETTER',
            'orientation' => 'L'
        ]);

        $mpdf->SetTitle('Existencias');

        // mostrar errores
        $mpdf->showImageErrors = false;

        $logoalcaldia = 'images/gobiernologo.jpg';
        $logosantaana = 'images/logo.png';

        $fechaFormat = date("d-m-Y", strtotime(Carbon::now('America/El_Salvador')));



        $existencias = DB::table('materiales as m')
            ->leftJoin('entradas_detalle as ed', 'ed.id_material', '=', 'm.id')
            ->leftJoin('unidad_medida as um', 'um.id', '=', 'm.id_medida')
            ->leftJoin('marca as ma', 'ma.id', '=', 'm.id_marca')
            ->leftJoin('color as c', 'c.id', '=', 'm.id_color')
            ->leftJoin('talla as t', 't.id', '=', 'm.id_talla')
            ->select(
                'm.codigo',
                'm.nombre as material',
                'um.nombre as unidad',
                'ma.nombre as marca',
                'c.nombre as color',
                't.nombre as talla',
                DB::raw('COALESCE(SUM(ed.cantidad_inicial - ed.cantidad_entregada),0) as existencia'),
                DB::raw('SUM((ed.cantidad_inicial - ed.cantidad_entregada) * ed.precio) as valor_total')
            )

            ->groupBy(
                'm.id', 'm.codigo', 'm.nombre',
                'um.nombre', 'ma.nombre', 'c.nombre', 't.nombre'
            )
            ->orderBy('m.nombre')
            ->having('existencia', '>', 0)
            ->get();





        $tabla = "

            <table style='width: 100%; border-collapse: collapse; font-family: Arial, normal, sans-serif; font-size:11px;' >
                <tr>
                    <!-- Logo izquierdo -->
                    <td style='width: 15%; text-align: left;'>
                        <img src='$logosantaana' alt='Santa Ana Norte' style='max-width: 100px; height: auto;'>
                    </td>
                    <!-- Texto centrado -->
                    <td style='width: 60%; text-align: center;'>
                        <h1 style='font-size: 15px; margin: 0; color: #003366; text-transform: uppercase;'>
                        ALCALDÍA MUNICIPAL DE SANTA ANA NORTE</h1>
                        <h1 style='font-size: 15px; margin: 0; color: #003366; text-transform: uppercase;'>UNIDAD DE SEGURIDAD Y SALUD OCUPACIONAL.</h1> <br>
                        <h2 style='font-size: 13px; margin: 0; color: #003366; text-transform: uppercase;'>REPORTE DE EXISTENCIAS</h2>
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
        <p>Fecha Generado: $fechaFormat</p>
        ";


        $tabla .= "
<table width='100%' border='1' cellspacing='0' cellpadding='5'>
    <thead>
        <tr style='background-color:#f0f0f0; font-size:12px'>
            <th>#</th>
            <th>Código</th>
            <th>Material</th>
            <th>Marca</th>
            <th>Color</th>
            <th>Talla</th>
            <th>Unidad</th>
            <th>Existencia</th>
            <th>Valor ($)</th>
        </tr>
    </thead>
    <tbody>
";

        $cont = 1;
        $totalExistencia = 0;
        $totalValor = 0;


        foreach ($existencias as $item) {

            $existencia = (int) $item->existencia;
            $valor = (float) $item->valor_total;

            $totalExistencia += $existencia;
            $totalValor += $valor;

            $tabla .= "
        <tr style='font-size:11px'>
            <td>{$cont}</td>
            <td>{$item->codigo}</td>
            <td>{$item->material}</td>
            <td>{$item->marca}</td>
            <td>{$item->color}</td>
            <td>{$item->talla}</td>
            <td>{$item->unidad}</td>
            <td align='right'>{$existencia}</td>
            <td align='right'>$ ".number_format($valor, 2)."</td>
        </tr>
    ";

            $cont++;
        }

        $tabla .= "
    </tbody>
    <tfoot>
        <tr style='font-weight:bold; background-color:#e6e6e6'>
            <td colspan='8' align='right'>TOTAL GENERAL</td>

            <td align='right'>$ ".number_format($totalValor, 2)."</td>
        </tr>
    </tfoot>
</table>
";



        $stylesheet = file_get_contents('css/cssbodega.css');
        $mpdf->WriteHTML($stylesheet,1);

        $mpdf->setFooter("Página: " . '{PAGENO}' . "/" . '{nb}');
        $mpdf->WriteHTML($tabla,2);

        $mpdf->Output();

    }




















}
