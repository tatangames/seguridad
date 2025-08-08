<?php

namespace App\Http\Controllers\backend\config;

use App\Http\Controllers\Controller;
use App\Models\Empleado;
use App\Models\Entradas;
use App\Models\EntradasDetalle;
use App\Models\Marca;
use App\Models\Materiales;
use App\Models\Normativa;
use App\Models\Salidas;
use App\Models\SalidasDetalle;
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
        $arrayEmpleados = Empleado::orderBy('nombre', 'asc')->get();

        foreach ($arrayEmpleados as $item) {

            $item->nombreFull = $item->nombre . " (" . $item->unidad . ")";
        }


        return view('backend.admin.reportes.general.vistageneralreportes', compact('arrayEmpleados'));
    }


    // SALIDAS SEPARADAS
    public function reporteEncargadoRecibeSeparados($idencargado)
    {
        $infoEncargado = Encargado::where('id', $idencargado)->first();

        $arraySalidas = Salidas::where('id_encargado', $idencargado)
            ->orderBy('fecha', 'ASC')
            ->get();

        $resultsBloque = [];
        $totalTodosLosBloques = 0;

        foreach ($arraySalidas as $salida) {
            $detalleSalida = SalidasDetalle::where('id_salida', $salida->id)->get();

            $salida->fechaFormat = date("d-m-Y", strtotime($salida->fecha));

            $infoDistrito = Distrito::where('id', $salida->id_distrito)->first();
            $salida->nombreDistrito = $infoDistrito->nombre;


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
            <table style='width: 100%; border-collapse: collapse;'>
                <tr>
                    <!-- Logo izquierdo -->
                    <td style='width: 15%; text-align: left;'>
                        <img src='$logosantaana' alt='Santa Ana Norte' style='max-width: 100px; height: auto;'>
                    </td>
                    <!-- Texto centrado -->
                    <td style='width: 60%; text-align: center;'>
                        <h1 style='font-size: 16px; margin: 0; color: #003366; text-transform: uppercase;'>ALCALDÍA MUNICIPAL DE SANTA ANA NORTE</h1>
                        <h2 style='font-size: 14px; margin: 0; color: #003366; text-transform: uppercase;'></h2>
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
            <div style='text-align: center; margin-top: 20px;'>
                <h1 style='font-size: 15px; margin: 0; color: #000;'>ENTREGAS DE MATERIAL</h1>
            </div>
            <div style='text-align: left; margin-top: 10px;'>
                <p style='font-size: 13px; margin: 0; color: #000;'><strong>Fecha:</strong> $fechaFormat</p>
            </div>
             <div style='text-align: left; margin-top: 10px;'>
                <p style='font-size: 13px; margin: 0; color: #000;'><strong>Encargado:</strong> $infoEncargado->nombre</p>
            </div>
      ";


        foreach ($resultsBloque as $fila){

            $tabla .= "<table width='100%' id='tablaFor' style='margin-top: 30px'>
            <thead>
                <tr>
                    <th style='text-align: center; font-size:12px; width: 12%; font-weight: bold; border: 1px solid black;'>Fecha Salida</th>
                    <th style='text-align: center; font-size:12px; width: 12%; font-weight: bold; border: 1px solid black;'>Distrito</th>
                    <th style='text-align: center; font-size:12px; width: 12%; font-weight: bold; border: 1px solid black;'>Descripción</th>
                </tr>
            </thead>
            <tbody>";

            $tabla .= "<tr>
                    <td style='font-size: 11px; font-weight: normal'>$fila->fechaFormat</td>
                    <td style='font-size: 11px; font-weight: normal'>$fila->nombreDistrito</td>
                    <td style='font-size: 11px; font-weight: normal'>$fila->descripcion</td>
                </tr>";

            $tabla .= "</tbody></table>";

            $tabla .= "<table width='100%' id='tablaFor'>
                    <thead>
                        <tr>
                            <th style='font-weight: bold; width: 8%; font-size: 12px; text-align: center;'>LOTE</th>
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





    public function reporteEncargadoRecibeJuntos($idencargado)
    {
        $infoEncargado = Encargado::where('id', $idencargado)->first();

        $arraySalidasAgrupadas = DB::table('salidas AS sa')
            ->join('salidas_detalle AS sadeta', 'sadeta.id_salida', '=', 'sa.id')
            ->join('entradas_detalle AS entradeta', 'sadeta.id_entrada_detalle', '=', 'entradeta.id')
            ->select(
                'entradeta.id_material',
                DB::raw('SUM(sadeta.cantidad_salida) AS total_salida'),
            )
            ->where('sa.id_encargado', $idencargado)
            ->groupBy('entradeta.id_material')
            ->get();


        foreach ($arraySalidasAgrupadas as $fila) {
            $infoMaterial = Materiales::where('id', $fila->id_material)->first();
            $infoMarca = Marca::where('id', $infoMaterial->id_marca)->first();
            $infoNormativa = Normativa::where('id', $infoMaterial->id_normativa)->first();
            $infoUnidad = UnidadMedida::where('id', $infoMaterial->id_medida)->first();

            $fila->nombreMaterial = $infoMaterial->nombre;
            $fila->nombreMarca = $infoMarca->nombre;
            $fila->nombreNormativa = $infoNormativa->nombre;
            $fila->nombreUnidadMedida = $infoUnidad->nombre;
        }


        //$mpdf = new \Mpdf\Mpdf(['format' => 'LETTER']);
        $mpdf = new \Mpdf\Mpdf(['tempDir' => sys_get_temp_dir(), 'format' => 'LETTER']);

        $mpdf->SetTitle('Entregas');

        // mostrar errores
        $mpdf->showImageErrors = false;

        $logoalcaldia = 'images/gobiernologo.jpg';
        $logosantaana = 'images/logo.png';

        $fechaFormat = date("d-m-Y", strtotime(Carbon::now('America/El_Salvador')));


        $tabla = "
            <table style='width: 100%; border-collapse: collapse;'>
                <tr>
                    <!-- Logo izquierdo -->
                    <td style='width: 15%; text-align: left;'>
                        <img src='$logosantaana' alt='Santa Ana Norte' style='max-width: 100px; height: auto;'>
                    </td>
                    <!-- Texto centrado -->
                    <td style='width: 60%; text-align: center;'>
                        <h1 style='font-size: 16px; margin: 0; color: #003366; text-transform: uppercase;'>ALCALDÍA MUNICIPAL DE SANTA ANA NORTE</h1>
                        <h2 style='font-size: 14px; margin: 0; color: #003366; text-transform: uppercase;'></h2>
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
            <div style='text-align: center; margin-top: 20px;'>
                <h1 style='font-size: 15px; margin: 0; color: #000;'>ENTREGAS DE MATERIAL</h1>
            </div>
            <div style='text-align: left; margin-top: 10px;'>
                <p style='font-size: 13px; margin: 0; color: #000;'><strong>Fecha:</strong> $fechaFormat</p>
            </div>
             <div style='text-align: left; margin-top: 10px;'>
                <p style='font-size: 13px; margin: 0; color: #000;'><strong>Encargado:</strong> $infoEncargado->nombre</p>
            </div>
      ";

        $tabla .= "<table id='tablaFor' style='width: 100%; border-collapse: collapse; margin-top: 35px'>
        <tbody>
            <tr>
                <th style='text-align: center; font-size:10px; width: 12%; font-weight: bold; border: 1px solid black;'>Material</th>
                 <th style='text-align: center; font-size:10px; width: 12%; font-weight: bold; border: 1px solid black;'>U.M</th>
                 <th style='text-align: center; font-size:10px; width: 12%; font-weight: bold; border: 1px solid black;'>Marca</th>
                 <th style='text-align: center; font-size:10px; width: 12%; font-weight: bold; border: 1px solid black;'>Normativa</th>
                <th style='text-align: center; font-size:10px; width: 10%; font-weight: bold; border: 1px solid black;'>Cantidad Entregada</th>
            </tr>
        ";

        foreach ($arraySalidasAgrupadas as $fila) {
            if($fila->total_salida > 0){
                $tabla .= "<tr>
                <td style='text-align: center; font-size:10px; border: 1px solid black;'>$fila->nombreMaterial</td>
                <td style='text-align: center; font-size:10px; border: 1px solid black;'>$fila->nombreUnidadMedida</td>
                <td style='text-align: center; font-size:10px; border: 1px solid black;'>$fila->nombreMarca</td>
                <td style='text-align: center; font-size:10px; border: 1px solid black;'>$fila->nombreNormativa</td>
                <td style='text-align: center; font-size:10px; border: 1px solid black;'>$fila->total_salida</td>
            </tr>";
            }
        }


        $tabla .= "</tbody></table>";

        $stylesheet = file_get_contents('css/cssbodega.css');
        $mpdf->WriteHTML($stylesheet,1);

        $mpdf->setFooter("Página: " . '{PAGENO}' . "/" . '{nb}');
        $mpdf->WriteHTML($tabla,2);

        $mpdf->Output();
    }










}
