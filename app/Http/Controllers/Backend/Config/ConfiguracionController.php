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

        return view('backend.admin.config.empleados.unidadempleado.vistaunidadempleados', compact('arrayDistritos'));
    }

    public function tablaUnidadEmpleado()
    {
        $listado = UnidadEmpleado::orderBy('nombre', 'ASC')->get();

        foreach ($listado as $item) {
            $infoDistrito = Distrito::where('id', $item->id_distrito)->first();

            $item->distrito = $infoDistrito->nombre;
        }

        return view('backend.admin.config.empleados.unidadempleado.tablaunidadempleados', compact('listado'));
    }


    public function nuevoUnidadEmpleado(Request $request)
    {
        $regla = array(
            'nombre' => 'required',
            'unidad' => 'required'
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()) {
            return ['success' => 0];
        }
        DB::beginTransaction();

        try {
            $dato = new UnidadEmpleado();
            $dato->id_distrito = $request->unidad;
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

    public function infoUnidadEmpleado(Request $request)
    {
        $regla = array(
            'id' => 'required'
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()) {
            return ['success' => 0];
        }

        $info = UnidadEmpleado::where('id', $request->id)->first();

        $arrayDistrito = Distrito::orderBy('nombre', 'ASC')->get();

        return ['success' => 1, 'info' => $info, 'arrayDistrito' => $arrayDistrito];
    }


    public function actualizarUnidadEmpleado(Request $request)
    {
        $regla = array(
            'id' => 'required',
            'nombre' => 'required',
            'distrito' => 'required'
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()) {
            return ['success' => 0];
        }

        UnidadEmpleado::where('id', $request->id)->update([
            'nombre' => $request->nombre,
            'id_distrito' => $request->distrito,
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
        $arrayDistrito = Distrito::orderBy('nombre', 'ASC')->get();
        $arrayUnidad = UnidadEmpleado::orderBy('nombre', 'ASC')->get();
        $arrayCargo = Cargo::orderBy('nombre', 'ASC')->get();

        return view('backend.admin.config.empleados.vistaempleados', compact('arrayDistrito', 'arrayUnidad', 'arrayCargo'));
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


    public function nuevoEmpleados(Request $request)
    {
        $regla = array(
            'nombre' => 'required',
            'unidad' => 'required',
            'cargo' => 'required'
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()) {
            return ['success' => 0];
        }
        DB::beginTransaction();

        try {
            $dato = new Empleado();
            $dato->nombre = $request->nombre;
            $dato->id_unidad_empleado = $request->unidad;
            $dato->id_cargo = $request->cargo;
            $dato->save();

            DB::commit();
            return ['success' => 1];
        } catch (\Throwable $e) {
            Log::info('error ' . $e);
            DB::rollback();
            return ['success' => 99];
        }
    }


    public function infoEmpleados(Request $request)
    {
        $regla = array(
            'id' => 'required'
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()) {
            return ['success' => 0];
        }

        $info = Empleado::where('id', $request->id)->first();
        $infoUniEmpleado = UnidadEmpleado::where('id', $info->id_unidad_empleado)->first();

        $arrayDistrito = Distrito::orderBy('nombre', 'ASC')->get();
        $arrayUnidad = UnidadEmpleado::orderBy('nombre', 'ASC')->get();
        $arrayCargo = Cargo::orderBy('nombre', 'ASC')->get();

        return ['success' => 1, 'info' => $info, 'arrayDistrito' => $arrayDistrito, 'arrayCargo' => $arrayCargo,
            'arrayUnidad' => $arrayUnidad, 'infoUniEmpleado' => $infoUniEmpleado];
    }

    public function actualizarEmpleados(Request $request)
    {
        $regla = array(
            'id' => 'required',
            'nombre' => 'required',
            'unidad' => 'required',
            'cargo' => 'required'
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()) {
            return ['success' => 0];
        }

        Empleado::where('id', $request->id)->update([
            'nombre' => $request->nombre,
            'id_unidad_empleado' => $request->unidad,
            'id_cargo' => $request->cargo,
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
