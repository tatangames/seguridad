<?php

namespace App\Http\Controllers\backend\config;

use App\Http\Controllers\Controller;
use App\Models\Entradas;
use App\Models\EntradasDetalle;
use App\Models\Marca;
use App\Models\Materiales;
use App\Models\Normativa;
use App\Models\UnidadMedida;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReportesController extends Controller
{
    public function __construct(){
        $this->middleware('auth');
    }

    public function indexGeneralReportes()
    {
        return view('backend.admin.reportes.general.vistageneralreportes');
    }


    public function reporteExistencias()
    {


        // 1. Obtener detalles de entrada con diferencia entre cantidad entregada y total
        $arrayInfo = EntradasDetalle::whereColumn('cantidad_entregada', '<', 'cantidad')->get();

        // 2. Procesar cada fila para agregar info adicional y calcular cantidad actual
        foreach ($arrayInfo as $fila) {
            $infoMaterial = Materiales::where('id', $fila->id_material)->first();
            $infoUnidad = UnidadMedida::where('id', $infoMaterial->id_medida)->first();

            $infoMarca = Marca::where('id', $infoMaterial->id_marca)->first();
            $infoNormativa = Normativa::where('id', $infoMaterial->id_normativa)->first();


            $fila->unidadMedida = $infoUnidad->nombre;
            $fila->nombreMaterial = $infoMaterial->nombre;
            $fila->nombreMarca = $infoMarca->nombre;
            $fila->nombreNormativa = $infoNormativa->nombre;

            $resta = $fila->cantidad - $fila->cantidad_entregada;
            $fila->cantidadActual = $resta;

            // Ya no se necesita el lote, así que no lo incluimos
        }

        // 3. Agrupar por material y sumar cantidadActual
        $arrayAgrupado = $arrayInfo->groupBy('id_material')->map(function ($items) {
            $primer = $items->first(); // Conservamos datos comunes como nombre y unidad

            return (object)[
                'id_material'     => $primer->id_material,
                'nombreMaterial'  => $primer->nombreMaterial,
                'unidadMedida'    => $primer->unidadMedida,
                'nombreMarca'    => $primer->nombreMarca,
                'nombreNormativa'    => $primer->nombreNormativa,
                'cantidadTotal'   => $items->sum('cantidadActual'),
            ];
        })->sortBy('nombreMaterial')->values();

        // 4. Fecha formateada para mostrar si la necesitas
        $fechaFormat = date("d-m-Y", strtotime(Carbon::now('America/El_Salvador')));




        //$mpdf = new \Mpdf\Mpdf(['format' => 'LETTER']);
        $mpdf = new \Mpdf\Mpdf(['tempDir' => sys_get_temp_dir(), 'format' => 'LETTER']);

        $mpdf->SetTitle('Existencias Actual');

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
                <h1 style='font-size: 15px; margin: 0; color: #000;'>EXISTENCIAS</h1>
            </div>
            <div style='text-align: left; margin-top: 10px;'>
            <p style='font-size: 13px; margin: 0; color: #000;'>Fecha: $fechaFormat</p>
        </div>
      ";

        $tabla .= "<table id='tablaFor' style='width: 100%; border-collapse: collapse; margin-top: 35px'>
        <tbody>
            <tr>
                <th style='text-align: center; font-size:10px; width: 12%; font-weight: bold; border: 1px solid black;'>Producto</th>
                 <th style='text-align: center; font-size:10px; width: 12%; font-weight: bold; border: 1px solid black;'>U.M</th>
                 <th style='text-align: center; font-size:10px; width: 12%; font-weight: bold; border: 1px solid black;'>Marca</th>
                 <th style='text-align: center; font-size:10px; width: 12%; font-weight: bold; border: 1px solid black;'>Normativa</th>
                <th style='text-align: center; font-size:10px; width: 10%; font-weight: bold; border: 1px solid black;'>Cantidad</th>
            </tr>
        ";

        foreach ($arrayAgrupado as $fila) {
            $tabla .= "<tr>
                <td style='text-align: center; font-size:10px; border: 1px solid black;'>$fila->nombreMaterial</td>
                <td style='text-align: center; font-size:10px; border: 1px solid black;'>$fila->unidadMedida</td>
                <td style='text-align: center; font-size:10px; border: 1px solid black;'>$fila->nombreMarca</td>
                <td style='text-align: center; font-size:10px; border: 1px solid black;'>$fila->nombreNormativa</td>
                <td style='text-align: center; font-size:10px; border: 1px solid black;'>$fila->cantidadTotal</td>
            </tr>";
        }



        $tabla .= "</tbody></table>";

        $stylesheet = file_get_contents('css/cssbodega.css');
        $mpdf->WriteHTML($stylesheet,1);

        $mpdf->setFooter("Página: " . '{PAGENO}' . "/" . '{nb}');
        $mpdf->WriteHTML($tabla,2);

        $mpdf->Output();
    }



}
