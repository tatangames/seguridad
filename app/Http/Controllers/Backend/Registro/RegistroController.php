<?php

namespace App\Http\Controllers\Backend\Registro;

use App\Http\Controllers\Controller;
use App\Models\Libros;
use App\Models\NichoCobros;
use App\Models\NichoMunicipal;
use App\Models\NichoMunicipalDetalle;
use Carbon\Carbon;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class RegistroController extends Controller
{
    public function __construct(){
        $this->middleware('auth');
    }


    // ***** REGISTRO PRINCIPAL ******

    public function indexRegistro()
    {
        $arrayLibros = Libros::orderBy('nombre', 'asc')->get();

        return view('backend.admin.registro.vistaregistro', compact('arrayLibros'));
    }

    public function buscarCorrelativo(Request $request)
    {
        $regla = array(
            'id' => 'required', // idlibro
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        $nicho = NichoMunicipal::where('id_libros', $request->id)
            ->orderByDesc('correlativo')
            ->first();

        $nuevoCorrelativo = $nicho ? $nicho->correlativo + 1 : 1;

        return ['success' => 1, 'correlativo' => $nuevoCorrelativo];
    }

    public function nuevoRegistro(Request $request)
    {
        $regla = array(
            'idlibro' => 'required',
            'nombreFallecido' => 'required',
            'fechaFallecido' => 'required',
            'nombreContribuyente' => 'required',
            'periodoContribuyente' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        DB::beginTransaction();

        try {
            $fechaCiclo = $request->fechaTesoreriaContribuyente;
            if($request->fechaTesoreriaContribuyente == null){
                $fechaCiclo = Carbon::now('America/El_Salvador');
            }

            $nicho = NichoMunicipal::where('id_libros', $request->idlibro)
                ->orderByDesc('correlativo')
                ->first();

            $nuevoCorrelativo = $nicho ? $nicho->correlativo + 1 : 1;

            $regNichoMunicipal = new NichoMunicipal();
            $regNichoMunicipal->id_libros = $request->idlibro;
            $regNichoMunicipal->correlativo = $nuevoCorrelativo;
            $regNichoMunicipal->save();

            $regNichoMunicipalDetalle = new NichoMunicipalDetalle();
            $regNichoMunicipalDetalle->id_nicho_municipal = $regNichoMunicipal->id;
            $regNichoMunicipalDetalle->nombre = $request->nombreFallecido;
            $regNichoMunicipalDetalle->fecha_fallecimiento = $request->fechaFallecido;
            $regNichoMunicipalDetalle->fecha_exhumacion = $request->fechaExhumacion;
            $regNichoMunicipalDetalle->save();

            $regContribuyente = new NichoCobros();
            $regContribuyente->id_nichomunicipal_detalle = $regNichoMunicipalDetalle->id;
            $regContribuyente->fecha_ciclo = $fechaCiclo;
            $regContribuyente->nombre = $request->nombreContribuyente;
            $regContribuyente->dui = $request->duiContribuyente;
            $regContribuyente->telefono = $request->telefonoContribuyente;
            $regContribuyente->periodo = $request->periodoContribuyente;
            $regContribuyente->direccion = $request->direccionContribuyente;
            // Sera Costo de $20.00 por LEY
            $regContribuyente->base_costo = 20; // FIJO POR LEY 20 DOLARES
            $regContribuyente->anios_cobro = 7; // FIJO POR LEY 7 AÑOS
            $regContribuyente->base_comision = 5; // FIJO POR LEY 5%
            $regContribuyente->recibo = $request->reciboTesoreriaContribuyente;
            $regContribuyente->fecha_recibo = $request->fechaTesoreriaContribuyente;
            $regContribuyente->save();

            DB::commit();
            return ['success' => 1];

        } catch (\Throwable $e) {
            Log::info('ee ' . $e);
            DB::rollback();
            return ['success' => 99];
        }
    }



    //******** LIBROS DETALLE **************************


    public function indexLibrosDetalle()
    {
        $arrayLibros = Libros::orderBy('nombre', 'ASC')->get();

        $primerId = optional($arrayLibros->first())->id;

        return view('backend.admin.librosdetalle.vistalibrosdetalle', compact('arrayLibros', 'primerId'));
    }


    public function tablaLibrosDetalle($id)
    {
        $listado = NichoMunicipal::where('id_libros', $id)
            ->orderBy('correlativo', 'desc')
            ->get();

        foreach ($listado as $fila) {

            $arrayNichoMuniDetalle = NichoMunicipalDetalle::where('id_nicho_municipal', $fila->id)->get();

            $nombresFallecidos = '';
            $fechasFallecimiento = '';
            $fechasExhumacion = '';


            $proximaFechaVencimiento = "";
            $periodosMoraVencimiento = "";

            $hayVarios = false;
            if(count($arrayNichoMuniDetalle) > 1){
                $hayVarios = true;
            }

            $contadorFallecidos = 0;
            foreach ($arrayNichoMuniDetalle as $item){
                $contadorFallecidos++;
                $ff = date("d-m-Y", strtotime($item->fecha_fallecimiento));
                $fechasFallecimiento .= $ff . '<br>';

                $feEx = '';
                if($item->fecha_exhumacion != null){
                    $fe = date("d-m-Y", strtotime($item->fecha_exhumacion));
                    $feEx .= $fe . '<br>';
                }else{
                    $feEx .= '-' . '<br>';
                }
                $fechasExhumacion .= $feEx;

                if($hayVarios){
                    $nombresFallecidos .= $contadorFallecidos . "-" . $item->nombre . '<br>';
                }else{
                    $nombresFallecidos .= $item->nombre . '<br>';
                }


                // VERIFICAR CADA CICLO DE COBROS


                if($item->fecha_exhumacion == null){
                    // OBTENER EL ULTIMO REGISTRO
                    $infoCobro = NichoCobros::where('id_nichomunicipal_detalle', $item->id)
                        ->orderByDesc('fecha_ciclo') // Ordenar de más reciente a más antiguo
                        ->first();

                    // SIEMPRE HABRA MINIMO 1 REGISTRO
                    $conteoCobro = NichoCobros::where('id_nichomunicipal_detalle', $item->id)->count();

                    if ($conteoCobro == 1){
                        // Solo tiene 1 registro de pago

                        $fechaPago = Carbon::parse($infoCobro->fecha_ciclo); // Fecha del último cobro
                        $fechaVencimiento = $fechaPago->copy()->addYears(14)->startOfDay(); // Sumar 14 años y fijar a la medianoche
                        $fechaActual = Carbon::now('America/El_Salvador')->startOfDay(); // Fecha actual sin hora

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
                            $proximaFechaVencimiento .= '<span class="badge bg-warning">' . $fechaVencimiento->format('d-m-Y') . '</span><br>';
                        } elseif ($estado === 'rojo') {
                            $proximaFechaVencimiento .= '<span class="badge bg-danger">' . $fechaVencimiento->format('d-m-Y') . '</span><br>';
                        } else {
                            // No se muestra etiqueta si es "normal"
                            $proximaFechaVencimiento .= $fechaVencimiento->format('d-m-Y');
                        }


                        $periodosMoraVencimiento .= $periodosMora;
                    } else {

                    }




                }
            }

            $fila->nombresFallecidos = $nombresFallecidos;
            $fila->fechasFallecimiento = $fechasFallecimiento;
            $fila->fechasExhumacion = $fechasExhumacion;

            $fila->fechaCiclo = $proximaFechaVencimiento;
            $fila->periodoCiclo = $periodosMoraVencimiento;
        }

        return view('backend.admin.librosdetalle.tablalibrosdetalle', compact('listado'));
    }












}
