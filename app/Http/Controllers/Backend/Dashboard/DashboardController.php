<?php

namespace App\Http\Controllers\Backend\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Libros;
use App\Models\NichoCobros;
use App\Models\NichoMunicipal;
use App\Models\NichoMunicipalDetalle;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function vistaBuscador()
    {
        return view('backend.admin.buscador.vistabuscador');
    }

    public function buscadorPersona(Request $request)
    {
        if ($request->get('query')) {
            $query = $request->get('query');

            $arrayPersona = NichoMunicipalDetalle::where('nombre', 'LIKE', "%{$query}%")->get();

            $output = '<ul class="dropdown-menu" style="display:block; position:relative; overflow: auto; ">';
            $tiene = true;
            foreach ($arrayPersona as $row) {

                $nombreCompleto = $row->nombre;

                // si solo hay 1 fila, No mostrara el hr, salto de linea
                if (count($arrayPersona) == 1) {
                    if (!empty($row)) {
                        $tiene = false;
                        $output .= '
                 <li class="cursor-pointer" onclick="modificarValor(this)" id="' . $row->id . '"><a href="#" style="margin-left: 3px; color: black">' . $nombreCompleto . '</a></li>
                ';
                    }
                } else {
                    if (!empty($row)) {
                        $tiene = false;
                        $output .= '
                 <li class="cursor-pointer" onclick="modificarValor(this)" id="' . $row->id . '"><a href="#" style="margin-left: 3px; color: black">' . $nombreCompleto . '</a></li>
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



    //************* PERSONA BUSCADA **********************

    public function vistaPersonaBuscada($id)
    {
        // nicho_municipal_detalle

        return view('backend.admin.buscador.infobuscado.vistapersonabuscada', compact('id'));
    }

    public function tablaPersonaBuscada($id)
    {
        $infoPrin = NichoMunicipalDetalle::where('id', $id)->first();

        $listado = NichoMunicipal::where('id', $infoPrin->id_nicho_municipal)
            ->orderBy('correlativo', 'desc')
            ->get();

        foreach ($listado as $fila) {

            $arrayNichoMuniDetalle = NichoMunicipalDetalle::where('id_nicho_municipal', $fila->id)->get();


            // COLUMNA: NOMBRE DEL FALLECIDO
            $nombresFallecidos = '';
            // COLUMNA: FECHA DE FALLECIMIENTO
            $fechasFallecimiento = '';
            // COLUMNA: FECHA EXHUMACION
            $fechasExhumacion = "";
            // COLUMNA: FECHA INICIO (desde fecha Fallecimiento o del ultimo cobro)
            $fechaInicioCiclo = "";
            // COLUMNA: FECHA VENCIMIENTO
            $proximaFechaVencimiento = "";
            // COLUMNA: PERIODO MORA PENDIENTE
            $periodosMoraVencimiento = "";
            // COLUMNA: PERIODOS PAGADOS
            $periodosPagados = "";

            // BOTON INFO PARA CADA PERSONA

            $botonNicho = "";

            // SI HAY FECHA DE EXHUMACION ME MOSTRARA EL TEXTO, SINO HARA EL CALCULO


            foreach ($arrayNichoMuniDetalle as $item){


                $botonNicho .= '<button type="button" class="btn btn-info btn-xs"
                 onclick="vistaDetalle(' . $item->id . ')">
                 <i class="fas fa-eye" title="Info"></i>&nbsp; Info
               </button><hr><br>';


                $ff = date("d-m-Y", strtotime($item->fecha_fallecimiento));
                $fechasFallecimiento .= $ff . '<hr><br>';

                $feEx = '';
                if($item->fecha_exhumacion != null){
                    $fe = date("d-m-Y", strtotime($item->fecha_exhumacion));
                    $feEx .= $fe . '<hr><br>';
                }else{
                    $feEx .= '-' . '<hr><br>';
                }
                $fechasExhumacion .= $feEx;

                $nombresFallecidos .= $item->nombre . '<hr><br>';


                // VERIFICAR CADA CICLO DE COBROS

                // OBTENER EL ULTIMO REGISTRO
                $infoCobro = NichoCobros::where('id_nichomunicipal_detalle', $item->id)
                    ->orderByDesc('fecha_recibo') // Ordenar de más reciente a más antiguo
                    ->first();

                // SIEMPRE HABRA MINIMO 1 REGISTRO
                $conteoCobro = NichoCobros::where('id_nichomunicipal_detalle', $item->id)->count();

                if($item->fecha_exhumacion == null){
                    $periodosPagados .= $infoCobro->periodo . "<hr><br>";

                    if ($conteoCobro == 1){
                        // Solo tiene 1 registro de pago
                        $fechaInicioCiclo .= "Fecha Fallecimiento" . "<hr><br>";

                        $fechaPago = Carbon::parse($item->fecha_fallecimiento);
                        $fechaActual = Carbon::now('America/El_Salvador')->startOfDay(); // Fecha actual sin hora

                        $periodos = ($infoCobro->periodo == 1) ? 14 : 14 + (($infoCobro->periodo - 1) * 7);

                        $fechaVencimiento = $fechaPago->copy()->addYears($periodos)->startOfDay(); // Sumar los años correspondientes



                        $periodosMora = 0;
                        $estado = 'normal'; // Estado por defecto
                        $aniosTranscurridos = $fechaActual->diffInYears($fechaPago);

                        // Comprobamos si la fecha de vencimiento ya ha pasado
                        if ($aniosTranscurridos >= 14) {
                            // Primer periodo de mora al pasar 14 años
                            $periodosMora = 1;
                            // Los años adicionales que exceden los primeros 14 años
                            $aniosAdicionales = $aniosTranscurridos - 14;
                            // Cada 7 años completos adicionales suman otro periodo de mora
                            $periodosMora += floor($aniosAdicionales / 7);
                        }


                        if ($fechaActual->isSameYear($fechaVencimiento) && $fechaActual->isBefore($fechaVencimiento)) {
                            $estado = 'amarillo'; // Estamos en el año de vencimiento, pero antes de la fecha
                        } elseif ($fechaActual->isSameDay($fechaVencimiento) || $fechaActual->isAfter($fechaVencimiento)) {
                            $estado = 'rojo'; // Fecha actual es igual o posterior a la fecha de vencimiento
                        }

                        if ($estado === 'amarillo') {
                            $proximaFechaVencimiento .= '<span class="badge bg-warning">' . $fechaVencimiento->format('d-m-Y') . '</span><hr><br>';
                        } elseif ($estado === 'rojo') {
                            $proximaFechaVencimiento .= '<span class="badge bg-danger">' . $fechaVencimiento->format('d-m-Y') . '</span><hr><br>';
                        } else {
                            // No se muestra etiqueta si es "normal"
                            $proximaFechaVencimiento .= $fechaVencimiento->format('d-m-Y') . "<hr><br>";
                        }

                        $periodosMoraVencimiento .= $periodosMora . "<hr><br>";
                    } else {

                        $fechaInicioCiclo .= "Recibo (" . date("d-m-Y", strtotime($infoCobro->fecha_recibo)) . ")" . "<hr><br>";

                        $fechaPago = Carbon::parse($infoCobro->fecha_recibo); // Último pago registrado
                        // Ahora la fecha de vencimiento es la fecha del último pago más 7 años
                        // Pero se multiplica por cada periodo
                        $periodoMultiplicado = 7 * $infoCobro->periodo;

                        $fechaVencimiento = $fechaPago->copy()->addYears($periodoMultiplicado)->startOfDay();
                        $fechaActual = Carbon::now('America/El_Salvador')->startOfDay();

                        $periodosMora = 0;
                        $estado = 'normal';

                        // Calculamos los años transcurridos desde el último pago
                        $aniosTranscurridos = $fechaActual->diffInYears($fechaPago);

                        // Cada 7 años completos desde el último pago cuentan como 1 periodo de mora
                        if ($aniosTranscurridos >= 7) {
                            $periodosMora = floor($aniosTranscurridos / 7);
                        }

                        // Determinamos el estado (amarillo o rojo) según la fecha de vencimiento
                        if ($fechaActual->isSameYear($fechaVencimiento) && $fechaActual->isBefore($fechaVencimiento)) {
                            $estado = 'amarillo';
                        } elseif ($fechaActual->isSameDay($fechaVencimiento) || $fechaActual->isAfter($fechaVencimiento)) {
                            $estado = 'rojo';
                        }

                        if ($estado === 'amarillo') {
                            $proximaFechaVencimiento .= '<span class="badge bg-warning">' . $fechaVencimiento->format('d-m-Y') . '</span><hr><br>';
                        } elseif ($estado === 'rojo') {
                            $proximaFechaVencimiento .= '<span class="badge bg-danger">' . $fechaVencimiento->format('d-m-Y') . '</span><hr><br>';
                        } else {
                            $proximaFechaVencimiento .= $fechaVencimiento->format('d-m-Y') . "<hr><br>";
                        }

                        $periodosMoraVencimiento .= $periodosMora . '</span><hr><br>';
                    }
                }else{
                    $fechaInicioCiclo .= "Exhumado" . "<hr><br>";
                    $proximaFechaVencimiento .= "Exhumado" . "<hr><br>";
                    $periodosMoraVencimiento .= "Exhumado" . "<hr><br>";
                    $periodosPagados .= "Exhumado" . "<hr><br>";
                }
            }

            $fila->nombresFallecidos = $nombresFallecidos;
            $fila->fechasFallecimiento = $fechasFallecimiento;
            $fila->fechasExhumacion = $fechasExhumacion;
            $fila->fechaInicioCiclo = $fechaInicioCiclo;
            $fila->fechaProxVencimiento = $proximaFechaVencimiento;
            $fila->peridosMora = $periodosMoraVencimiento;
            $fila->periodosPagados = $periodosPagados;
            $fila->botonNicho = $botonNicho;
        }


        return view('backend.admin.librosdetalle.tablalibrosdetalle', compact('listado'));
    }
}
