@extends('backend.menus.superior')

@section('content-admin-css')
    <link href="{{ asset('css/adminlte.min.css') }}" type="text/css" rel="stylesheet" />
    <link href="{{ asset('css/dataTables.bootstrap4.css') }}" type="text/css" rel="stylesheet" />
    <link href="{{ asset('css/toastr.min.css') }}" type="text/css" rel="stylesheet" />
    <link href="{{ asset('css/buttons_estilo.css') }}" rel="stylesheet">
    <link href="{{ asset('css/select2.min.css') }}" type="text/css" rel="stylesheet">
    <link href="{{ asset('css/select2-bootstrap-5-theme.min.css') }}" type="text/css" rel="stylesheet">
    <link href="{{ asset('css/estiloToggle.css') }}" type="text/css" rel="stylesheet" />
@stop

<style>
    table { table-layout: fixed; }

    .filtros-panel {
        background: linear-gradient(135deg, #1a3a6b 0%, #2156af 100%);
        border-radius: 10px;
        padding: 18px 22px 10px;
        margin-bottom: 18px;
        box-shadow: 0 4px 18px rgba(33,86,175,0.18);
    }
    .filtros-panel label {
        color: #c8d8f8; font-size: 11px; font-weight: 700;
        letter-spacing: .07em; text-transform: uppercase;
        margin-bottom: 4px; display: block;
    }
    .filtros-panel .form-control,
    .filtros-panel .custom-select {
        background: rgba(255,255,255,0.10);
        border: 1px solid rgba(255,255,255,0.22);
        color: #fff; border-radius: 6px;
        font-size: 13px; height: 34px;
        transition: background .2s, border .2s;
    }
    .filtros-panel .form-control::placeholder { color: rgba(255,255,255,.5); }
    .filtros-panel .form-control:focus,
    .filtros-panel .custom-select:focus {
        background: rgba(255,255,255,0.18); border-color: #82aaff;
        color: #fff; box-shadow: none; outline: none;
    }
    .filtros-panel .custom-select option { color: #222; background: #fff; }
    .filtros-panel .btn-limpiar {
        background: rgba(255,255,255,0.15);
        border: 1px solid rgba(255,255,255,0.3);
        color: #fff; border-radius: 6px; font-size: 12px;
        height: 34px; padding: 0 16px; cursor: pointer;
        transition: background .2s; width: 100%;
    }
    .filtros-panel .btn-limpiar:hover { background: rgba(255,255,255,0.28); }

    .resumen-badges { margin-bottom: 14px; }
    .resumen-badges .badge-stat {
        display: inline-flex; align-items: center; gap: 6px;
        background: #f0f4ff; border: 1px solid #d0dcf7; color: #2156af;
        border-radius: 20px; padding: 4px 13px;
        font-size: 12px; font-weight: 600;
        margin-right: 8px; margin-bottom: 6px;
    }
    .resumen-badges .badge-stat .dot { width: 8px; height: 8px; border-radius: 50%; flex-shrink: 0; }
    .resumen-badges .badge-stat .dot.total { background: #6c757d; }
    .resumen-badges .badge-stat .dot.con   { background: #28a745; }
    .resumen-badges .badge-stat .dot.sin   { background: #dc3545; }

    #tablaUnidad thead tr th {
        background: #2156af; color: #fff;
        font-size: 12px; font-weight: 700;
        letter-spacing: .04em; text-transform: uppercase;
        border: none !important; white-space: nowrap; padding: 10px 12px;
    }
    #tablaUnidad tbody tr { transition: background .15s; }
    #tablaUnidad tbody tr:hover { background: #eef3ff !important; }
    #tablaUnidad tbody td { vertical-align: middle; font-size: 13px; padding: 8px 12px; }

    .nombre-uni { font-weight: 600; color: #1a2d55; }

    .badge-jefe-asig {
        background: #d4edda; color: #155724;
        border: 1px solid #c3e6cb; border-radius: 12px;
        padding: 2px 8px; font-size: 11px; font-weight: 600;
        display: inline-block; margin: 1px 2px;
    }
    .badge-sin-jefe {
        background: #f8d7da; color: #721c24;
        border: 1px solid #f5c6cb; border-radius: 12px;
        padding: 2px 9px; font-size: 11px; font-weight: 700;
        display: inline-block;
    }

    .dataTables_wrapper .dataTables_filter { display: none; }
    div.dataTables_wrapper div.dataTables_length select { min-width: 60px; }
    .dataTables_info { font-size: 12px; color: #888; }
</style>

<div id="divcontenedor" style="display: none">

    <section class="content-header">
        <div class="row mb-2">
            <div class="col-sm-6">
                <button type="button"
                        style="font-weight:bold; background-color:#2156af; color:white !important;"
                        id="btn-nueva-unidad"
                        class="button button-3d button-rounded button-pill button-small">
                    <i class="fas fa-pencil-alt"></i> Nueva Unidad
                </button>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item">Unidad</li>
                    <li class="breadcrumb-item active">Listado de Unidades</li>
                </ol>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">

            {{-- Filtros --}}
            <div class="filtros-panel">
                <div class="row align-items-end">
                    <div class="col-md-4 col-sm-6 mb-2">
                        <label><i class="fas fa-search mr-1"></i>Buscar</label>
                        <input type="text" id="filtro-buscar" class="form-control" placeholder="Nombre de unidad…">
                    </div>
                    <div class="col-md-4 col-sm-6 mb-2">
                        <label><i class="fas fa-map-marker-alt mr-1"></i>Distrito</label>
                        <select id="filtro-distrito" class="form-control custom-select">
                            <option value="">Todos los distritos</option>
                        </select>
                    </div>
                    <div class="col-md-3 col-sm-6 mb-2">
                        <label><i class="fas fa-user-tie mr-1"></i>Estado Jefe</label>
                        <select id="filtro-jefe" class="form-control custom-select">
                            <option value="">Todos</option>
                            <option value="con">Con jefe asignado</option>
                            <option value="sin">Sin jefe asignado</option>
                        </select>
                    </div>
                    <div class="col-md-1 col-sm-6 mb-2 d-flex align-items-end">
                        <button class="btn-limpiar" id="btn-limpiar-filtros">
                            <i class="fas fa-times mr-1"></i> Limpiar
                        </button>
                    </div>
                </div>
            </div>

            {{-- Badges --}}
            <div class="resumen-badges">
                <span class="badge-stat"><span class="dot total"></span> Total: <strong id="ucnt-total">0</strong></span>
                <span class="badge-stat"><span class="dot con"></span> Con jefe: <strong id="ucnt-con">0</strong></span>
                <span class="badge-stat"><span class="dot sin"></span> Sin jefe: <strong id="ucnt-sin">0</strong></span>
            </div>

            {{-- Tabla --}}
            <div class="row">
                <div class="col-12">
                    <div class="card" style="border-radius:10px; box-shadow:0 2px 16px rgba(0,0,0,.07); border:none;">
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table id="tablaUnidad" class="table table-bordered table-striped mb-0" style="table-layout:fixed; width:100%">
                                    <thead>
                                    <tr>
                                        <th style="width:25%">Nombre</th>
                                        <th style="width:16%">Distrito</th>
                                        <th style="width:35%">Jefes Asignados</th>
                                        <th style="width:24%">Opciones</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($listado as $dato)
                                        <tr data-distrito="{{ $dato->distrito }}"
                                            data-tienejefe="{{ $dato->jefes->count() > 0 ? 'con' : 'sin' }}">
                                            <td class="nombre-uni">{{ $dato->nombre }}</td>
                                            <td>{{ $dato->distrito }}</td>
                                            <td>
                                                @forelse($dato->jefes as $j)
                                                    <span class="badge-jefe-asig">
                                                        <i class="fas fa-user-tie" style="font-size:9px"></i>
                                                        {{ $j->nombre }}
                                                    </span>
                                                @empty
                                                    <span class="badge-sin-jefe">Sin asignar</span>
                                                @endforelse
                                            </td>
                                            <td>
                                                {{-- Sin onclick inline: usamos data-* y clases para delegación --}}
                                                <button type="button"
                                                        class="btn btn-info btn-xs mb-1 btn-editar-unidad"
                                                        data-id="{{ $dato->id }}"
                                                        title="Editar unidad">
                                                    <i class="fas fa-edit"></i> Editar
                                                </button>
                                                <button type="button"
                                                        class="btn btn-success btn-xs btn-gestionar-jefes"
                                                        data-id="{{ $dato->id }}"
                                                        data-nombre="{{ addslashes($dato->nombre) }}"
                                                        title="Gestionar jefes">
                                                    <i class="fas fa-user-tie"></i> Gestionar Jefes
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </section>

    {{-- ══ MODAL AGREGAR UNIDAD ══ --}}
    <div class="modal fade" id="modalAgregar">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Nueva Unidad</h4>
                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <div class="modal-body">
                    <form id="formulario-nuevo">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>Distrito:</label>
                                        <select class="form-control" id="select-distrito">
                                            @foreach($arrayDistritos as $sel)
                                                <option value="{{ $sel->id }}">{{ $sel->nombre }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label>Nombre de Unidad</label>
                                        <input type="text" maxlength="100" class="form-control" id="unidad-nuevo" autocomplete="off">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                    <button type="button"
                            style="font-weight:bold; background-color:#2156af; color:white !important;"
                            class="button button-rounded button-pill button-small"
                            id="btn-guardar-nuevo">Guardar</button>
                </div>
            </div>
        </div>
    </div>

    {{-- ══ MODAL EDITAR UNIDAD ══ --}}
    <div class="modal fade" id="modalEditar">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Editar Unidad</h4>
                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <div class="modal-body">
                    <form id="formulario-editar">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <input type="hidden" id="id-editar">
                                    <div class="form-group">
                                        <label>Distrito:</label>
                                        <select class="form-control" id="select-distrito-editar"></select>
                                    </div>
                                    <div class="form-group">
                                        <label>Nombre de Unidad</label>
                                        <input type="text" maxlength="100" class="form-control" id="unidad-editar" autocomplete="off">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                    <button type="button"
                            style="font-weight:bold; background-color:#28a745; color:white !important;"
                            class="button button-rounded button-pill button-small"
                            id="btn-guardar-editar">Actualizar</button>
                </div>
            </div>
        </div>
    </div>

    {{-- ══ MODAL GESTIONAR JEFES (jefe_unidad pivote) ══ --}}
    <div class="modal fade" id="modalJefes">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">
                        Jefes de: <span id="titulo-unidad" class="text-primary"></span>
                    </h4>
                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="id-unidad-jefes">

                    <p class="text-muted" style="font-size:12px">
                        Selecciona uno o varios empleados que serán jefes responsables de esta unidad.
                        Se guardan en la tabla <code>jefe_unidad</code>.
                    </p>

                    <div class="form-group">
                        <label>Agregar jefe a esta unidad:</label>
                        <div class="input-group">
                            <select class="form-control" id="select-agregar-jefe">
                                <option value="">Seleccionar empleado…</option>
                            </select>
                            <div class="input-group-append">
                                <button class="btn btn-primary btn-sm" id="btn-agregar-jefe">
                                    <i class="fas fa-plus"></i> Agregar
                                </button>
                            </div>
                        </div>
                    </div>

                    <label>Jefes actualmente asignados:</label>
                    <div id="lista-jefes-asignados" style="min-height:40px">
                        <small class="text-muted">Cargando…</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

</div>{{-- fin #divcontenedor --}}


@extends('backend.menus.footerjs')
@section('archivos-js')

    <script src="{{ asset('js/jquery.dataTables.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/dataTables.bootstrap4.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/toastr.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/axios.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/sweetalert2.all.min.js') }}"></script>
    <script src="{{ asset('js/alertaPersonalizada.js') }}"></script>
    <script src="{{ asset('js/select2.min.js') }}" type="text/javascript"></script>

    <script type="text/javascript">

        /* ════════════════════════════════════════════
           INICIALIZACIÓN
        ════════════════════════════════════════════ */
        $(document).ready(function () {

            var select2Opts = {
                theme: "bootstrap-5",
                language: { noResults: function () { return "Búsqueda no encontrada"; } }
            };
            $('#select-distrito, #select-distrito-editar, #select-agregar-jefe').select2(select2Opts);

            /* ── DataTable ── */
            var dtU = $("#tablaUnidad").DataTable({
                "paging"      : true,
                "searching"   : true,
                "ordering"    : true,
                "order"       : [[0, 'asc']],
                "info"        : true,
                "autoWidth"   : false,
                "responsive"  : true,
                "pagingType"  : "full_numbers",
                "lengthMenu"  : [[25, 50, 100, -1], [25, 50, 100, "Todo"]],
                "pageLength"  : 25,
                "language": {
                    "sLengthMenu"   : "Mostrar _MENU_ registros",
                    "sZeroRecords"  : "No se encontraron resultados",
                    "sEmptyTable"   : "Ningún dato disponible",
                    "sInfo"         : "Registros del _START_ al _END_ de _TOTAL_",
                    "sInfoEmpty"    : "Registros del 0 al 0 de 0",
                    "sInfoFiltered" : "(filtrado de _MAX_ registros)",
                    "oPaginate"     : { "sFirst": "«", "sLast": "»", "sNext": "›", "sPrevious": "‹" }
                },
                "drawCallback": actualizarContadoresUnidad
            });

            /* ── Poblar filtro Distrito ── */
            var distritos = [];
            $("#tablaUnidad tbody tr").each(function () {
                var d = $(this).data('distrito');
                if (d && !distritos.includes(d)) distritos.push(d);
            });
            distritos.sort().forEach(function (d) {
                $('#filtro-distrito').append('<option value="' + d + '">' + d + '</option>');
            });

            /* ── Filtro personalizado ── */
            $.fn.dataTable.ext.search.push(function (settings, data, dataIndex) {
                if (settings.nTable.id !== 'tablaUnidad') return true;
                var buscar   = $('#filtro-buscar').val().toLowerCase().trim();
                var distrito = $('#filtro-distrito').val();
                var jefe     = $('#filtro-jefe').val();
                var nombre     = (data[0] || '').toLowerCase();
                var trDistrito = $(dtU.row(dataIndex).node()).data('distrito')  || '';
                var trJefe     = $(dtU.row(dataIndex).node()).data('tienejefe') || '';
                if (buscar   && nombre.indexOf(buscar) === -1) return false;
                if (distrito && trDistrito !== distrito)        return false;
                if (jefe     && trJefe     !== jefe)            return false;
                return true;
            });

            $('#filtro-buscar').on('input', function () {
                dtU.draw();
            });

            $('#filtro-distrito, #filtro-jefe').on('change', function () {
                // Cerrar cualquier dropdown de Select2 abierto antes de redibujar
                // para evitar que su overlay intercepte el siguiente clic en la tabla
                if ($(this).hasClass('select2-hidden-accessible')) {
                    $(this).select2('close');
                }
                dtU.draw();
            });

            actualizarContadoresUnidad();
            document.getElementById("divcontenedor").style.display = "block";

            /* ════════════════════════════════════════════
               DELEGACIÓN DE EVENTOS
               — Usamos $(document).on() para que funcione
                 aunque DataTables reconstruya el DOM y
                 aunque Select2 haya tenido el foco previo.
            ════════════════════════════════════════════ */

            // Botón: Nueva Unidad
            $(document).on('click', '#btn-nueva-unidad', function () {
                cerrarSelect2Filtros();
                modalAgregar();
            });

            // Botón: Editar unidad (dentro de la tabla)
            $(document).on('click', '.btn-editar-unidad', function () {
                cerrarSelect2Filtros();
                var id = $(this).data('id');
                informacion(id);
            });

            // Botón: Gestionar Jefes (dentro de la tabla)
            $(document).on('click', '.btn-gestionar-jefes', function () {
                cerrarSelect2Filtros();
                var idUnidad     = $(this).data('id');
                var nombreUnidad = $(this).data('nombre');
                modalAsignarJefes(idUnidad, nombreUnidad);
            });

            // Botón: Guardar nueva unidad
            $(document).on('click', '#btn-guardar-nuevo', function () {
                nuevo();
            });

            // Botón: Actualizar unidad
            $(document).on('click', '#btn-guardar-editar', function () {
                editar();
            });

            // Botón: Agregar jefe al modal
            $(document).on('click', '#btn-agregar-jefe', function () {
                agregarJefeUnidad();
            });

            // Botón: Quitar jefe (generado dinámicamente dentro del modal)
            $(document).on('click', '.btn-quitar-jefe', function () {
                var pivotId = $(this).data('pivot-id');
                quitarJefeUnidad(pivotId);
            });

            // Botón: Limpiar filtros
            $(document).on('click', '#btn-limpiar-filtros', function () {
                limpiarFiltrosUnidad();
            });

            // Al abrir modal de Jefes: destruir y reinicializar Select2 interno
            // para evitar que herede estado previo del foco
            $('#modalJefes').on('shown.bs.modal', function () {
                if ($('#select-agregar-jefe').hasClass('select2-hidden-accessible')) {
                    $('#select-agregar-jefe').select2('destroy');
                }
                $('#select-agregar-jefe').select2({
                    theme: "bootstrap-5",
                    dropdownParent: $('#modalJefes'),
                    language: { noResults: function () { return "Búsqueda no encontrada"; } }
                });
            });

            // Al abrir modal de Agregar: reinicializar Select2 interno
            $('#modalAgregar').on('shown.bs.modal', function () {
                if ($('#select-distrito').hasClass('select2-hidden-accessible')) {
                    $('#select-distrito').select2('destroy');
                }
                $('#select-distrito').select2({
                    theme: "bootstrap-5",
                    dropdownParent: $('#modalAgregar'),
                    language: { noResults: function () { return "Búsqueda no encontrada"; } }
                });
            });

            // Al abrir modal de Editar: reinicializar Select2 interno
            $('#modalEditar').on('shown.bs.modal', function () {
                if ($('#select-distrito-editar').hasClass('select2-hidden-accessible')) {
                    $('#select-distrito-editar').select2('destroy');
                }
                $('#select-distrito-editar').select2({
                    theme: "bootstrap-5",
                    dropdownParent: $('#modalEditar'),
                    language: { noResults: function () { return "Búsqueda no encontrada"; } }
                });
            });
        });

        /* ════════════════════════════════════════════
   HELPERS
════════════════════════════════════════════ */

        // Cierra cualquier dropdown de Select2 abierto en los filtros.
        // Select2 deja un overlay invisible al cerrarse que intercepta
        // el primer clic sobre la tabla si no se limpia explícitamente.
        function cerrarSelect2Filtros() {
            $('#filtro-distrito, #filtro-jefe').each(function () {
                if ($(this).hasClass('select2-hidden-accessible')) {
                    $(this).select2('close');
                }
            });
            $('#filtro-buscar').blur();
        }

        /* ════════════════════════════════════════════
           CONTADORES
        ════════════════════════════════════════════ */
        function actualizarContadoresUnidad() {
            var dtU   = $('#tablaUnidad').DataTable();
            var total = dtU.rows({ filter: 'applied' }).count();
            var con   = 0;
            dtU.rows({ filter: 'applied' }).every(function () {
                if ($(this.node()).data('tienejefe') === 'con') con++;
            });
            $('#ucnt-total').text(total);
            $('#ucnt-con').text(con);
            $('#ucnt-sin').text(total - con);
        }

        /* ════════════════════════════════════════════
           FILTROS
        ════════════════════════════════════════════ */
        function limpiarFiltrosUnidad() {
            $('#filtro-buscar, #filtro-distrito, #filtro-jefe').val('');
            $('#tablaUnidad').DataTable().draw();
        }

        /* ════════════════════════════════════════════
           CRUD UNIDAD
        ════════════════════════════════════════════ */
        function modalAgregar() {
            document.getElementById("formulario-nuevo").reset();
            $('#select-distrito').val($('#select-distrito option:first').val()).trigger('change');
            $('#modalAgregar').modal('show');
        }

        function nuevo() {
            var nombre   = document.getElementById('unidad-nuevo').value;
            var distrito = document.getElementById('select-distrito').value;
            if (!nombre) { toastr.error('Nombre es requerido'); return; }
            openLoading();
            var fd = new FormData();
            fd.append('nombre', nombre);
            fd.append('unidad', distrito);
            axios.post(url + '/unidadempleado/nuevo', fd)
                .then(function (r) {
                    closeLoading();
                    if (r.data.success === 1) {
                        toastr.success('Registrado correctamente');
                        $('#modalAgregar').modal('hide');
                        location.reload();
                    } else { toastr.error('Error al registrar'); }
                })
                .catch(function () { toastr.error('Error al registrar'); closeLoading(); });
        }

        function informacion(id) {
            openLoading();
            document.getElementById("formulario-editar").reset();
            axios.post(url + '/unidadempleado/informacion', { 'id': id })
                .then(function (r) {
                    closeLoading();
                    if (r.data.success === 1) {
                        $('#modalEditar').modal('show');
                        $('#id-editar').val(id);
                        $('#unidad-editar').val(r.data.info.nombre);
                        document.getElementById("select-distrito-editar").options.length = 0;
                        $.each(r.data.arrayDistrito, function (k, v) {
                            var sel = r.data.info.id_distrito == v.id ? ' selected' : '';
                            $('#select-distrito-editar').append('<option value="' + v.id + '"' + sel + '>' + v.nombre + '</option>');
                        });
                    } else { toastr.error('Información no encontrada'); }
                })
                .catch(function () { closeLoading(); toastr.error('Información no encontrada'); });
        }

        function editar() {
            var id       = document.getElementById('id-editar').value;
            var nombre   = document.getElementById('unidad-editar').value;
            var distrito = document.getElementById('select-distrito-editar').value;
            if (!nombre) { toastr.error('Nombre es requerido'); return; }
            openLoading();
            var fd = new FormData();
            fd.append('id', id);
            fd.append('nombre', nombre);
            fd.append('distrito', distrito);
            axios.post(url + '/unidadempleado/editar', fd)
                .then(function (r) {
                    closeLoading();
                    if (r.data.success === 1) {
                        toastr.success('Actualizado correctamente');
                        $('#modalEditar').modal('hide');
                        location.reload();
                    } else { toastr.error('Error al actualizar'); }
                })
                .catch(function () { toastr.error('Error al actualizar'); closeLoading(); });
        }

        /* ════════════════════════════════════════════
           GESTIONAR JEFES (jefe_unidad pivote)
        ════════════════════════════════════════════ */
        function modalAsignarJefes(idUnidad, nombreUnidad) {
            $('#id-unidad-jefes').val(idUnidad);
            $('#titulo-unidad').text(nombreUnidad);
            $('#lista-jefes-asignados').html('<small class="text-muted">Cargando…</small>');
            document.getElementById("select-agregar-jefe").options.length = 0;
            $('#select-agregar-jefe').append('<option value="">Seleccionar empleado…</option>');
            $('#modalJefes').modal('show');

            openLoading();
            axios.post(url + '/unidadempleado/jefes/informacion', { 'id': idUnidad })
                .then(function (r) {
                    closeLoading();
                    if (r.data.success === 1) {
                        $.each(r.data.arrayJefes, function (k, v) {
                            $('#select-agregar-jefe').append('<option value="' + v.id + '">' + v.nombre_completo + '</option>');
                        });
                        $('#select-agregar-jefe').trigger('change');
                        renderJefesAsignados(r.data.asignados);
                    } else { toastr.error('Información no encontrada'); }
                })
                .catch(function () { closeLoading(); toastr.error('Error al cargar'); });
        }

        function renderJefesAsignados(asignados) {
            // 1. Actualizar el modal
            var html = '';
            if (asignados.length === 0) {
                html = '<p class="text-muted mb-0"><small>Ningún jefe asignado aún.</small></p>';
            } else {
                asignados.forEach(function (j) {
                    // Usamos data-pivot-id y clase .btn-quitar-jefe en lugar de onclick inline
                    html += '<div class="d-flex align-items-center justify-content-between mb-1 p-2" '
                        + 'style="background:#f8f9fa; border-radius:6px; border:1px solid #dee2e6">'
                        + '<span><i class="fas fa-user-tie mr-2 text-success"></i><strong>' + j.nombre + '</strong>'
                        + ' <small class="text-muted">(' + j.cargo + ')</small></span>'
                        + '<button class="btn btn-danger btn-xs btn-quitar-jefe" data-pivot-id="' + j.pivot_id + '">'
                        + '<i class="fas fa-times"></i> Quitar</button>'
                        + '</div>';
                });
            }
            $('#lista-jefes-asignados').html(html);

            // 2. Actualizar la fila en la tabla principal
            var idUnidad = $('#id-unidad-jefes').val();
            var dtU = $('#tablaUnidad').DataTable();

            dtU.rows().every(function () {
                var $tr  = $(this.node());
                var $btn = $tr.find('.btn-gestionar-jefes[data-id="' + idUnidad + '"]');
                if ($btn.length) {
                    // Actualizar data-tienejefe
                    $tr.attr('data-tienejefe', asignados.length > 0 ? 'con' : 'sin');

                    // Actualizar celda de jefes (columna índice 2)
                    var badgesHtml = '';
                    if (asignados.length === 0) {
                        badgesHtml = '<span class="badge-sin-jefe">Sin asignar</span>';
                    } else {
                        asignados.forEach(function (j) {
                            badgesHtml += '<span class="badge-jefe-asig">'
                                + '<i class="fas fa-user-tie" style="font-size:9px"></i> '
                                + j.nombre + '</span>';
                        });
                    }
                    $tr.find('td').eq(2).html(badgesHtml);
                }
            });

            // 3. Redibujar DataTable y actualizar contadores
            dtU.draw(false);
            actualizarContadoresUnidad();
        }

        function agregarJefeUnidad() {
            var idUnidad   = document.getElementById('id-unidad-jefes').value;
            var idEmpleado = document.getElementById('select-agregar-jefe').value;
            if (!idEmpleado) { toastr.error('Selecciona un empleado'); return; }
            openLoading();
            var fd = new FormData();
            fd.append('id_unidad',   idUnidad);
            fd.append('id_empleado', idEmpleado);
            axios.post(url + '/unidadempleado/jefes/agregar', fd)
                .then(function (r) {
                    closeLoading();
                    if (r.data.success === 1) {
                        toastr.success('Jefe asignado');
                        renderJefesAsignados(r.data.asignados);
                    } else if (r.data.success === 2) {
                        toastr.warning('Este jefe ya está asignado a esta unidad');
                    } else { toastr.error('Error al asignar'); }
                })
                .catch(function () { toastr.error('Error al asignar'); closeLoading(); });
        }

        function quitarJefeUnidad(pivotId) {
            openLoading();
            var fd = new FormData();
            fd.append('pivot_id', pivotId);
            axios.post(url + '/unidadempleado/jefes/quitar', fd)
                .then(function (r) {
                    closeLoading();
                    if (r.data.success === 1) {
                        toastr.success('Jefe removido');
                        renderJefesAsignados(r.data.asignados);
                    } else { toastr.error('Error al remover'); }
                })
                .catch(function () { toastr.error('Error al remover'); closeLoading(); });
        }

    </script>

@endsection
