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
        $infoEmpleado = Empleado::where('id', $idempleado)->first();
        $infoUnidadEmpleado = UnidadEmpleado::where('id', $infoEmpleado->id_unidad_empleado)->first();
        $infoDistrito = Distrito::where('id', $infoUnidadEmpleado->id_distrito)->first();

        $arraySalidas = Salidas::where('id_empleado', $idempleado)
            ->orderBy('fecha', 'ASC')
            ->get();

        $resultsBloque = [];
        $totalTodosLosBloques = 0;

        foreach ($arraySalidas as $salida) {
            $detalleSalida = SalidasDetalle::where('id_salida', $salida->id)->get();

            $salida->fechaFormat = date("d-m-Y", strtotime($salida->fecha));

           // $infoDistrito = Distrito::where('id', $salida->id_distrito)->first();
            $salida->nombreDistrito = "xx";


            $sumaBloquesTotal = 0;

            // Añadir información extra a cada detalle
            $detalleSalida->each(function ($item) use (&$sumaBloquesTotal) {
                $entradaDetalle = EntradasDetalle::find($item->id_entrada_detalle);
                $infoEntrada = Entradas::where('id', $entradaDetalle->id_entradas)->first();
                $item->lote = $infoEntrada->lote;


                $material = Materiales::find($entradaDetalle->id_material);
                $marca = Marca::find($material->id_marca);
                $normativa = Normativa::find($material->id_normativa);
                $unidad = UnidadMedida::find($material->id_medida);

                $item->nombreMaterial = $material->nombre;
                $item->nombreMarca = $marca->nombre;
                $item->nombreNormativa = $normativa->nombre;
                $item->nombreUnidadMedida = $unidad->nombre;

                $multiplicado = $item->cantidad_salida * $entradaDetalle->precio;
                $sumaBloquesTotal += $multiplicado;

                $item->precio = "$" . number_format($entradaDetalle->precio, 2, '.', ',');
                $item->multiplicado = "$" . number_format($multiplicado, 2, '.', ',');
            });

            $totalTodosLosBloques += $sumaBloquesTotal;
            $salida->sumaBloquesTotal = "$" . number_format($sumaBloquesTotal, 2, '.', ',');

            // Ordenar detalles por nombreMaterial
            $detalleSalida = $detalleSalida->sortBy('nombreMaterial')->values();

            // Agregar salida con su detalle ordenado
            $salida->detalle = $detalleSalida;
            $resultsBloque[] = $salida;
        }


        $totalTodosLosBloques = "$" . number_format($totalTodosLosBloques, 2, '.', ',');


        //$mpdf = new \Mpdf\Mpdf(['format' => 'LETTER']);
        $mpdf = new \Mpdf\Mpdf(['tempDir' => sys_get_temp_dir(), 'format' => 'LETTER']);

        $mpdf->SetTitle('Entregas');

        // mostrar errores
        $mpdf->showImageErrors = false;

        $logoalcaldia = 'images/gobiernologo.jpg';
        $logosantaana = 'images/logo.png';

        $fechaFormat = date("d-m-Y", strtotime(Carbon::now('America/El_Salvador')));





        $tabla = "
           <table width='100%' style='border-collapse:collapse; font-family: Arial, sans-serif;'>
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
                <td style='width:50%; border-top:0.8px solid #000; border-bottom:0.8px solid #000; padding:6px 8px; text-align:center; font-size:15px; font-weight:bold;'>
                    FICHA DE ENTREGA DE EQUIPO DE<br>
                    PROTECCION PERSONAL (E.P.P)
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
        <br>";





















        $tabla .= "
            <div style='text-align: center; margin-top: 20px;'>
                <h1 style='font-size: 15px; margin: 0; color: #000;'>ENTREGAS DE MATERIAL</h1>
            </div>
            <div style='text-align: left; margin-top: 10px;'>
                <p style='font-size: 13px; margin: 0; color: #000;'><strong>Fecha:</strong> $fechaFormat</p>
            </div>
             <div style='text-align: left; margin-top: 10px;'>
                <p style='font-size: 13px; margin: 0; color: #000;'><strong>Distrito:</strong> $infoDistrito->nombre</p>
            </div>
              <div style='text-align: left; margin-top: 10px;'>
                <p style='font-size: 13px; margin: 0; color: #000;'><strong>Unidad:</strong> $infoUnidadEmpleado->nombre</p>
            </div>
              <div style='text-align: left; margin-top: 10px;'>
                <p style='font-size: 13px; margin: 0; color: #000;'><strong>Empleado:</strong> $infoEmpleado->nombre</p>
            </div>
      ";


        foreach ($resultsBloque as $fila){
            $tabla .= "<table width='100%' id='tablaFor' style='margin-top: 30px'>
            <thead>
                <tr>
                    <th style='text-align: center; font-size:12px; width: 12%; font-weight: bold; border: 1px solid black;'>Fecha Salida</th>
                    <th style='text-align: center; font-size:12px; width: 12%; font-weight: bold; border: 1px solid black;'>Descripción</th>
                </tr>
            </thead>
            <tbody>";

            $tabla .= "<tr>
                    <td style='font-size: 11px; font-weight: normal'>$fila->fechaFormat</td>
                    <td style='font-size: 11px; font-weight: normal'>$fila->descripcion</td>
                </tr>";

            $tabla .= "</tbody></table>";

            $tabla .= "<table width='100%' id='tablaFor'>
                    <thead>
                        <tr>
                            <th style='font-weight: bold; width: 11%; font-size: 12px; text-align: center;'>Factura</th>
                            <th style='font-weight: bold; width: 22%; font-size: 12px; text-align: center;'>Material</th>
                            <th style='font-weight: bold; width: 13%; font-size: 12px; text-align: center;'>Marca</th>
                            <th style='font-weight: bold; width: 12%; font-size: 12px; text-align: center;'>U/M</th>
                            <th style='font-weight: bold; width: 12%; font-size: 12px; text-align: center;'>Normativa</th>
                            <th style='font-weight: bold; width: 12%; font-size: 12px; text-align: center;'>Cantidad</th>
                            <th style='font-weight: bold; width: 10%; font-size: 12px; text-align: center;'>Precio</th>
                            <th style='font-weight: bold; width: 10%; font-size: 12px; text-align: center;'>Total</th>
                        </tr>
                    </thead>
                    <tbody>";

            foreach ($fila->detalle as $filaDeta){

                $tabla .= "<tr>
                    <td style='font-size: 11px; font-weight: normal'>$filaDeta->lote</td>
                    <td style='font-size: 11px; font-weight: normal'>$filaDeta->nombreMaterial</td>
                    <td style='font-size: 11px; font-weight: normal'>$filaDeta->nombreMarca</td>
                    <td style='font-size: 11px; font-weight: normal'>$filaDeta->nombreUnidadMedida</td>
                    <td style='font-size: 11px; font-weight: normal'>$filaDeta->nombreNormativa</td>
                    <td style='font-size: 11px; font-weight: normal'>$filaDeta->cantidad_salida</td>
                    <td style='font-size: 11px; font-weight: normal'>$filaDeta->precio</td>
                    <td style='font-size: 11px; font-weight: normal'>$filaDeta->multiplicado</td>
                </tr>";
            }

            $tabla .= "<tr>
                    <td colspan='7' style='font-size: 11px; font-weight: bold'>Total</td>
                    <td style='font-size: 11px; font-weight: bold'>$fila->sumaBloquesTotal</td>
                </tr>";


            $tabla .= "</tbody></table>";
        }

        $tabla .= "
            <div style='text-align: left; margin-top: 35px; margin-left: 15px'>
                <p style='font-size: 15px; margin: 0; color: #000;'><strong>Total: $totalTodosLosBloques </strong> </p>
            </div>

      ";


        $stylesheet = file_get_contents('css/cssbodega.css');
        $mpdf->WriteHTML($stylesheet,1);

        $mpdf->setFooter("Página: " . '{PAGENO}' . "/" . '{nb}');
        $mpdf->WriteHTML($tabla,2);

        $mpdf->Output();
    }




    public function reporteKardexMaterial($idmaterial)
    {
        $infoMaterial = Materiales::with([
            'marca',
            'normativa',
            'color',
            'talla'
        ])->findOrFail($idmaterial);

        $marca = optional($infoMaterial->marca)->nombre;
        $normativa = optional($infoMaterial->normativa)->nombre;
        $color = optional($infoMaterial->color)->nombre;
        $talla = optional($infoMaterial->talla)->nombre;

        // 🔹 TODAS LAS ENTRADAS DEL MATERIAL
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

        // 🔹 TODAS LAS SALIDAS DEL MATERIAL
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

        $sumaTotalRecibido = 0;
        $sumaTotalEntregado = 0;

        foreach ($entradas as $entrada) {

            $entrada->fechaFormat = date("d-m-Y", strtotime($entrada->fecha));
            $entrada->detalle = $salidas[$entrada->id] ?? collect();

            $sumaTotalRecibido += $entrada->cantidadInicial;

            foreach ($entrada->detalle as $salida) {
                $salida->fechaFormat = date("d-m-Y", strtotime($salida->fecha));
                $sumaTotalEntregado += $salida->cantidad_salida;
            }
        }

        $totalBodega = $sumaTotalRecibido - $sumaTotalEntregado;

        // ---------------- PDF ------------------

        $mpdf = new \Mpdf\Mpdf([
            'tempDir' => sys_get_temp_dir(),
            'format' => 'LETTER-L',
            'default_font' => 'arial'
        ]);

        $mpdf->SetTitle('Reporte Kardex');
        $mpdf->showImageErrors = false;

        $logoalcaldia = 'images/logo.png';

        $fechaFormat = now()->format('d-m-Y');

        $tabla = "
           <table width='100%' style='border-collapse:collapse; font-family: Arial, sans-serif;'>
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
                <td style='width:50%; border-top:0.8px solid #000; border-bottom:0.8px solid #000; padding:6px 8px; text-align:center; font-size:15px; font-weight:bold;'>
                    FICHA DE ENTREGA DE EQUIPO DE<br>
                    PROTECCION PERSONAL (E.P.P)
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
        <br>";

        $tabla .= "
        <p><strong>Fecha Generado:</strong> $fechaFormat</p>
        <p><strong>Material:</strong> {$infoMaterial->nombre}</p>
        <p><strong>Código:</strong> {$infoMaterial->codigo}</p>
        <p><strong>Marca:</strong> $marca</p>
        <p><strong>Normativa:</strong> $normativa</p>
        <p><strong>Color:</strong> $color</p>
        <p><strong>Talla:</strong> $talla</p>
    ";

        foreach ($entradas as $entrada) {

            $tabla .= "
            <h4 style='margin-top:25px;'>ENTRADA</h4>
            <table style='width:100%; border-collapse: collapse;' cellpadding='5'>
                <tr>
                    <th style='border:1px solid #000;'>Fecha</th>
                    <th style='border:1px solid #000;'>Lote</th>
                    <th style='border:1px solid #000;'>Recibido</th>
                    <th style='border:1px solid #000;'>Descripción</th>
                </tr>
                <tr>
                    <td style='border:1px solid #000;'>{$entrada->fechaFormat}</td>
                    <td style='border:1px solid #000;'>{$entrada->lote}</td>
                    <td style='border:1px solid #000;'>{$entrada->cantidadInicial}</td>
                    <td style='border:1px solid #000;'>{$entrada->descripcion}</td>
                </tr>
            </table>";

            if ($entrada->detalle->count() > 0) {

                $tabla .= "
                <h4 style='margin-top:15px;'>SALIDAS</h4>
                <table style='width:100%; border-collapse: collapse;' cellpadding='5'>
                    <tr>
                        <th style='border:1px solid #000;'>Fecha</th>
                        <th style='border:1px solid #000;'>Distrito</th>
                        <th style='border:1px solid #000;'>Unidad</th>
                        <th style='border:1px solid #000;'>Empleado</th>
                        <th style='border:1px solid #000;'>Cantidad</th>
                    </tr>";

                                foreach ($entrada->detalle as $salida) {

                                    $tabla .= "
                    <tr>
                        <td style='border:1px solid #000;'>{$salida->fechaFormat}</td>
                        <td style='border:1px solid #000;'>{$salida->nombreDistrito}</td>
                        <td style='border:1px solid #000;'>{$salida->nombreUnidad}</td>
                        <td style='border:1px solid #000;'>{$salida->nombreEmpleado}</td>
                        <td style='border:1px solid #000; text-align:center;'>{$salida->cantidad_salida}</td>
                    </tr>";
                }

                $tabla .= "</table>";
            }
        }

        $tabla .= "
        <div style='margin-top:30px; font-size:14px;'>
            <p><strong>Total Recibido:</strong> $sumaTotalRecibido</p>
            <p><strong>Total Entregado:</strong> $sumaTotalEntregado</p>
            <p><strong>Total Actual en Bodega:</strong> $totalBodega</p>
        </div>
    ";

        $stylesheet = file_get_contents('css/cssbodega.css');
        $mpdf->WriteHTML($stylesheet,1);
        $mpdf->setFooter("Página {PAGENO}/{nb}");
        $mpdf->WriteHTML($tabla,2);

        $mpdf->Output();
    }













}
