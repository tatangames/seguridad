<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Frontend\Login\LoginController;
use App\Http\Controllers\Controles\ControlController;
use App\Http\Controllers\Backend\Roles\RolesController;
use App\Http\Controllers\Backend\Roles\PermisoController;
use App\Http\Controllers\Backend\Perfil\PerfilController;
use App\Http\Controllers\Backend\Config\ConfiguracionController;
use App\Http\Controllers\Backend\Materiales\MaterialesController;
use App\Http\Controllers\Backend\Materiales\RegistrosController;
use App\Http\Controllers\Backend\Materiales\HistorialController;
use App\Http\Controllers\Backend\Config\ReportesController;





// --- LOGIN ---

Route::get('/', [LoginController::class,'index'])->name('login');

Route::post('/admin/login', [LoginController::class, 'login']);
Route::post('/admin/logout', [LoginController::class, 'logout'])->name('admin.logout');

// --- CONTROL WEB ---

Route::get('/panel', [ControlController::class,'indexRedireccionamiento'])->name('admin.panel');

// --- ROLES ---

Route::get('/admin/roles/index', [RolesController::class,'index'])->name('admin.roles.index');
Route::get('/admin/roles/tabla', [RolesController::class,'tablaRoles']);
Route::get('/admin/roles/lista/permisos/{id}', [RolesController::class,'vistaPermisos']);
Route::get('/admin/roles/permisos/tabla/{id}', [RolesController::class,'tablaRolesPermisos']);
Route::post('/admin/roles/permiso/borrar', [RolesController::class, 'borrarPermiso']);
Route::post('/admin/roles/permiso/agregar', [RolesController::class, 'agregarPermiso']);
Route::get('/admin/roles/permisos/lista', [RolesController::class,'listaTodosPermisos']);
Route::get('/admin/roles/permisos-todos/tabla', [RolesController::class,'tablaTodosPermisos']);
Route::post('/admin/roles/borrar-global', [RolesController::class, 'borrarRolGlobal']);

// --- PERMISOS A USUARIOS ---

Route::get('/admin/permisos/index', [PermisoController::class,'index'])->name('admin.permisos.index');
Route::get('/admin/permisos/tabla', [PermisoController::class,'tablaUsuarios']);
Route::post('/admin/permisos/nuevo-usuario', [PermisoController::class, 'nuevoUsuario']);
Route::post('/admin/permisos/info-usuario', [PermisoController::class, 'infoUsuario']);
Route::post('/admin/permisos/editar-usuario', [PermisoController::class, 'editarUsuario']);
Route::post('/admin/permisos/nuevo-rol', [PermisoController::class, 'nuevoRol']);
Route::post('/admin/permisos/extra-nuevo', [PermisoController::class, 'nuevoPermisoExtra']);
Route::post('/admin/permisos/extra-borrar', [PermisoController::class, 'borrarPermisoGlobal']);

// --- PERFIL DE USUARIO ---
Route::get('/admin/editar-perfil/index', [PerfilController::class,'indexEditarPerfil'])->name('admin.perfil');
Route::post('/admin/editar-perfil/actualizar', [PerfilController::class, 'editarUsuario']);

// --- SIN PERMISOS VISTA 403 ---
Route::get('sin-permisos', [ControlController::class,'indexSinPermiso'])->name('no.permisos.index');


// UNIDAD DE MEDIDA
Route::get('/admin/unidadmedida/index', [ConfiguracionController::class,'vistaUnidadMedida'])->name('admin.unidadmedida.index');
Route::get('/admin/unidadmedida/tabla', [ConfiguracionController::class,'tablaUnidadMedida']);
Route::post('/admin/unidadmedida/nuevo', [ConfiguracionController::class,'nuevoUnidadMedida']);
Route::post('/admin/unidadmedida/informacion', [ConfiguracionController::class,'infoUnidadMedida']);
Route::post('/admin/unidadmedida/editar', [ConfiguracionController::class,'actualizarUnidadMedida']);


// MARCA
Route::get('/admin/marca/index', [ConfiguracionController::class,'vistaMarca'])->name('admin.marca.index');
Route::get('/admin/marca/tabla', [ConfiguracionController::class,'tablaMarca']);
Route::post('/admin/marca/nuevo', [ConfiguracionController::class,'nuevoMarca']);
Route::post('/admin/marca/informacion', [ConfiguracionController::class,'infoMarca']);
Route::post('/admin/marca/editar', [ConfiguracionController::class,'actualizarMarca']);

// NORMATIVA
Route::get('/admin/normativa/index', [ConfiguracionController::class,'vistaNormativa'])->name('admin.normativa.index');
Route::get('/admin/normativa/tabla', [ConfiguracionController::class,'tablaNormativa']);
Route::post('/admin/normativa/nuevo', [ConfiguracionController::class,'nuevoNormativa']);
Route::post('/admin/normativa/informacion', [ConfiguracionController::class,'infoNormativa']);
Route::post('/admin/normativa/editar', [ConfiguracionController::class,'actualizarNormativa']);


// COLOR
Route::get('/admin/color/index', [ConfiguracionController::class,'vistaColor'])->name('admin.color.index');
Route::get('/admin/color/tabla', [ConfiguracionController::class,'tablaColor']);
Route::post('/admin/color/nuevo', [ConfiguracionController::class,'nuevoColor']);
Route::post('/admin/color/informacion', [ConfiguracionController::class,'infoColor']);
Route::post('/admin/color/editar', [ConfiguracionController::class,'actualizarColor']);

// TALLA
Route::get('/admin/talla/index', [ConfiguracionController::class,'vistaTalla'])->name('admin.talla.index');
Route::get('/admin/talla/tabla', [ConfiguracionController::class,'tablaTalla']);
Route::post('/admin/talla/nuevo', [ConfiguracionController::class,'nuevoTalla']);
Route::post('/admin/talla/informacion', [ConfiguracionController::class,'infoTalla']);
Route::post('/admin/talla/editar', [ConfiguracionController::class,'actualizarTalla']);

// MATERIALES
Route::get('/admin/materiales/index', [MaterialesController::class,'indexMateriales'])->name('admin.materiales.index');
Route::get('/admin/materiales/tabla/index', [MaterialesController::class,'tablaMateriales']);
Route::post('/admin/materiales/nuevo', [MaterialesController::class, 'nuevoMaterial']);
Route::post('/admin/materiales/informacion', [MaterialesController::class, 'informacionMaterial']);
Route::post('/admin/materiales/editar', [MaterialesController::class, 'editarMaterial']);

// BUSCAR MATERIAL PARA OBTENER CATEGORIA CODIGO
Route::post('/admin/materiales/buscarmaterial',  [MaterialesController::class,'buscadorMaterialCodigoCategoria']);

// DETALLE
Route::get('/admin/material/detalle/{id}', [MaterialesController::class,'vistaDetalleMaterial']);
Route::get('/admin/material/detalle/tabla/{id}', [MaterialesController::class,'tablaDetalleMaterial']);

// MOVIMIENTOS DETALLE
Route::get('/admin/material/movimientos/detalle/{id}', [MaterialesController::class,'vistaMovimientosDetalleMaterial']);
Route::get('/admin/material/movimientos/detalle/tabla/{id}', [MaterialesController::class,'tablaMovimientosDetalleMaterial']);


// UNIDAD EMPLEADO
Route::get('/admin/distrito/index', [ConfiguracionController::class,'vistaDistrito'])->name('admin.distrito.index');
Route::get('/admin/distrito/tabla', [ConfiguracionController::class,'tablaDistrito']);
Route::post('/admin/distrito/nuevo', [ConfiguracionController::class,'nuevoDistrito']);
Route::post('/admin/distrito/informacion', [ConfiguracionController::class,'infoDistrito']);
Route::post('/admin/distrito/editar', [ConfiguracionController::class,'actualizarDistrito']);


// UNIDAD EMPLEADO
Route::get('/admin/unidadempleado/index', [ConfiguracionController::class,'vistaUnidadEmpleado'])->name('admin.unidad.empleado.index');
Route::get('/admin/unidadempleado/tabla', [ConfiguracionController::class,'tablaUnidadEmpleado']);
Route::post('/admin/unidadempleado/nuevo', [ConfiguracionController::class,'nuevoUnidadEmpleado']);
Route::post('/admin/unidadempleado/informacion', [ConfiguracionController::class,'infoUnidadEmpleado']);
Route::post('/admin/unidadempleado/editar', [ConfiguracionController::class,'actualizarUnidadEmpleado']);
Route::post('/admin/unidadempleado/jefeinmediato/informacion', [ConfiguracionController::class,'informacionJefeInmediato']);
Route::post('/admin/unidadempleado/jefeinmediato/editar', [ConfiguracionController::class,'editarJefeInmediato']);

// CARGOS
Route::get('/admin/cargo/index', [ConfiguracionController::class,'vistaCargo'])->name('admin.cargo.index');
Route::get('/admin/cargo/tabla', [ConfiguracionController::class,'tablaCargo']);
Route::post('/admin/cargo/nuevo', [ConfiguracionController::class,'nuevoCargo']);
Route::post('/admin/cargo/informacion', [ConfiguracionController::class,'infoCargo']);
Route::post('/admin/cargo/editar', [ConfiguracionController::class,'actualizarCargo']);

// EMPLEADO
Route::get('/admin/empleados/index', [ConfiguracionController::class,'vistaEmpleados'])->name('admin.empleados.index');
Route::get('/admin/empleados/tabla', [ConfiguracionController::class,'tablaEmpleados']);

Route::post('/admin/empleados/buscarunidad', [ConfiguracionController::class,'buscarUnidadConDistrito']);
Route::post('/admin/empleados/buscarunidad-empleado', [ConfiguracionController::class,'buscarUnidadConDistritoEmpleado']);

Route::post('/admin/empleados/nuevo', [ConfiguracionController::class,'nuevoEmpleados']);
Route::post('/admin/empleados/informacion', [ConfiguracionController::class,'infoEmpleados']);
Route::post('/admin/empleados/editar', [ConfiguracionController::class,'actualizarEmpleados']);

// PROVEEDORES
Route::get('/admin/proveedor/index', [ConfiguracionController::class,'vistaProveedor'])->name('admin.proveedor.index');
Route::get('/admin/proveedor/tabla', [ConfiguracionController::class,'tablaProveedor']);
Route::post('/admin/proveedor/nuevo', [ConfiguracionController::class,'nuevoProveedor']);
Route::post('/admin/proveedor/informacion', [ConfiguracionController::class,'infoProveedor']);
Route::post('/admin/proveedor/editar', [ConfiguracionController::class,'actualizarProveedor']);








// REGISTRO DE ENTRADAS
Route::get('/admin/entradas/vista', [RegistrosController::class,'indexRegistroEntrada'])->name('admin.entrada.registro.index');
Route::post('/admin/buscar/material',  [RegistrosController::class,'buscadorMaterialGlobal']);
Route::post('/admin/entradas/guardar',  [RegistrosController::class,'guardarEntrada']);

// REGISTRO DE SALIDAS
Route::get('/admin/salidas/vista', [RegistrosController::class,'indexRegistroSalida'])->name('admin.salidas.registro.index');
Route::post('/admin/buscar/material/disponible',  [RegistrosController::class,'buscadorMaterialDisponible']);
Route::post('/admin/buscar/material/disponibilidad', [RegistrosController::class, 'infoBodegaMaterialDetalleFila']);

Route::post('/admin/salida/guardar',  [RegistrosController::class,'guardarSalidaMateriales']);

// TEMPORAL PARA EL REPORTE PDF
Route::post('/admin/salida/guardar-temporal',  [RegistrosController::class,'guardarSalidaMaterialesTemporal']);
Route::get('/admin/salidas/pdf-temporal', [RegistrosController::class,'generarPdfTemporal']);
Route::get('/admin/salidas/pdfcompleto/{idsalida}', [RegistrosController::class,'generarPdfSalida']);




// RETORNOS
Route::get('/admin/retornos/index', [RegistrosController::class,'vistaRetorno'])->name('admin.retorno.registro.index');
Route::get('/admin/retornos/tabla', [RegistrosController::class,'tablaRetorno']);
Route::post('/admin/retornos/informacion',  [RegistrosController::class,'informacionRetorno']);
Route::post('/admin/retornos/registrar',  [RegistrosController::class,'registrarRetorno']);
Route::post('/admin/retornos/descartar/registrar',  [RegistrosController::class,'registrarDescarte']);
Route::post('/admin/retornos/borrar',  [RegistrosController::class,'borrarRetornoDescarte']);


// HISTORIAL - SALIDAS
Route::get('/admin/historial/salidas/index', [HistorialController::class,'indexHistorialSalidas'])->name('sidebar.historial.salidas');
Route::get('/admin/historial/salidas/tabla', [HistorialController::class,'tablaHistorialSalidas']);
Route::post('/admin/historial/salidas/informacion',  [HistorialController::class,'informacionHistorialSalida']);
Route::post('/admin/historial/salidas/editar',  [HistorialController::class,'editarHistorialSalida']);



Route::get('/admin/historial/salidadetalle/index/{id}', [HistorialController::class,'indexHistorialSalidasDetalle']);
Route::get('/admin/historial/salidadetalle/tabla/{id}', [HistorialController::class,'tablaHistorialSalidasDetalle']);


// index movimientos salidas detalle
Route::get('/admin/historial/salidadetalle/movimientos/index/{id}', [HistorialController::class,'indexHistorialSalidasDetalleMovimientos']);
Route::get('/admin/historial/salidadetalle/movimientos/tabla/{id}', [HistorialController::class,'tablaHistorialSalidasDetalleMovimientos']);

// borra salidas_detalle y sus movimientos
Route::post('/admin/historial/salidadetalle/borraritem', [HistorialController::class,'salidaDetalleBorrarItem']);



// HISTORIAL - ENTRADAS
Route::get('/admin/historial/entrada/index', [HistorialController::class,'indexHistorialEntradas'])->name('sidebar.historial.entradas');
Route::get('/admin/historial/entrada/tabla', [HistorialController::class,'tablaHistorialEntradas']);
Route::post('/admin/historial/entrada/informacion',  [HistorialController::class,'informacionHistorialEntrada']);
Route::post('/admin/historial/entrada/editar',  [HistorialController::class,'editarHistorialEntrada']);


// - Detalle
Route::get('/admin/historial/entradadetalle/index/{id}', [HistorialController::class,'indexHistorialEntradasDetalle']);
Route::get('/admin/historial/entradadetalle/tabla/{id}', [HistorialController::class,'tablaHistorialEntradasDetalle']);

// vista para ingresar nuevo producto al lote existente
Route::get('/admin/historial/nuevoingresoentradadetalle/index/{id}', [HistorialController::class,'indexNuevoIngresoEntradaDetalle']);
Route::post('/admin/registrar/productosextras',  [HistorialController::class,'registrarProductosExtras']);

// BORRAR ENTRADA COMPLETA DE PRODUCTOS -> ELIMINARA SALIDAS SI HUBIERON
Route::post('/admin/historial/entrada/borrarlote', [HistorialController::class, 'historialEntradaBorrarLote']);
Route::post('/admin/historial/entradadetalle/borraritem', [HistorialController::class, 'historialEntradaDetalleBorrarItem']);


// RETORNOS
Route::get('/admin/historial/retornos/index', [HistorialController::class,'indexHistorialRetornos'])->name('sidebar.historial.retornos');
Route::get('/admin/historial/retornos/tabla', [HistorialController::class,'tablaHistorialRetornos']);




// REPORTES
Route::get('/admin/reprotes/general/index', [ReportesController::class,'indexGeneralReportes'])->name('admin.historial.general.index');
Route::get('/admin/reportes/pdf/recibe-separados/{id}', [ReportesController::class,'reporteEmpleadoRecibidos']);
Route::get('/admin/reportes/pdf/kardex-material/{id}', [ReportesController::class,'reporteKardexMaterial']);




// REEMPLAZO MES
Route::get('/admin/reemplazomes/index', [HistorialController::class,'indexReemplazoMes'])->name('admin.reemplazo.mes.index');
Route::get('/admin/reemplazomes/tabla', [HistorialController::class,'tablaReemplazoMes']);


Route::get('/admin/existencia/general/index', [HistorialController::class,'indexExistencias'])->name('admin.existencias.general.index');
Route::get('/admin/existencia/pdf/generar', [HistorialController::class,'reportePdfExistencias']);



