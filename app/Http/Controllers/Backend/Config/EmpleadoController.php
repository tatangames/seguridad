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

class EmpleadoController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }


    public function index()
    {
        $listado = Empleado::with(['unidadEmpleado.distrito', 'cargo', 'jefeDirecto'])->get();
        $arrayDistrito = Distrito::orderBy('nombre')->get();

        return view('backend.admin.config.empleados.vistaempleados', compact('listado', 'arrayDistrito'));
    }

    public function crear()
    {
        $arrayDistrito = Distrito::orderBy('nombre')->get();
        $arrayCargo    = Cargo::orderBy('nombre')->get();
        $arrayEmpleados = Empleado::where('jefe', 1)
            ->orderBy('nombre')
            ->get();

        return view('backend.admin.config.empleados.vistanuevoempleado', compact('arrayDistrito', 'arrayCargo', 'arrayEmpleados'));
    }

    public function editar($id)
    {
        $empleado = Empleado::with('unidadEmpleado')->findOrFail($id);
        $arrayDistrito = Distrito::orderBy('nombre')->get();
        $arrayCargo    = Cargo::orderBy('nombre')->get();

        // Usar unidadEmpleado en lugar de unidad
        $idDistrito  = $empleado->unidadEmpleado?->id_distrito ?? null;

        $arrayUnidad = $idDistrito
            ? UnidadEmpleado::where('id_distrito', $idDistrito)->orderBy('nombre')->get()
            : collect();

        $arrayEmpleados = Empleado::where('jefe', 1)
            ->where('id', '!=', $id)
            ->orderBy('nombre')
            ->get();

        return view('backend.admin.config.empleados.vistaeditarempleado', compact(
            'empleado', 'arrayDistrito', 'arrayCargo', 'arrayUnidad', 'arrayEmpleados'
        ));
    }

    public function buscarUnidad(Request $request)
    {
        $unidades = UnidadEmpleado::where('id_distrito', $request->id)
            ->orderBy('nombre')->get();

        return response()->json([
            'success'     => 1,
            'arrayUnidad' => $unidades
        ]);
    }

    public function nuevo(Request $request)
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

    public function actualizar(Request $request)
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


}
