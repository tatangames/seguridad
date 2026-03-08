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
        $regla = array('id' => 'required');
        $validar = Validator::make($request->all(), $regla);
        if ($validar->fails()) return ['success' => 0];

        $infoSalida      = Salidas::where('id', $request->id)->first();
        $infoEmpleado    = Empleado::where('id', $infoSalida->id_empleado)->first();
        $infoUniEmpleado = UnidadEmpleado::where('id', $infoEmpleado->id_unidad_empleado)->first();

        $arrayDistrito  = Distrito::orderBy('nombre', 'ASC')->get();
        // ← Filtrar unidades solo del distrito actual
        $arrayUnidad    = UnidadEmpleado::where('id_distrito', $infoUniEmpleado->id_distrito)
            ->orderBy('nombre', 'ASC')->get();
        $arrayEmpleados = Empleado::where('id_unidad_empleado', $infoEmpleado->id_unidad_empleado)
            ->orderBy('nombre', 'ASC')->get();

        return [
            'success'        => 1,
            'info'           => $infoSalida,
            'arrayDistrito'  => $arrayDistrito,
            'arrayUnidad'    => $arrayUnidad,
            'arrayEmpleados' => $arrayEmpleados,
            'infoUniEmpleado'=> $infoUniEmpleado,
        ];
    }


    public function editarHistorialSalida(Request $request)
    {
        $regla = [
            'id'      => 'required',
            'fecha'   => 'required',
            'empleado'=> 'required',
        ];

        $validar = Validator::make($request->all(), $regla);
        if ($validar->fails()) return ['success' => 0];

        // Recalcular snapshot del empleado seleccionado
        $empleado   = Empleado::find($request->empleado);
        $infoCargo  = Cargo::find($empleado->id_cargo);
        $infoUnidad = UnidadEmpleado::find($empleado->id_unidad_empleado);

        $colaborador = $empleado->nombre;
        $area        = $infoUnidad?->nombre ?? '';
        $cargo       = $infoCargo?->nombre  ?? '';

        // Jefe inmediato
        if ($empleado->jefe == 1) {
            $jefeDirecto   = Empleado::find($empleado->id_jefe);
            $jefeInmediato = $jefeDirecto?->nombre ?? '';
        } else {
            $jefeInmediato = DB::table('jefe_unidad')
                ->join('empleado', 'jefe_unidad.id_empleado', '=', 'empleado.id')
                ->where('jefe_unidad.id_unidad_empleado', $empleado->id_unidad_empleado)
                ->pluck('empleado.nombre')
                ->implode(' / ') ?: '';
        }

        Salidas::where('id', $request->id)->update([
            'fecha'          => $request->fecha,
            'descripcion'    => $request->descripcion,
            'id_empleado'    => $request->empleado,
            'material_linea' => $request->linea,
            // ── Snapshot ──────────────────────────────────
            'colaborador'    => $colaborador,
            'area'           => $area,
            'cargo'          => $cargo,
            'jefe_inmediato' => $jefeInmediato,
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

    public function tablaHistorialEntradasDetalle($id)
    {
        $listado = DB::table('entradas_detalle AS bo')
            ->join('materiales AS bm', 'bo.id_material', '=', 'bm.id')
            ->join('unidad_medida AS uni', 'bm.id_medida', '=', 'uni.id')
            ->select('bo.id', 'bo.cantidad_inicial AS cantidad', 'bm.nombre', 'uni.nombre AS nombreUnidad',
                'bo.id_entradas', 'bm.id_marca', 'bm.id_normativa', 'bm.id_color', 'bm.id_talla', 'bo.precio')
            ->where('bo.id_entradas', $id)
            ->get();

        foreach ($listado as $fila) {

            $marca      = "";
            $normativa  = "";
            $color      = "";
            $talla      = "";

            if ($infoMarca = Marca::where('id', $fila->id_marca)->first()) {
                $marca = $infoMarca->nombre;
            }

            if ($infoNormativa = Normativa::where('id', $fila->id_normativa)->first()) {
                $normativa = $infoNormativa->nombre;
            }

            if ($infoColor = Color::where('id', $fila->id_color)->first()) {
                $color = $infoColor->nombre;
            }

            if ($infoTalla = Talla::where('id', $fila->id_talla)->first()) {
                $talla = $infoTalla->nombre;
            }

            $fila->marca      = $marca;
            $fila->normativa  = $normativa;
            $fila->color      = $color;
            $fila->talla      = $talla;

            $fila->precioFormat = '$' . number_format((float) $fila->precio, 2, '.', ',');
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
                $detalle->cantidad_inicial = $filaArray['infoCantidad'];
                $detalle->precio = $filaArray['infoPrecio'];
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






    // =========== REEMPLAZO MES ==============


    public function indexReemplazoMes()
    {
        return view('backend.admin.reemplazo.vistamesreemplazo');
    }

    public function tablaReemplazoMes()
    {


        return view('backend.admin.reemplazo.tablamesreemplazo');
    }



    public function indexExistencias()
    {
        return view('backend.admin.reportes.existencia.vistaexistencias');
    }




    public function reportePdfExistencias()
    {
        $mpdf = new \Mpdf\Mpdf([
            'tempDir'      => sys_get_temp_dir(),
            'format'       => 'LETTER',
            'orientation'  => 'L',
            'default_font' => 'arial',
        ]);

        $mpdf->SetTitle('Existencias');
        $mpdf->showImageErrors = false;

        $logoalcaldia = public_path('images/logo.png');
        $fechaFormat  = \Carbon\Carbon::now('America/El_Salvador')->format('d-m-Y');

        // ── Existencias (cantidades) ───────────────────────────────────
        $existencias = DB::table('materiales as m')
            ->leftJoin('unidad_medida as um', 'um.id', '=', 'm.id_medida')
            ->leftJoin('marca as ma', 'ma.id', '=', 'm.id_marca')
            ->leftJoin('color as c', 'c.id', '=', 'm.id_color')
            ->leftJoin('talla as t', 't.id', '=', 'm.id_talla')
            ->leftJoinSub(
                DB::table('entradas_detalle')
                    ->select('id_material', DB::raw('SUM(cantidad_inicial) as total_ingresado'))
                    ->groupBy('id_material'),
                'ed', 'ed.id_material', '=', 'm.id'
            )
            ->leftJoinSub(
                DB::table('salidas_detalle as sd')
                    ->join('entradas_detalle as ed2', 'ed2.id', '=', 'sd.id_entrada_detalle')
                    ->select('ed2.id_material', DB::raw('SUM(sd.cantidad_salida) as total_salido'))
                    ->groupBy('ed2.id_material'),
                'sd', 'sd.id_material', '=', 'm.id'
            )
            ->select(
                'm.id',
                'm.codigo',
                'm.nombre as material',
                'um.nombre as unidad',
                'ma.nombre as marca',
                'c.nombre as color',
                't.nombre as talla',
                DB::raw('COALESCE(ed.total_ingresado, 0) as total_ingresado'),
                DB::raw('COALESCE(sd.total_salido, 0) as total_salido'),
                DB::raw('(COALESCE(ed.total_ingresado, 0) - COALESCE(sd.total_salido, 0)) as existencia')
            )
            ->havingRaw('existencia > 0')
            ->orderBy('m.nombre')
            ->get();

        // ── Valor correcto por lote: (cantidad_inicial - salido) × precio ──
        $valoresPorMaterial = DB::table('entradas_detalle as ed')
            ->leftJoin(
                DB::raw('(SELECT id_entrada_detalle, SUM(cantidad_salida) as salido
                      FROM salidas_detalle
                      GROUP BY id_entrada_detalle) as sd'),
                'sd.id_entrada_detalle', '=', 'ed.id'
            )
            ->select(
                'ed.id_material',
                DB::raw('SUM((ed.cantidad_inicial - COALESCE(sd.salido, 0)) * ed.precio) as valor_real')
            )
            ->groupBy('ed.id_material')
            ->pluck('valor_real', 'id_material');

        // ══ ENCABEZADO ═════════════════════════════════════════════════
        $tabla = "
    <table width='100%' style='border-collapse:collapse; font-family:Arial, sans-serif; margin-bottom:6px;'>
        <tr>
            <td style='width:20%; border:0.8px solid #000; padding:6px 8px;'>
                <table width='100%'>
                    <tr>
                        <td style='width:35%; text-align:left;'>
                            <img src='{$logoalcaldia}' style='height:40px'>
                        </td>
                        <td style='width:65%; text-align:left; color:#104e8c;
                                    font-size:12px; font-weight:bold; line-height:1.4;'>
                            SANTA ANA NORTE<br>EL SALVADOR
                        </td>
                    </tr>
                </table>
            </td>
            <td style='width:60%; border-top:0.8px solid #000; border-bottom:0.8px solid #000;
                        padding:8px; text-align:center; vertical-align:middle;'>
                <div style='font-size:16px; font-weight:bold; color:#1a3a6b; letter-spacing:1px;'>
                    REPORTE DE EXISTENCIAS DE E.P.P.
                </div>
                <div style='font-size:11px; color:#555; margin-top:3px;'>
                    Equipo de Protección Personal — Fecha: <strong>{$fechaFormat}</strong>
                </div>
            </td>
            <td style='width:20%; border:0.8px solid #000; padding:0; vertical-align:top;'>
                <table width='100%' style='font-size:10px; border-collapse:collapse;'>
                    <tr>
                        <td style='border-right:0.8px solid #000; border-bottom:0.8px solid #000;
                                    padding:4px 6px; font-weight:bold;'>Código:</td>
                        <td style='border-bottom:0.8px solid #000; padding:4px 6px; text-align:center;'>
                            SEAC-002-FICH
                        </td>
                    </tr>
                    <tr>
                        <td style='border-right:0.8px solid #000; border-bottom:0.8px solid #000;
                                    padding:4px 6px; font-weight:bold;'>Versión:</td>
                        <td style='border-bottom:0.8px solid #000; padding:4px 6px; text-align:center;'>
                            000
                        </td>
                    </tr>
                    <tr>
                        <td style='border-right:0.8px solid #000; padding:4px 6px; font-weight:bold;'>
                            Vigencia:
                        </td>
                        <td style='padding:4px 6px; text-align:center;'>22/10/2025</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
    ";

        // ── Sin datos ──────────────────────────────────────────────────
        if ($existencias->isEmpty()) {
            $tabla .= "
        <div style='text-align:center; margin-top:60px; font-family:Arial, sans-serif;'>
            <p style='font-size:13px; color:#888;'>No se encontraron existencias disponibles.</p>
        </div>
        ";
        } else {

            // ══ TABLA ══════════════════════════════════════════════════
            $tabla .= "
        <table width='100%' style='border-collapse:collapse; font-family:Arial, sans-serif; margin-top:10px;'>
            <thead>
                <tr>
                    <th style='background:#1a3a6b; color:#fff; font-size:10px; font-weight:bold;
                                border:1px solid #000; padding:6px 4px; text-align:center; width:3%;'>#</th>
                    <th style='background:#1a3a6b; color:#fff; font-size:10px; font-weight:bold;
                                border:1px solid #000; padding:6px 4px; text-align:center; width:8%;'>Código</th>
                    <th style='background:#1a3a6b; color:#fff; font-size:10px; font-weight:bold;
                                border:1px solid #000; padding:6px 4px; text-align:center; width:28%;'>Material</th>
                    <th style='background:#1a3a6b; color:#fff; font-size:10px; font-weight:bold;
                                border:1px solid #000; padding:6px 4px; text-align:center; width:12%;'>Marca</th>
                    <th style='background:#1a3a6b; color:#fff; font-size:10px; font-weight:bold;
                                border:1px solid #000; padding:6px 4px; text-align:center; width:9%;'>Color</th>
                    <th style='background:#1a3a6b; color:#fff; font-size:10px; font-weight:bold;
                                border:1px solid #000; padding:6px 4px; text-align:center; width:8%;'>Talla</th>
                    <th style='background:#1a3a6b; color:#fff; font-size:10px; font-weight:bold;
                                border:1px solid #000; padding:6px 4px; text-align:center; width:9%;'>Unidad</th>
                    <th style='background:#1a3a6b; color:#fff; font-size:10px; font-weight:bold;
                                border:1px solid #000; padding:6px 4px; text-align:center; width:10%;'>Existencia</th>
                    <th style='background:#1a3a6b; color:#fff; font-size:10px; font-weight:bold;
                                border:1px solid #000; padding:6px 4px; text-align:center; width:13%;'>Valor ($)</th>
                </tr>
            </thead>
            <tbody>
        ";

            $cont       = 1;
            $totalValor = 0;

            foreach ($existencias as $item) {
                $existencia  = (int)   $item->existencia;
                $valor       = (float) ($valoresPorMaterial[$item->id] ?? 0);
                $totalValor += $valor;

                $tabla .= "
            <tr>
                <td style='border:1px solid #ccc; font-size:10px; padding:5px;
                            text-align:center; color:#666;'>{$cont}</td>
                <td style='border:1px solid #ccc; font-size:10px; padding:5px;
                            text-align:center; font-weight:bold; color:#1a3a6b;'>{$item->codigo}</td>
                <td style='border:1px solid #ccc; font-size:10px; padding:5px;
                            font-weight:600; color:#1a2d55;'>{$item->material}</td>
                <td style='border:1px solid #ccc; font-size:10px; padding:5px;
                            text-align:center;'>" . ($item->marca  ?? '—') . "</td>
                <td style='border:1px solid #ccc; font-size:10px; padding:5px;
                            text-align:center;'>" . ($item->color  ?? '—') . "</td>
                <td style='border:1px solid #ccc; font-size:10px; padding:5px;
                            text-align:center;'>" . ($item->talla  ?? '—') . "</td>
                <td style='border:1px solid #ccc; font-size:10px; padding:5px;
                            text-align:center;'>" . ($item->unidad ?? '—') . "</td>
                <td style='border:1px solid #ccc; font-size:10px; padding:5px;
                            text-align:center;'>{$existencia}</td>
                <td style='border:1px solid #ccc; font-size:10px; padding:5px; text-align:right;'>
                    \$ " . number_format($valor, 2) . "
                </td>
            </tr>
            ";

                $cont++;
            }

            $tabla .= "
            </tbody>
            <tfoot>
                <tr>
                    <td colspan='8' style='border:1px solid #000; padding:7px 8px; text-align:right;
                                            font-size:11px; font-weight:bold;
                                            background:#1a3a6b; color:#fff; letter-spacing:.5px;'>
                        TOTAL GENERAL
                    </td>
                    <td style='border:1px solid #000; padding:7px; text-align:right;
                                font-size:12px; font-weight:bold;
                                background:#1a3a6b; color:#fff;'>
                        \$ " . number_format($totalValor, 2) . "
                    </td>
                </tr>
            </tfoot>
        </table>
        ";
        }

        // ── Generar PDF ────────────────────────────────────────────────
        $stylesheet = file_get_contents(public_path('css/cssbodega.css'));
        $mpdf->WriteHTML($stylesheet, 1);
        $mpdf->setFooter('Página: {PAGENO}/{nb}');
        $mpdf->WriteHTML($tabla, 2);
        $mpdf->Output();
    }






    public function indexHistorialEntradasMateriales()
    {
        return view('backend.admin.historial.todosmaterial.vistatodomaterialentradas');
    }



    public function tablaHistorialEntradasMateriales()
    {
        $arrayEntradas = Entradas::orderBy('fecha', 'desc')->get();
        $pilaIDEntradas = array();
        foreach ($arrayEntradas as $fila) {
            array_push($pilaIDEntradas, $fila->id);
        }

        $arrayEntradasDetalle = EntradasDetalle::whereIn('id_entradas', $pilaIDEntradas)->get();

        foreach ($arrayEntradasDetalle as $fila) {
            $infoEntrada  = Entradas::where('id', $fila->id_entradas)->first();
            $infoMaterial = Materiales::where('id', $fila->id_material)->first();

            $fila->fechaFormat        = date("d-m-Y", strtotime($infoEntrada->fecha));
            $fila->lote               = $infoEntrada->lote;
            $fila->observacion        = $infoEntrada->observacion;
            $fila->nombreMaterial     = $infoMaterial->nombre;
            $fila->precio_ordencompra = (float)($infoEntrada->precio_ordencompra ?? 0);

            $fila->cantidad_entregada = SalidasDetalle::where('id_entrada_detalle', $fila->id)
                ->sum('cantidad_salida');

            $fila->stock_restante     = $fila->cantidad_inicial - $fila->cantidad_entregada;
        }

        return view('backend.admin.historial.todosmaterial.tablatodomaterialentradas', compact('arrayEntradasDetalle'));
    }




















}
