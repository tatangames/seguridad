<?php

namespace App\Http\Controllers\backend\config;

use App\Http\Controllers\Controller;
use App\Models\Color;
use App\Models\Distrito;
use App\Models\Empleado;
use App\Models\Entradas;
use App\Models\EntradasDetalle;
use App\Models\Marca;
use App\Models\Materiales;
use App\Models\Normativa;
use App\Models\Salidas;
use App\Models\SalidasDetalle;
use App\Models\Talla;
use App\Models\UnidadEmpleado;
use App\Models\UnidadMedida;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportesController extends Controller
{
    public function __construct(){
        $this->middleware('auth');
    }

    public function indexGeneralReportes()
    {
        $arrayDistritos = Distrito::orderBy('nombre', 'asc')->get();
        $arrayMateriales = Materiales::orderBy('nombre', 'asc')->get();

        foreach($arrayMateriales as $row) {

            $medida = "";
            $marca = "";
            $normativa = "";
            $color = "";
            $talla = "";

            if ($info = UnidadMedida::where('id', $row->id_medida)->first()) {
                $medida = "(" . $info->nombre . ")";
            }

            if ($info = Marca::where('id', $row->id_marca)->first()) {
                $marca = "(" . $info->nombre . ")";
            }

            if ($info = Normativa::where('id', $row->id_normativa)->first()) {
                $normativa = "(" . $info->nombre . ")";
            }

            if ($info = Color::where('id', $row->id_color)->first()) {
                $color = "(" . $info->nombre . ")";
            }

            if ($info = Talla::where('id', $row->id_talla)->first()) {
                $talla = "(" . $info->nombre . ")";
            }


            $row->nombreCompleto = $row->nombre . '  ' . $medida . '  ' . $marca . '  ' . $normativa . '  ' . $color . '  ' . $talla;
        }


            return view('backend.admin.reportes.general.vistageneralreportes',
            compact('arrayDistritos', 'arrayMateriales'));
    }


    // SALIDAS SEPARADAS
    public function reporteEmpleadoRecibidos($idempleado)
    {
        // ── Validar que existan los datos base ─────────────────────────
        $infoEmpleado = Empleado::find($idempleado);
        if (!$infoEmpleado) abort(404, 'Empleado no encontrado');

        $infoUnidadEmpleado = UnidadEmpleado::find($infoEmpleado->id_unidad_empleado);
        if (!$infoUnidadEmpleado) abort(404, 'Unidad del empleado no encontrada');

        $infoDistrito = Distrito::find($infoUnidadEmpleado->id_distrito);
        if (!$infoDistrito) abort(404, 'Distrito no encontrado');

        // ── Salidas del empleado ───────────────────────────────────────
        $arraySalidas = Salidas::where('id_empleado', $idempleado)
            ->orderBy('fecha', 'ASC')
            ->get();

        $resultsBloque        = [];
        $totalTodosLosBloques = 0;

        foreach ($arraySalidas as $salida) {
            $detalleSalida      = SalidasDetalle::where('id_salida', $salida->id)->get();
            $salida->fechaFormat = date("d-m-Y", strtotime($salida->fecha));
            $sumaBloquesTotal   = 0;

            $detalleSalida->each(function ($item) use (&$sumaBloquesTotal) {
                $entradaDetalle = EntradasDetalle::find($item->id_entrada_detalle);
                $infoEntrada    = Entradas::find($entradaDetalle->id_entradas);
                $item->lote     = $infoEntrada->lote ?? '—';

                $material   = Materiales::find($entradaDetalle->id_material);
                $marca      = Marca::find($material->id_marca);
                $normativa  = Normativa::find($material->id_normativa);
                $unidad     = UnidadMedida::find($material->id_medida);

                $item->nombreMaterial      = $material->nombre      ?? '—';
                $item->nombreMarca         = $marca->nombre         ?? '—';
                $item->nombreNormativa     = $normativa->nombre     ?? '—';
                $item->nombreUnidadMedida  = $unidad->nombre        ?? '—';

                $multiplicado      = $item->cantidad_salida * $entradaDetalle->precio;
                $sumaBloquesTotal += $multiplicado;

                $item->precio      = '$' . number_format($entradaDetalle->precio, 2, '.', ',');
                $item->multiplicado = '$' . number_format($multiplicado,          2, '.', ',');
            });

            $totalTodosLosBloques  += $sumaBloquesTotal;
            $salida->sumaBloquesTotal = '$' . number_format($sumaBloquesTotal, 2, '.', ',');
            $salida->detalle          = $detalleSalida->sortBy('nombreMaterial')->values();
            $resultsBloque[]          = $salida;
        }

        $totalTodosLosBloques = '$' . number_format($totalTodosLosBloques, 2, '.', ',');

        // ── Configuración mPDF ─────────────────────────────────────────
        $mpdf = new \Mpdf\Mpdf(['tempDir' => sys_get_temp_dir(), 'format' => 'LETTER']);
        $mpdf->SetTitle('Entregas');
        $mpdf->showImageErrors = false;

        $logoalcaldia = 'images/logo.png';
        $fechaFormat  = date("d-m-Y", strtotime(Carbon::now('America/El_Salvador')));

        // ══ ENCABEZADO ══════════════════════════════════════════════════
        $tabla = "
    <table width='100%' style='border-collapse:collapse; font-family:Arial, sans-serif;'>
        <tr>
            <td style='width:25%; border:0.8px solid #000; padding:6px 8px;'>
                <table width='100%'>
                    <tr>
                        <td style='width:30%; text-align:left;'>
                            <img src='{$logoalcaldia}' style='height:38px'>
                        </td>
                        <td style='width:70%; text-align:left; color:#104e8c; font-size:13px; font-weight:bold; line-height:1.3;'>
                            SANTA ANA NORTE<br>EL SALVADOR
                        </td>
                    </tr>
                </table>
            </td>
            <td style='width:50%; border-top:0.8px solid #000; border-bottom:0.8px solid #000;
                        padding:6px 8px; text-align:center; font-size:15px; font-weight:bold;'>
                FICHA DE ENTREGA DE EQUIPO DE<br>PROTECCION PERSONAL (E.P.P)
            </td>
            <td style='width:25%; border:0.8px solid #000; padding:0; vertical-align:top;'>
                <table width='100%' style='font-size:10px;'>
                    <tr>
                        <td width='40%' style='border-right:0.8px solid #000; border-bottom:0.8px solid #000; padding:4px 6px;'><strong>Código:</strong></td>
                        <td width='60%' style='border-bottom:0.8px solid #000; padding:4px 6px; text-align:center;'>SEAC-002-FICH</td>
                    </tr>
                    <tr>
                        <td style='border-right:0.8px solid #000; border-bottom:0.8px solid #000; padding:4px 6px;'><strong>Versión:</strong></td>
                        <td style='border-bottom:0.8px solid #000; padding:4px 6px; text-align:center;'>000</td>
                    </tr>
                    <tr>
                        <td style='border-right:0.8px solid #000; padding:4px 6px;'><strong>Fecha de vigencia:</strong></td>
                        <td style='padding:4px 6px; text-align:center;'>22/10/2025</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
    <br>
    ";

        // ══ DATOS DEL EMPLEADO ═══════════════════════════════════════════
        $tabla .= "
    <div style='text-align:center; margin-top:20px;'>
        <h1 style='font-size:15px; margin:0; color:#000;'>ENTREGAS DE MATERIAL</h1>
    </div>
    <div style='text-align:left; margin-top:10px;'>
        <p style='font-size:13px; margin:0;'><strong>Fecha:</strong> {$fechaFormat}</p>
    </div>
    <div style='text-align:left; margin-top:8px;'>
        <p style='font-size:13px; margin:0;'><strong>Distrito:</strong> {$infoDistrito->nombre}</p>
    </div>
    <div style='text-align:left; margin-top:8px;'>
        <p style='font-size:13px; margin:0;'><strong>Unidad:</strong> {$infoUnidadEmpleado->nombre}</p>
    </div>
    <div style='text-align:left; margin-top:8px;'>
        <p style='font-size:13px; margin:0;'><strong>Empleado:</strong> {$infoEmpleado->nombre}</p>
    </div>
    ";

        // ── Sin salidas ────────────────────────────────────────────────
        if (empty($resultsBloque)) {
            $tabla .= "
        <div style='text-align:center; margin-top:40px;'>
            <p style='font-size:13px; color:#888;'>No se encontraron entregas registradas para este empleado.</p>
        </div>
        ";
        }

        // ══ BLOQUES POR SALIDA ════════════════════════════════════════════
        foreach ($resultsBloque as $fila) {

            // — Cabecera del bloque: fecha + descripción —
            $tabla .= "
        <table width='100%' style='margin-top:28px; border-collapse:collapse; font-family:Arial, sans-serif;'>
            <thead>
                <tr>
                    <th style='text-align:center; font-size:11px; width:15%; font-weight:bold;
                                border:1px solid #000; background:#e8eef8; padding:5px;'>Fecha Salida</th>
                    <th style='text-align:center; font-size:11px; width:85%; font-weight:bold;
                                border:1px solid #000; background:#e8eef8; padding:5px;'>Descripción</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style='font-size:11px; border:1px solid #000; padding:5px; text-align:center;'>
                        {$fila->fechaFormat}
                    </td>
                    <td style='font-size:11px; border:1px solid #000; padding:5px;'>
                        {$fila->descripcion}
                    </td>
                </tr>
            </tbody>
        </table>
        ";

            // — Detalle del bloque —
            $tabla .= "
        <table width='100%' style='border-collapse:collapse; font-family:Arial, sans-serif;'>
            <thead>
                <tr>
                    <th style='font-weight:bold; width:11%; font-size:11px; text-align:center;
                                border:1px solid #000; background:#f4f6fb; padding:5px;'>Factura</th>
                    <th style='font-weight:bold; width:22%; font-size:11px; text-align:center;
                                border:1px solid #000; background:#f4f6fb; padding:5px;'>Material</th>
                    <th style='font-weight:bold; width:13%; font-size:11px; text-align:center;
                                border:1px solid #000; background:#f4f6fb; padding:5px;'>Marca</th>
                    <th style='font-weight:bold; width:10%; font-size:11px; text-align:center;
                                border:1px solid #000; background:#f4f6fb; padding:5px;'>U/M</th>
                    <th style='font-weight:bold; width:12%; font-size:11px; text-align:center;
                                border:1px solid #000; background:#f4f6fb; padding:5px;'>Normativa</th>
                    <th style='font-weight:bold; width:10%; font-size:11px; text-align:center;
                                border:1px solid #000; background:#f4f6fb; padding:5px;'>Cantidad</th>
                    <th style='font-weight:bold; width:11%; font-size:11px; text-align:center;
                                border:1px solid #000; background:#f4f6fb; padding:5px;'>Precio</th>
                    <th style='font-weight:bold; width:11%; font-size:11px; text-align:center;
                                border:1px solid #000; background:#f4f6fb; padding:5px;'>Total</th>
                </tr>
            </thead>
            <tbody>
        ";

            foreach ($fila->detalle as $filaDeta) {
                $tabla .= "
            <tr>
                <td style='font-size:11px; border:1px solid #000; padding:5px; text-align:center;'>{$filaDeta->lote}</td>
                <td style='font-size:11px; border:1px solid #000; padding:5px;'>{$filaDeta->nombreMaterial}</td>
                <td style='font-size:11px; border:1px solid #000; padding:5px;'>{$filaDeta->nombreMarca}</td>
                <td style='font-size:11px; border:1px solid #000; padding:5px; text-align:center;'>{$filaDeta->nombreUnidadMedida}</td>
                <td style='font-size:11px; border:1px solid #000; padding:5px; text-align:center;'>{$filaDeta->nombreNormativa}</td>
                <td style='font-size:11px; border:1px solid #000; padding:5px; text-align:center;'>{$filaDeta->cantidad_salida}</td>
                <td style='font-size:11px; border:1px solid #000; padding:5px; text-align:right;'>{$filaDeta->precio}</td>
                <td style='font-size:11px; border:1px solid #000; padding:5px; text-align:right;'>{$filaDeta->multiplicado}</td>
            </tr>
            ";
            }

            // — Subtotal del bloque —
            $tabla .= "
            <tr>
                <td colspan='7' style='font-size:11px; border:1px solid #000; padding:5px;
                                        text-align:right; font-weight:bold; background:#f9f9f9;'>
                    Subtotal:
                </td>
                <td style='font-size:11px; border:1px solid #000; padding:5px;
                            text-align:right; font-weight:bold; background:#f9f9f9;'>
                    {$fila->sumaBloquesTotal}
                </td>
            </tr>
            </tbody>
        </table>
        ";
        }

        // ══ TOTAL GENERAL ════════════════════════════════════════════════
        $tabla .= "
    <div style='text-align:right; margin-top:20px; margin-right:4px;'>
        <p style='font-size:14px; margin:0; color:#000;'>
            <strong>Total General: {$totalTodosLosBloques}</strong>
        </p>
    </div>
    ";

        // ══ GENERAR PDF ══════════════════════════════════════════════════
        $stylesheet = file_get_contents('css/cssbodega.css');
        $mpdf->WriteHTML($stylesheet, 1);
        $mpdf->setFooter('Página: {PAGENO}/{nb}');
        $mpdf->WriteHTML($tabla, 2);
        $mpdf->Output();
    }


    public function reporteKardexMaterial($idmaterial)
    {
        // ── Validar material ───────────────────────────────────────────
        $infoMaterial = Materiales::with(['marca', 'normativa', 'color', 'talla'])
            ->find($idmaterial);

        if (!$infoMaterial) abort(404, 'Material no encontrado');

        $marca     = optional($infoMaterial->marca)->nombre     ?? '—';
        $normativa = optional($infoMaterial->normativa)->nombre ?? '—';
        $color     = optional($infoMaterial->color)->nombre     ?? '—';
        $talla     = optional($infoMaterial->talla)->nombre     ?? '—';

        // ── Entradas del material ──────────────────────────────────────
        $entradas = DB::table('entradas_detalle as ed')
            ->join('entradas as e', 'e.id', '=', 'ed.id_entradas')
            ->select(
                'e.id',
                'e.fecha',
                'e.descripcion',
                'e.lote',
                DB::raw('SUM(ed.cantidad_inicial) as cantidadInicial')
            )
            ->where('ed.id_material', $idmaterial)
            ->groupBy('e.id', 'e.fecha', 'e.descripcion', 'e.lote')
            ->orderBy('e.fecha', 'ASC')
            ->get();

        // ── Salidas del material ───────────────────────────────────────
        $salidas = DB::table('salidas_detalle as sd')
            ->join('entradas_detalle as ed', 'sd.id_entrada_detalle', '=', 'ed.id')
            ->join('salidas as s', 's.id', '=', 'sd.id_salida')
            ->join('empleado as em', 'em.id', '=', 's.id_empleado')
            ->join('unidad_empleado as ue', 'ue.id', '=', 'em.id_unidad_empleado')
            ->join('distrito as d', 'd.id', '=', 'ue.id_distrito')
            ->select(
                'ed.id_entradas',
                's.fecha',
                'sd.cantidad_salida',
                'em.nombre as nombreEmpleado',
                'ue.nombre as nombreUnidad',
                'd.nombre as nombreDistrito'
            )
            ->where('ed.id_material', $idmaterial)
            ->orderBy('s.fecha', 'ASC')
            ->get()
            ->groupBy('id_entradas');

        // ── Totales y enriquecer entradas ──────────────────────────────
        $sumaTotalRecibido  = 0;
        $sumaTotalEntregado = 0;

        foreach ($entradas as $entrada) {
            $entrada->fechaFormat = date('d-m-Y', strtotime($entrada->fecha));
            $entrada->detalle     = $salidas[$entrada->id] ?? collect();

            $sumaTotalRecibido += $entrada->cantidadInicial;

            $subtotalSalidas = 0;
            foreach ($entrada->detalle as $salida) {
                $salida->fechaFormat  = date('d-m-Y', strtotime($salida->fecha));
                $subtotalSalidas     += $salida->cantidad_salida;
                $sumaTotalEntregado  += $salida->cantidad_salida;
            }
            $entrada->subtotalSalidas = $subtotalSalidas;
            // Stock después de esta entrada = recibido - salidas de esa entrada
            $entrada->stockEntrada = $entrada->cantidadInicial - $subtotalSalidas;
        }

        $totalBodega = $sumaTotalRecibido - $sumaTotalEntregado;

        // ── PDF ────────────────────────────────────────────────────────
        $mpdf = new \Mpdf\Mpdf([
            'tempDir'      => sys_get_temp_dir(),
            'format'       => 'LETTER-L',
            'default_font' => 'arial',
        ]);
        $mpdf->SetTitle('Reporte Kardex');
        $mpdf->showImageErrors = false;

        $logoalcaldia = 'images/logo.png';
        $fechaFormat  = now()->format('d-m-Y');

        // ══ ENCABEZADO ═════════════════════════════════════════════════
        $tabla = "
    <table width='100%' style='border-collapse:collapse; font-family:Arial, sans-serif;'>
        <tr>
            <td style='width:25%; border:0.8px solid #000; padding:6px 8px;'>
                <table width='100%'>
                    <tr>
                        <td style='width:30%; text-align:left;'>
                            <img src='{$logoalcaldia}' style='height:38px'>
                        </td>
                        <td style='width:70%; text-align:left; color:#104e8c; font-size:13px; font-weight:bold; line-height:1.3;'>
                            SANTA ANA NORTE<br>EL SALVADOR
                        </td>
                    </tr>
                </table>
            </td>
            <td style='width:50%; border-top:0.8px solid #000; border-bottom:0.8px solid #000;
                        padding:6px 8px; text-align:center; font-size:15px; font-weight:bold;'>
                KARDEX DE MATERIAL<br>
                <span style='font-size:12px; font-weight:normal;'>Control de Entradas y Salidas</span>
            </td>
            <td style='width:25%; border:0.8px solid #000; padding:0; vertical-align:top;'>
                <table width='100%' style='font-size:10px;'>
                    <tr>
                        <td width='40%' style='border-right:0.8px solid #000; border-bottom:0.8px solid #000; padding:4px 6px;'><strong>Código:</strong></td>
                        <td width='60%' style='border-bottom:0.8px solid #000; padding:4px 6px; text-align:center;'>SEAC-002-FICH</td>
                    </tr>
                    <tr>
                        <td style='border-right:0.8px solid #000; border-bottom:0.8px solid #000; padding:4px 6px;'><strong>Versión:</strong></td>
                        <td style='border-bottom:0.8px solid #000; padding:4px 6px; text-align:center;'>000</td>
                    </tr>
                    <tr>
                        <td style='border-right:0.8px solid #000; padding:4px 6px;'><strong>Fecha de vigencia:</strong></td>
                        <td style='padding:4px 6px; text-align:center;'>22/10/2025</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
    <br>
    ";

        // ══ DATOS DEL MATERIAL ══════════════════════════════════════════
        $tabla .= "
    <table width='100%' style='border-collapse:collapse; font-family:Arial, sans-serif;
                                margin-bottom:16px; font-size:12px;'>
        <tr>
            <td style='width:50%; padding:4px 0;'>
                <strong>Fecha Generado:</strong> {$fechaFormat}
            </td>
            <td style='width:50%; padding:4px 0;'>
                <strong>Código Material:</strong> {$infoMaterial->codigo}
            </td>
        </tr>
        <tr>
            <td colspan='2' style='padding:4px 0;'>
                <strong>Material:</strong> {$infoMaterial->nombre}
            </td>
        </tr>
        <tr>
            <td style='padding:4px 0;'><strong>Marca:</strong> {$marca}</td>
            <td style='padding:4px 0;'><strong>Normativa:</strong> {$normativa}</td>
        </tr>
        <tr>
            <td style='padding:4px 0;'><strong>Color:</strong> {$color}</td>
            <td style='padding:4px 0;'><strong>Talla:</strong> {$talla}</td>
        </tr>
    </table>
    ";

        // ── Sin entradas ───────────────────────────────────────────────
        if ($entradas->isEmpty()) {
            $tabla .= "
        <div style='text-align:center; margin-top:40px;'>
            <p style='font-size:13px; color:#888;'>No se encontraron entradas registradas para este material.</p>
        </div>
        ";
        }

        // ══ BLOQUES POR ENTRADA ════════════════════════════════════════
        foreach ($entradas as $entrada) {

            // — Cabecera entrada —
            $tabla .= "
        <table width='100%' style='border-collapse:collapse; font-family:Arial, sans-serif; margin-top:22px;'>
            <thead>
                <tr>
                    <th colspan='4' style='background:#1a3a6b; color:#fff; font-size:11px;
                                            padding:6px 8px; text-align:left; border:1px solid #000;'>
                        ▼ ENTRADA
                    </th>
                </tr>
                <tr>
                    <th style='border:1px solid #000; background:#e8eef8; font-size:11px;
                                padding:5px; text-align:center; width:15%;'>Fecha</th>
                    <th style='border:1px solid #000; background:#e8eef8; font-size:11px;
                                padding:5px; text-align:center; width:20%;'>Lote / Factura</th>
                    <th style='border:1px solid #000; background:#e8eef8; font-size:11px;
                                padding:5px; text-align:center; width:10%;'>Cantidad Recibida</th>
                    <th style='border:1px solid #000; background:#e8eef8; font-size:11px;
                                padding:5px; text-align:center; width:55%;'>Descripción</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style='border:1px solid #000; font-size:11px; padding:5px; text-align:center;'>
                        {$entrada->fechaFormat}
                    </td>
                    <td style='border:1px solid #000; font-size:11px; padding:5px; text-align:center;'>
                        {$entrada->lote}
                    </td>
                    <td style='border:1px solid #000; font-size:11px; padding:5px; text-align:center;
                                font-weight:bold; color:#155724;'>
                        {$entrada->cantidadInicial}
                    </td>
                    <td style='border:1px solid #000; font-size:11px; padding:5px;'>
                        {$entrada->descripcion}
                    </td>
                </tr>
            </tbody>
        </table>
        ";

            // — Salidas de esa entrada —
            if ($entrada->detalle->count() > 0) {

                $tabla .= "
            <table width='100%' style='border-collapse:collapse; font-family:Arial, sans-serif;'>
                <thead>
                    <tr>
                        <th colspan='5' style='background:#28a745; color:#fff; font-size:11px;
                                                padding:5px 8px; text-align:left; border:1px solid #000;'>
                            ▼ SALIDAS
                        </th>
                    </tr>
                    <tr>
                        <th style='border:1px solid #000; background:#f4f6fb; font-size:11px;
                                    padding:5px; text-align:center; width:13%;'>Fecha</th>
                        <th style='border:1px solid #000; background:#f4f6fb; font-size:11px;
                                    padding:5px; text-align:center; width:20%;'>Distrito</th>
                        <th style='border:1px solid #000; background:#f4f6fb; font-size:11px;
                                    padding:5px; text-align:center; width:27%;'>Unidad</th>
                        <th style='border:1px solid #000; background:#f4f6fb; font-size:11px;
                                    padding:5px; text-align:center; width:30%;'>Empleado</th>
                        <th style='border:1px solid #000; background:#f4f6fb; font-size:11px;
                                    padding:5px; text-align:center; width:10%;'>Cantidad</th>
                    </tr>
                </thead>
                <tbody>
            ";

                foreach ($entrada->detalle as $salida) {
                    $tabla .= "
                <tr>
                    <td style='border:1px solid #000; font-size:11px; padding:5px; text-align:center;'>
                        {$salida->fechaFormat}
                    </td>
                    <td style='border:1px solid #000; font-size:11px; padding:5px;'>
                        {$salida->nombreDistrito}
                    </td>
                    <td style='border:1px solid #000; font-size:11px; padding:5px;'>
                        {$salida->nombreUnidad}
                    </td>
                    <td style='border:1px solid #000; font-size:11px; padding:5px;'>
                        {$salida->nombreEmpleado}
                    </td>
                    <td style='border:1px solid #000; font-size:11px; padding:5px; text-align:center;
                                color:#721c24; font-weight:bold;'>
                        {$salida->cantidad_salida}
                    </td>
                </tr>
                ";
                }

                // Subtotal salidas + stock restante de esa entrada
                $tabla .= "
                    <tr>
                        <td colspan='4' style='border:1px solid #000; font-size:11px; padding:5px;
                                                text-align:right; font-weight:bold; background:#fff3cd;'>
                            Total salidas de esta entrada:
                        </td>
                        <td style='border:1px solid #000; font-size:11px; padding:5px;
                                    text-align:center; font-weight:bold; background:#fff3cd;
                                    color:#856404;'>
                            {$entrada->subtotalSalidas}
                        </td>
                    </tr>
                    <tr>
                        <td colspan='4' style='border:1px solid #000; font-size:11px; padding:5px;
                                                text-align:right; font-weight:bold; background:#d4edda;'>
                            Stock restante de esta entrada:
                        </td>
                        <td style='border:1px solid #000; font-size:11px; padding:5px;
                                    text-align:center; font-weight:bold; background:#d4edda;
                                    color:#155724;'>
                            {$entrada->stockEntrada}
                        </td>
                    </tr>
                </tbody>
            </table>
            ";

            } else {
                // Sin salidas para esta entrada
                $tabla .= "
            <table width='100%' style='border-collapse:collapse; font-family:Arial, sans-serif;'>
                <tr>
                    <td style='border:1px solid #ccc; font-size:11px; padding:6px 10px;
                                color:#888; font-style:italic; background:#f9f9f9;'>
                        Sin salidas registradas para esta entrada.
                        Stock disponible: <strong>{$entrada->cantidadInicial}</strong>
                    </td>
                </tr>
            </table>
            ";
            }
        }

        // ══ RESUMEN FINAL ═══════════════════════════════════════════════
        $tabla .= "
    <table width='40%' style='border-collapse:collapse; font-family:Arial, sans-serif;
                               margin-top:30px; margin-left:auto; font-size:12px;'>
        <tr>
            <td style='border:1px solid #000; padding:6px 10px; background:#e8eef8; font-weight:bold;'>
                Total Recibido
            </td>
            <td style='border:1px solid #000; padding:6px 10px; text-align:center;
                        font-weight:bold; color:#155724;'>
                {$sumaTotalRecibido}
            </td>
        </tr>
        <tr>
            <td style='border:1px solid #000; padding:6px 10px; background:#e8eef8; font-weight:bold;'>
                Total Entregado
            </td>
            <td style='border:1px solid #000; padding:6px 10px; text-align:center;
                        font-weight:bold; color:#721c24;'>
                {$sumaTotalEntregado}
            </td>
        </tr>
        <tr>
            <td style='border:1px solid #000; padding:6px 10px; background:#1a3a6b;
                        color:#fff; font-weight:bold;'>
                Stock Actual en Bodega
            </td>
            <td style='border:1px solid #000; padding:6px 10px; text-align:center;
                        font-weight:bold; background:#1a3a6b; color:#fff; font-size:14px;'>
                {$totalBodega}
            </td>
        </tr>
    </table>
    ";

        // ── Generar ────────────────────────────────────────────────────
        $stylesheet = file_get_contents('css/cssbodega.css');
        $mpdf->WriteHTML($stylesheet, 1);
        $mpdf->setFooter('Página {PAGENO}/{nb}');
        $mpdf->WriteHTML($tabla, 2);
        $mpdf->Output();
    }











}
