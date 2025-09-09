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
use App\Models\Prueba;
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
                            <th style='font-weight: bold; width: 8%; font-size: 12px; text-align: center;'>Factura</th>
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




    public function reporteKardexMaterial($idmaterial) {

        $infoMaterial = Materiales::where('id', $idmaterial)->first();

        $marca = "";
        $normativa = "";
        $color = "";
        $talla = "";

        if($infoMarca = Marca::where('id', $infoMaterial->id_marca)->first()){
            $marca = $infoMarca->nombre;
        }

        if($infoNormativa = Normativa::where('id', $infoMaterial->id_normativa)->first()){
            $normativa = $infoNormativa->nombre;
        }

        if($infoColor = Color::where('id', $infoMaterial->id_color)->first()){
            $color = $infoColor->nombre;
        }

        if($infoTalla = Talla::where('id', $infoMaterial->id_talla)->first()){
            $talla = $infoTalla->nombre;
        }



        // obtener listado de id entradas que tiene este material

        $pilaIdEntradas = array();
        $listadoEntradasID = EntradasDetalle::where('id_material', $idmaterial)->get();

        foreach ($listadoEntradasID as $fila) {
            array_push($pilaIdEntradas, $fila->id_entradas);
        }

        $arrayEntradas = Entradas::whereIn('id', $pilaIdEntradas)->orderBy('fecha', 'ASC')->get();

        $resultsBloque = array();
        $index = 0;


        $totalRecibido = 0;
        $totalEntregado = 0;

        foreach ($arrayEntradas as $fila) {
            array_push($resultsBloque,$fila);

            $fila->fechaFormat = date("d-m-Y", strtotime($fila->fecha));

            $conteoInicial = EntradasDetalle::where('id_material', $idmaterial)
                ->where('id_entradas', $fila->id)
                ->sum('cantidad_inicial');

            // buscar todas sus salidas
            $arraySalidas = DB::table('salidas_detalle AS sd')
                ->join('entradas_detalle AS ed', 'sd.id_entrada_detalle', '=', 'ed.id')
                ->select('ed.id_entradas', 'ed.cantidad', 'ed.cantidad_inicial', 'ed.cantidad_entregada',
                'ed.precio', 'sd.cantidad_salida', 'sd.id_salida')
                ->where('ed.id_entradas', $fila->id)
                ->get();


            foreach ($arraySalidas as $filaSalida) {

                $totalEntregado += $filaSalida->cantidad_entregada;

                $infoSalida = Salidas::where('id', $filaSalida->id_salida)->first();
                $filaSalida->fechaFormat = date("d-m-Y", strtotime($infoSalida->fecha));

                $infoEmpleado = Empleado::where('id', $infoSalida->id_empleado)->first();
                $infoUnidad = UnidadEmpleado::where('id', $infoEmpleado->id_unidad_empleado)->first();
                $infoDistrito = Distrito::where('id', $infoUnidad->id_distrito)->first();

                $filaSalida->nombreEmpleado = $infoEmpleado->nombre;
                $filaSalida->nombreUnidad = $infoUnidad->nombre;
                $filaSalida->nombreDistrito = $infoDistrito->nombre;
            }

            $arraySalidaDetalleSORT = $arraySalidas->sortBy('fecha');

            $totalRecibido += $conteoInicial;
            $fila->cantidadInicial = $conteoInicial;

            $resultsBloque[$index]->detalle = $arraySalidaDetalleSORT;
            $index++;
        }


        //$mpdf = new \Mpdf\Mpdf(['format' => 'LETTER']);
        //$mpdf = new \Mpdf\Mpdf(['tempDir' => sys_get_temp_dir(), 'format' => 'LETTER']);
        //$mpdf = new \Mpdf\Mpdf(['format' => 'LETTER-L']);
        $mpdf = new \Mpdf\Mpdf(['tempDir' => sys_get_temp_dir(), 'format' => 'LETTER-L']);


        $mpdf->SetTitle('Reporte Kardex');

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
              <div style='text-align: left; margin-top: 10px;'>
                <p style='font-size: 13px; margin: 0; color: #000;'><strong>Fecha Generado:</strong> $fechaFormat</p>
            </div>
             <div style='text-align: left; margin-top: 15px;'>
                <p style='font-size: 13px; margin: 0; color: #000;'><strong>Material:</strong> $infoMaterial->nombre</p>
            </div>
               <div style='text-align: left; margin-top: 10px;'>
                <p style='font-size: 13px; margin: 0; color: #000;'><strong>Código:</strong> $fechaFormat</p>
            </div>
              <div style='text-align: left; margin-top: 10px;'>
                <p style='font-size: 13px; margin: 0; color: #000;'><strong>Marca:</strong> $marca</p>
            </div>
              <div style='text-align: left; margin-top: 10px;'>
                <p style='font-size: 13px; margin: 0; color: #000;'><strong>Normativa:</strong> $normativa</p>
            </div>
                <div style='text-align: left; margin-top: 10px;'>
                <p style='font-size: 13px; margin: 0; color: #000;'><strong>Color:</strong> $color</p>
            </div>
                <div style='text-align: left; margin-top: 10px;'>
                <p style='font-size: 13px; margin: 0; color: #000;'><strong>Talla:</strong> $talla</p>
            </div>
      ";


        foreach ($arrayEntradas as $fila) {

            $tabla .= "<table width='100%' id='tablaFor' style='margin-top: 30px'>
            <thead>
                <tr>
                    <th style='text-align: center; font-size:12px; width: 12%; font-weight: bold; border: 1px solid black;'>Fecha Entrada</th>
                    <th style='text-align: center; font-size:12px; width: 12%; font-weight: bold; border: 1px solid black;'>Factura</th>
                     <th style='text-align: center; font-size:12px; width: 12%; font-weight: bold; border: 1px solid black;'>Recibido</th>
                    <th style='text-align: center; font-size:12px; width: 12%; font-weight: bold; border: 1px solid black;'>Descripción</th>
                </tr>
            </thead>
            <tbody>";

            $tabla .= "<tr>
                    <td style='font-size: 11px; font-weight: normal'>$fila->fechaFormat</td>
                    <td style='font-size: 11px; font-weight: normal'>$fila->lote</td>
                     <td style='font-size: 11px; font-weight: normal'>$fila->cantidadInicial</td>
                    <td style='font-size: 11px; font-weight: normal'>$fila->descripcion</td>
                </tr>";

            $tabla .= "</tbody></table>";




            if (!empty($fila->detalle) && count($fila->detalle) > 0) {

                $tabla .= "<table width='100%' id='tablaFor' style='margin-top: 30px'>
                <thead>
                    <tr>
                        <th style='text-align: center; font-size:12px; width: 12%; font-weight: bold; border: 1px solid black;'>Fecha Salida</th>
                        <th style='text-align: center; font-size:12px; width: 12%; font-weight: bold; border: 1px solid black;'>Distrito</th>
                        <th style='text-align: center; font-size:12px; width: 12%; font-weight: bold; border: 1px solid black;'>Unidad</th>
                        <th style='text-align: center; font-size:12px; width: 12%; font-weight: bold; border: 1px solid black;'>Empleado</th>
                        <th style='text-align: center; font-size:12px; width: 12%; font-weight: bold; border: 1px solid black;'>Entregado</th>
                    </tr>
                </thead>
                <tbody>";

                foreach ($fila->detalle as $jj){

                    $tabla .= "<tr>
                    <td style='font-size: 11px; font-weight: normal'>$jj->fechaFormat</td>
                    <td style='font-size: 11px; font-weight: normal'>$jj->nombreDistrito</td>
                     <td style='font-size: 11px; font-weight: normal'>$jj->nombreUnidad</td>
                      <td style='font-size: 11px; font-weight: normal'>$jj->nombreEmpleado</td>
                       <td style='font-size: 11px; font-weight: normal'>$jj->cantidad_salida</td>
                </tr>";

                }

                $tabla .= "</tbody></table>";
            }







        }







        $totalActual = $totalRecibido - $totalEntregado;


        $tabla .= "
            <div style='text-align: left; margin-top: 35px; margin-left: 15px'>
                <p style='font-size: 15px; margin: 0; color: #000;'><strong>Total Recibido: $totalRecibido </strong> </p>
                 <p style='font-size: 15px; margin: 0; color: #000;'><strong>Total Entregado: $totalEntregado </strong> </p>
                   <p style='font-size: 15px; margin: 0; color: #000;'><strong>Total Actual: $totalActual </strong> </p>
            </div>
      ";


        $stylesheet = file_get_contents('css/cssbodega.css');
        $mpdf->WriteHTML($stylesheet,1);

        $mpdf->setFooter("Página: " . '{PAGENO}' . "/" . '{nb}');
        $mpdf->WriteHTML($tabla,2);

        $mpdf->Output();
    }



    public function pruebas()
    {

        $unicos = Prueba::select('unidad')->distinct()->get();

        return $unicos;



    }










}
