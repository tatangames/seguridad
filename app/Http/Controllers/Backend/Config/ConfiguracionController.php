<?php

namespace App\Http\Controllers\Backend\Config;

use App\Http\Controllers\Controller;
use App\Models\Color;
use App\Models\Empleado;
use App\Models\Marca;
use App\Models\Normativa;
use App\Models\Talla;
use App\Models\UnidadMedida;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class ConfiguracionController extends Controller
{
    public function __construct(){
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




    //******************** EMPLEADOS *************************************************************


    public function vistaEmpleado()
    {
        return view('backend.admin.config.empleados.vistaempleados');
    }

    public function tablaEmpleado()
    {
        $lista = Empleado::orderBy('nombre', 'ASC')->get();

        return view('backend.admin.config.empleados.tablaempleados', compact('lista'));
    }


    public function nuevoEmpleado(Request $request)
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
            $dato = new Empleado();
            $dato->nombre = $request->nombre;
            $dato->unidad = $request->unidad;
            $dato->cargo = $request->cargo;
            $dato->save();

            DB::commit();
            return ['success' => 1];
        } catch (\Throwable $e) {
            Log::info('error ' . $e);
            DB::rollback();
            return ['success' => 99];
        }
    }


    public function infoEmpleado(Request $request)
    {
        $regla = array(
            'id' => 'required'
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()) {
            return ['success' => 0];
        }

        $info = Empleado::where('id', $request->id)->first();

        return ['success' => 1, 'info' => $info];
    }

    public function actualizarEmpleado(Request $request)
    {
        $regla = array(
            'id' => 'required',
            'nombre' => 'required'
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()) {
            return ['success' => 0];
        }

        Empleado::where('id', $request->id)->update([
            'nombre' => $request->nombre,
            'unidad' => $request->unidad,
            'cargo' => $request->cargo,
        ]);

        return ['success' => 1];
    }












}
