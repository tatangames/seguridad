<?php

namespace App\Http\Controllers\Backend\Config;

use App\Http\Controllers\Controller;
use App\Models\Cargo;
use App\Models\Color;
use App\Models\Distrito;
use App\Models\Empleado;
use App\Models\Marca;
use App\Models\Normativa;
use App\Models\Proveedor;
use App\Models\Talla;
use App\Models\UnidadEmpleado;
use App\Models\UnidadMedida;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class ConfiguracionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function vistaUnidadMedida()
    {
        return view('backend.admin.config.unidadmedida.vistaunidadmedida');
    }

    public function tablaUnidadMedida()
    {
        $lista = UnidadMedida::orderBy('nombre', 'ASC')->get();

        return view('backend.admin.config.unidadmedida.tablaunidadmedida', compact('lista'));
    }


    public function nuevoUnidadMedida(Request $request)
    {
        $regla = array(
            'nombre' => 'required'
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()) {
            return ['success' => 0];
        }
        DB::beginTransaction();

        try {
            $dato = new UnidadMedida();
            $dato->nombre = $request->nombre;
            $dato->save();

            DB::commit();
            return ['success' => 1];
        } catch (\Throwable $e) {
            Log::info('error ' . $e);
            DB::rollback();
            return ['success' => 99];
        }
    }


    public function infoUnidadMedida(Request $request)
    {
        $regla = array(
            'id' => 'required'
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()) {
            return ['success' => 0];
        }

        $info = UnidadMedida::where('id', $request->id)->first();

        return ['success' => 1, 'info' => $info];
    }

    public function actualizarUnidadMedida(Request $request)
    {
        $regla = array(
            'id' => 'required',
            'nombre' => 'required'
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()) {
            return ['success' => 0];
        }

        UnidadMedida::where('id', $request->id)->update([
            'nombre' => $request->nombre
        ]);

        return ['success' => 1];
    }


    //******************** MARCA *************************************************************


    public function vistaMarca()
    {
        return view('backend.admin.config.marca.vistamarca');
    }

    public function tablaMarca()
    {
        $lista = Marca::orderBy('nombre', 'ASC')->get();

        return view('backend.admin.config.marca.tablamarca', compact('lista'));
    }


    public function nuevoMarca(Request $request)
    {
        $regla = array(
            'nombre' => 'required'
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()) {
            return ['success' => 0];
        }
        DB::beginTransaction();

        try {
            $dato = new Marca();
            $dato->nombre = $request->nombre;
            $dato->save();

            DB::commit();
            return ['success' => 1];
        } catch (\Throwable $e) {
            Log::info('error ' . $e);
            DB::rollback();
            return ['success' => 99];
        }
    }


    public function infoMarca(Request $request)
    {
        $regla = array(
            'id' => 'required'
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()) {
            return ['success' => 0];
        }

        $info = Marca::where('id', $request->id)->first();

        return ['success' => 1, 'info' => $info];
    }

    public function actualizarMarca(Request $request)
    {
        $regla = array(
            'id' => 'required',
            'nombre' => 'required'
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()) {
            return ['success' => 0];
        }

        Marca::where('id', $request->id)->update([
            'nombre' => $request->nombre
        ]);

        return ['success' => 1];
    }


    //******************** NORMATIVA *************************************************************


    public function vistaNormativa()
    {
        return view('backend.admin.config.normativa.vistanormativa');
    }

    public function tablaNormativa()
    {
        $lista = Normativa::orderBy('nombre', 'ASC')->get();

        return view('backend.admin.config.normativa.tablanormativa', compact('lista'));
    }


    public function nuevoNormativa(Request $request)
    {
        $regla = array(
            'nombre' => 'required'
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()) {
            return ['success' => 0];
        }
        DB::beginTransaction();

        try {
            $dato = new Normativa();
            $dato->nombre = $request->nombre;
            $dato->save();

            DB::commit();
            return ['success' => 1];
        } catch (\Throwable $e) {
            Log::info('error ' . $e);
            DB::rollback();
            return ['success' => 99];
        }
    }


    public function infoNormativa(Request $request)
    {
        $regla = array(
            'id' => 'required'
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()) {
            return ['success' => 0];
        }

        $info = Normativa::where('id', $request->id)->first();

        return ['success' => 1, 'info' => $info];
    }

    public function actualizarNormativa(Request $request)
    {
        $regla = array(
            'id' => 'required',
            'nombre' => 'required'
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()) {
            return ['success' => 0];
        }

        Normativa::where('id', $request->id)->update([
            'nombre' => $request->nombre
        ]);

        return ['success' => 1];
    }


    //******************** COLOR *************************************************************


    public function vistaColor()
    {
        return view('backend.admin.config.color.vistacolor');
    }

    public function tablaColor()
    {
        $lista = Color::orderBy('nombre', 'ASC')->get();

        return view('backend.admin.config.color.tablacolor', compact('lista'));
    }


    public function nuevoColor(Request $request)
    {
        $regla = array(
            'nombre' => 'required'
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()) {
            return ['success' => 0];
        }
        DB::beginTransaction();

        try {
            $dato = new Color();
            $dato->nombre = $request->nombre;
            $dato->save();

            DB::commit();
            return ['success' => 1];
        } catch (\Throwable $e) {
            Log::info('error ' . $e);
            DB::rollback();
            return ['success' => 99];
        }
    }


    public function infoColor(Request $request)
    {
        $regla = array(
            'id' => 'required'
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()) {
            return ['success' => 0];
        }

        $info = Color::where('id', $request->id)->first();

        return ['success' => 1, 'info' => $info];
    }

    public function actualizarColor(Request $request)
    {
        $regla = array(
            'id' => 'required',
            'nombre' => 'required'
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()) {
            return ['success' => 0];
        }

        Color::where('id', $request->id)->update([
            'nombre' => $request->nombre
        ]);

        return ['success' => 1];
    }


//******************** TALLA *************************************************************


    public function vistaTalla()
    {
        return view('backend.admin.config.talla.vistatalla');
    }

    public function tablaTalla()
    {
        $lista = Talla::orderBy('nombre', 'ASC')->get();

        return view('backend.admin.config.talla.tablatalla', compact('lista'));
    }


    public function nuevoTalla(Request $request)
    {
        $regla = array(
            'nombre' => 'required'
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()) {
            return ['success' => 0];
        }
        DB::beginTransaction();

        try {
            $dato = new Talla();
            $dato->nombre = $request->nombre;
            $dato->save();

            DB::commit();
            return ['success' => 1];
        } catch (\Throwable $e) {
            Log::info('error ' . $e);
            DB::rollback();
            return ['success' => 99];
        }
    }


    public function infoTalla(Request $request)
    {
        $regla = array(
            'id' => 'required'
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()) {
            return ['success' => 0];
        }

        $info = Talla::where('id', $request->id)->first();

        return ['success' => 1, 'info' => $info];
    }

    public function actualizarTalla(Request $request)
    {
        $regla = array(
            'id' => 'required',
            'nombre' => 'required'
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()) {
            return ['success' => 0];
        }

        Talla::where('id', $request->id)->update([
            'nombre' => $request->nombre
        ]);

        return ['success' => 1];
    }


    //******************** DISTRITO *************************************************************

    public function vistaDistrito()
    {
        return view('backend.admin.config.empleados.distrito.vistadistrito');
    }

    public function tablaDistrito()
    {

        $listado = Distrito::orderBy('nombre', 'ASC')->get();

        return view('backend.admin.config.empleados.distrito.tabladistrito', compact('listado'));
    }

    public function nuevoDistrito(Request $request)
    {
        $regla = array(
            'nombre' => 'required'
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()) {
            return ['success' => 0];
        }
        DB::beginTransaction();

        try {
            $dato = new Distrito();
            $dato->nombre = $request->nombre;
            $dato->save();

            DB::commit();
            return ['success' => 1];
        } catch (\Throwable $e) {
            Log::info('error ' . $e);
            DB::rollback();
            return ['success' => 99];
        }
    }

    public function infoDistrito(Request $request){
        $regla = array(
            'id' => 'required'
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()) {
            return ['success' => 0];
        }

        $info = Distrito::where('id', $request->id)->first();

        return ['success' => 1, 'info' => $info];
    }


    public function actualizarDistrito(Request $request)
    {
        $regla = array(
            'id' => 'required',
            'nombre' => 'required'
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()) {
            return ['success' => 0];
        }

        Distrito::where('id', $request->id)->update([
            'nombre' => $request->nombre,
        ]);

        return ['success' => 1];
    }



    //******************** UNIDAD EMPLEADO *************************************************************


    public function vistaUnidadEmpleado()
    {
        $arrayDistritos = Distrito::orderBy('nombre', 'ASC')->get();

        // jefes: relación many-to-many via jefe_unidad
        $listado = UnidadEmpleado::with(['distrito', 'jefesACargo.cargo'])
            ->orderBy('nombre', 'ASC')
            ->get()
            ->map(function ($item) {
                $item->distrito = $item->distrito->nombre ?? '—';
                $item->jefes    = $item->jefesACargo;      // colección de empleados jefe
                return $item;
            });

        return view('backend.admin.config.empleados.unidadempleado.vistaunidadempleados',
            compact('arrayDistritos', 'listado'));
    }


    public function nuevoUnidadEmpleado(Request $request)
    {
        $validar = Validator::make($request->all(), [
            'nombre' => 'required',
            'unidad' => 'required',
        ]);

        if ($validar->fails()) return ['success' => 0];

        DB::beginTransaction();
        try {
            $dato = new UnidadEmpleado();
            $dato->id_distrito = $request->unidad;
            $dato->nombre      = $request->nombre;
            $dato->save();

            DB::commit();
            return ['success' => 1];
        } catch (\Throwable $e) {
            Log::error('nuevoUnidadEmpleado: ' . $e);
            DB::rollback();
            return ['success' => 99];
        }
    }


    public function infoUnidadEmpleado(Request $request)
    {
        $validar = Validator::make($request->all(), ['id' => 'required']);
        if ($validar->fails()) return ['success' => 0];

        $info          = UnidadEmpleado::findOrFail($request->id);
        $arrayDistrito = Distrito::orderBy('nombre', 'ASC')->get();

        return ['success' => 1, 'info' => $info, 'arrayDistrito' => $arrayDistrito];
    }


    public function actualizarUnidadEmpleado(Request $request)
    {
        $validar = Validator::make($request->all(), [
            'id'       => 'required',
            'nombre'   => 'required',
            'distrito' => 'required',
        ]);
        if ($validar->fails()) return ['success' => 0];

        UnidadEmpleado::where('id', $request->id)->update([
            'nombre'      => $request->nombre,
            'id_distrito' => $request->distrito,
        ]);

        return ['success' => 1];
    }



    public function informacionJefesUnidad(Request $request)
    {
        $validar = Validator::make($request->all(), ['id' => 'required']);
        if ($validar->fails()) return ['success' => 0];

        // Todos los empleados con jefe=true para poblar el select
        $arrayJefes = Empleado::with('cargo')
            ->where('jefe', true)
            ->orderBy('nombre', 'ASC')
            ->get()
            ->map(function ($e) {
                $e->nombre_completo = $e->nombre . ' (' . ($e->cargo->nombre ?? '—') . ')';
                return $e;
            });

        // Jefes ya asignados a esta unidad
        $asignados = DB::table('jefe_unidad')
            ->join('empleado', 'empleado.id', '=', 'jefe_unidad.id_empleado')
            ->join('cargo', 'cargo.id', '=', 'empleado.id_cargo')
            ->where('jefe_unidad.id_unidad_empleado', $request->id)
            ->select(
                'jefe_unidad.id as pivot_id',
                'empleado.nombre',
                'cargo.nombre as cargo'
            )
            ->get();

        return ['success' => 1, 'arrayJefes' => $arrayJefes, 'asignados' => $asignados];
    }







    // ── Agregar jefe a unidad ──
    public function agregarJefeUnidad(Request $request)
    {
        $validar = Validator::make($request->all(), [
            'id_unidad'   => 'required',
            'id_empleado' => 'required',
        ]);
        if ($validar->fails()) return ['success' => 0];

        // Verificar que no esté duplicado
        $existe = DB::table('jefe_unidad')
            ->where('id_unidad_empleado', $request->id_unidad)
            ->where('id_empleado', $request->id_empleado)
            ->exists();

        if ($existe) return ['success' => 2]; // ya asignado

        DB::table('jefe_unidad')->insert([
            'id_empleado'        => $request->id_empleado,
            'id_unidad_empleado' => $request->id_unidad,
        ]);

        // Devolver lista actualizada
        $asignados = $this->getAsignados($request->id_unidad);

        return ['success' => 1, 'asignados' => $asignados];
    }


// ── Quitar jefe de unidad ──
    public function quitarJefeUnidad(Request $request)
    {
        $validar = Validator::make($request->all(), ['pivot_id' => 'required']);
        if ($validar->fails()) return ['success' => 0];

        $pivot = DB::table('jefe_unidad')->where('id', $request->pivot_id)->first();
        if (!$pivot) return ['success' => 0];

        DB::table('jefe_unidad')->where('id', $request->pivot_id)->delete();

        $asignados = $this->getAsignados($pivot->id_unidad_empleado);

        return ['success' => 1, 'asignados' => $asignados];
    }


// ── Helper privado: obtener jefes asignados de una unidad ──
    private function getAsignados($idUnidad)
    {
        return DB::table('jefe_unidad')
            ->join('empleado', 'empleado.id', '=', 'jefe_unidad.id_empleado')
            ->join('cargo', 'cargo.id', '=', 'empleado.id_cargo')
            ->where('jefe_unidad.id_unidad_empleado', $idUnidad)
            ->select(
                'jefe_unidad.id as pivot_id',
                'empleado.nombre',
                'cargo.nombre as cargo'
            )
            ->get();
    }











    public function editarJefeInmediato(Request $request)
    {
        $regla = array(
            'id' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()) {
            return ['success' => 0];
        }

        UnidadEmpleado::where('id', $request->id)->update([
            'id_empleado' => $request->empleadounidad,
            'id_empleado_inmediato' => $request->empleadoinmediato
        ]);

        return ['success' => 1];
    }



    //******************** CARGOS *************************************************************

    public function vistaCargo()
    {
        return view('backend.admin.config.empleados.cargo.vistacargo');
    }

    public function tablaCargo()
    {
        $listado = Cargo::orderBy('nombre', 'ASC')->get();

        return view('backend.admin.config.empleados.cargo.tablacargo', compact('listado'));
    }

    public function nuevoCargo(Request $request)
    {
        $regla = array(
            'nombre' => 'required'
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()) {
            return ['success' => 0];
        }
        DB::beginTransaction();

        try {
            $dato = new Cargo();
            $dato->nombre = $request->nombre;
            $dato->save();

            DB::commit();
            return ['success' => 1];
        } catch (\Throwable $e) {
            Log::info('error ' . $e);
            DB::rollback();
            return ['success' => 99];
        }
    }

    public function infoCargo(Request $request){
        $regla = array(
            'id' => 'required'
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()) {
            return ['success' => 0];
        }

        $info = Cargo::where('id', $request->id)->first();

        return ['success' => 1, 'info' => $info];
    }


    public function actualizarCargo(Request $request)
    {
        $regla = array(
            'id' => 'required',
            'nombre' => 'required'
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()) {
            return ['success' => 0];
        }

        Cargo::where('id', $request->id)->update([
            'nombre' => $request->nombre,
        ]);

        return ['success' => 1];
    }








    //******************** EMPLEADOS *************************************************************

    public function vistaEmpleados()
    {
        // $listado con eager loading — sin N+1
        $listado = Empleado::with(['unidadEmpleado.distrito', 'cargo', 'jefe'])
            ->orderBy('nombre', 'ASC')
            ->get()
            ->map(function ($item) {
                $item->unidad      = $item->unidadEmpleado->nombre          ?? '—';
                $item->distrito    = $item->unidadEmpleado->distrito->nombre ?? '—';
                $item->cargo       = $item->cargo->nombre                   ?? '—';
                $item->jefe_nombre = $item->jefe->nombre                    ?? null;
                return $item;
            });

        $arrayDistrito  = Distrito::orderBy('nombre', 'ASC')->get();
        $arrayCargo     = Cargo::orderBy('nombre', 'ASC')->get();

        // Solo jefes para el select "Jefe Directo" en el modal
        $arrayEmpleados = Empleado::with('cargo')
            ->where('jefe', true)
            ->orderBy('nombre', 'ASC')
            ->get()
            ->map(function ($e) {
                $e->cargo_nombre = $e->cargo->nombre ?? '—';
                return $e;
            });

        return view('backend.admin.config.empleados.vistaempleados',
            compact('listado', 'arrayDistrito', 'arrayCargo', 'arrayEmpleados'));
    }



    public function tablaEmpleados()
    {
        $listado = Empleado::orderBy('nombre', 'ASC')->get();

        foreach ($listado as $item){

            $infoUnidad = UnidadEmpleado::where('id', $item->id_unidad_empleado)->first();
            $infoDistrito = Distrito::where('id', $infoUnidad->id_distrito)->first();
            $infoCargo = Cargo::where('id', $item->id_cargo)->first();

            $item->unidad = $infoUnidad->nombre;
            $item->cargo = $infoCargo->nombre;
            $item->distrito = $infoDistrito->nombre;
        }

        return view('backend.admin.config.empleados.tablaempleados', compact('listado'));
    }

    public function buscarUnidadConDistrito(Request $request)
    {
        $regla = array(
            'id' => 'required'
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()) {
            return ['success' => 0];
        }

        $arrayUnidad = UnidadEmpleado::where('id_distrito', $request->id)->get();

        return ['success' => 1, 'arrayUnidad' => $arrayUnidad];
    }


    public function buscarUnidadConDistritoEmpleado(Request $request)
    {
        $regla = array(
            'id' => 'required' // id unidad
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()) {
            return ['success' => 0];
        }

        $arrayEmpleados = Empleado::where('id_unidad_empleado', $request->id)->get();

        foreach ($arrayEmpleados as $item){
            $infoCargo = Cargo::where('id', $item->id_cargo)->first();
            $item->nombreCompleto = $item->nombre . ' (' . $infoCargo->nombre . ')';

            if($item->jefe == 1){

                $nombreJefe = "";

                // PORQUE SOY YO MISMO EL JEFE
                // ENTONCES BUSCAR SI QUIEN ES MI JEFE SUPERIOR
                if($datauni = UnidadEmpleado::where('id_empleado', $item->id)->first()){
                    if($datauni->id_empleado_inmediato != null){
                        $datoE = Empleado::where('id', $datauni->id_empleado_inmediato)->first();
                        $nombreJefe = $datoE->nombre;
                    }
                }


                $item->jefe = $nombreJefe;
            }else{

                // BUSCAR EL JEFE DONDE PERTENEZCO EN LA UNIDAD
                if($Dato = Empleado::where('id_unidad_empleado', $item->id_unidad_empleado)
                    ->where('id', '!=', $item->id)
                    ->where('jefe', 1)
                    ->first()){
                    $item->jefe = $Dato->nombre;
                }
            }
        }

        return ['success' => 1, 'arrayEmpleados' => $arrayEmpleados];
    }


    public function nuevoEmpleados(Request $request)
    {
        $validar = Validator::make($request->all(), [
            'nombre' => 'required',
            'unidad' => 'required',
            'cargo'  => 'required',
            'jefe'   => 'required',
        ]);
        if ($validar->fails()) return ['success' => 0];

        DB::beginTransaction();
        try {
            $dato = new Empleado();
            $dato->nombre             = $request->nombre;
            $dato->id_unidad_empleado = $request->unidad;
            $dato->id_cargo           = $request->cargo;
            $dato->jefe               = $request->jefe;
            $dato->dui                = $request->dui;
            $dato->id_jefe            = $request->id_jefe ?: null; // ← nuevo
            $dato->save();

            DB::commit();
            return ['success' => 1];
        } catch (\Throwable $e) {
            Log::error('nuevoEmpleados: ' . $e);
            DB::rollback();
            return ['success' => 99];
        }
    }


    public function infoEmpleados(Request $request)
    {
        $validar = Validator::make($request->all(), ['id' => 'required']);
        if ($validar->fails()) return ['success' => 0];

        $info           = Empleado::findOrFail($request->id);
        $infoUniEmpleado = UnidadEmpleado::findOrFail($info->id_unidad_empleado);
        $arrayDistrito  = Distrito::orderBy('nombre', 'ASC')->get();
        $arrayUnidad    = UnidadEmpleado::orderBy('nombre', 'ASC')->get();
        $arrayCargo     = Cargo::orderBy('nombre', 'ASC')->get();

        // Todos los jefes disponibles excepto el propio empleado
        $arrayEmpleados = Empleado::with('cargo')
            ->where('jefe', true)
            ->where('id', '!=', $request->id)
            ->orderBy('nombre', 'ASC')
            ->get()
            ->map(function ($e) {
                $e->nombre_completo = $e->nombre . ' (' . ($e->cargo->nombre ?? '—') . ')';
                return $e;
            });

        return [
            'success'         => 1,
            'info'            => $info,
            'infoUniEmpleado' => $infoUniEmpleado,
            'arrayDistrito'   => $arrayDistrito,
            'arrayUnidad'     => $arrayUnidad,
            'arrayCargo'      => $arrayCargo,
            'arrayEmpleados'  => $arrayEmpleados, // ← nuevo
        ];
    }

    public function actualizarEmpleados(Request $request)
    {
        $validar = Validator::make($request->all(), [
            'id'     => 'required',
            'nombre' => 'required',
            'unidad' => 'required',
            'cargo'  => 'required',
            'jefe'   => 'required',
        ]);
        if ($validar->fails()) return ['success' => 0];

        Empleado::where('id', $request->id)->update([
            'nombre'             => $request->nombre,
            'id_unidad_empleado' => $request->unidad,
            'id_cargo'           => $request->cargo,
            'jefe'               => $request->jefe,
            'dui'                => $request->dui,
            'id_jefe'            => $request->id_jefe ?: null, // ← nuevo
        ]);

        return ['success' => 1];
    }


    //******************** PROVEEDOR *************************************************************

    public function vistaProveedor()
    {
        return view('backend.admin.config.proveedor.vistaproveedor');
    }

    public function tablaProveedor()
    {
        $listado = Proveedor::orderBy('nombre', 'ASC')->get();

        return view('backend.admin.config.proveedor.tablaproveedor', compact('listado'));
    }

    public function nuevoProveedor(Request $request)
    {
        $regla = array(
            'nombre' => 'required'
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()) {
            return ['success' => 0];
        }
        DB::beginTransaction();

        try {
            $dato = new Proveedor();
            $dato->nombre = $request->nombre;
            $dato->telefono = $request->telefono;
            $dato->save();

            DB::commit();
            return ['success' => 1];
        } catch (\Throwable $e) {
            Log::info('error ' . $e);
            DB::rollback();
            return ['success' => 99];
        }
    }

    public function infoProveedor(Request $request){
        $regla = array(
            'id' => 'required'
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()) {
            return ['success' => 0];
        }

        $info = Proveedor::where('id', $request->id)->first();

        return ['success' => 1, 'info' => $info];
    }


    public function actualizarProveedor(Request $request)
    {
        $regla = array(
            'id' => 'required',
            'nombre' => 'required'
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()) {
            return ['success' => 0];
        }

        Proveedor::where('id', $request->id)->update([
            'nombre' => $request->nombre,
            'telefono' => $request->telefono
        ]);

        return ['success' => 1];
    }








}
