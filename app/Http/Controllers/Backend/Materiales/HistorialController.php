<?php

namespace App\Http\Controllers\backend\materiales;

use App\Http\Controllers\Controller;
use App\Models\Color;
use App\Models\Distrito;
use App\Models\Encargado;
use App\Models\Entradas;
use App\Models\EntradasDetalle;
use App\Models\Marca;
use App\Models\Materiales;
use App\Models\Normativa;
use App\Models\Retorno;
use App\Models\Salidas;
use App\Models\SalidasDetalle;
use App\Models\Talla;
use App\Models\UnidadMedida;
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

            $infoRecibe = Encargado::where('id', $fila->id_encargado)->first();
            $fila->nombreRecibe = $infoRecibe->nombre;

            $infoDistrito = Distrito::where('id', $fila->id_distrito)->first();
            $fila->nombreDistrito = $infoDistrito->nombre;
        }

        return view('backend.admin.historial.salidas.tablasalidabodega', compact('listado'));
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
        }

        return view('backend.admin.historial.entradas.tablaentradabodega', compact('listado'));
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
                'bo.id_entradas', 'bm.id_marca', 'bm.id_normativa', 'bm.id_color', 'bm.id_talla')
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




}
