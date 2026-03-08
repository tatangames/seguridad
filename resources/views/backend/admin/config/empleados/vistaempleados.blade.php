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
        box-shadow: 0 4px 18px rgba(33, 86, 175, 0.18);
    }
    .filtros-panel label {
        color: #c8d8f8;
        font-size: 11px;
        font-weight: 700;
        letter-spacing: .07em;
        text-transform: uppercase;
        margin-bottom: 4px;
        display: block;
    }
    .filtros-panel .form-control,
    .filtros-panel .custom-select {
        background: rgba(255,255,255,0.10);
        border: 1px solid rgba(255,255,255,0.22);
        color: #fff;
        border-radius: 6px;
        font-size: 13px;
        height: 34px;
        transition: background .2s, border .2s;
    }
    .filtros-panel .form-control::placeholder { color: rgba(255,255,255,.5); }
    .filtros-panel .form-control:focus,
    .filtros-panel .custom-select:focus {
        background: rgba(255,255,255,0.18);
        border-color: #82aaff;
        color: #fff;
        box-shadow: none;
        outline: none;
    }
    .filtros-panel .custom-select option { color: #222; background: #fff; }
    .filtros-panel .btn-limpiar {
        background: rgba(255,255,255,0.15);
        border: 1px solid rgba(255,255,255,0.3);
        color: #fff;
        border-radius: 6px;
        font-size: 12px;
        height: 34px;
        padding: 0 16px;
        cursor: pointer;
        transition: background .2s;
        white-space: nowrap;
        width: 100%;
    }
    .filtros-panel .btn-limpiar:hover { background: rgba(255,255,255,0.28); }

    .resumen-badges { margin-bottom: 14px; }
    .resumen-badges .badge-stat {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: #f0f4ff;
        border: 1px solid #d0dcf7;
        color: #2156af;
        border-radius: 20px;
        padding: 4px 13px;
        font-size: 12px;
        font-weight: 600;
        margin-right: 8px;
        margin-bottom: 6px;
    }
    .resumen-badges .badge-stat .dot { width: 8px; height: 8px; border-radius: 50%; flex-shrink: 0; }
    .resumen-badges .badge-stat .dot.jefe  { background: #28a745; }
    .resumen-badges .badge-stat .dot.emp   { background: #2156af; }
    .resumen-badges .badge-stat .dot.total { background: #6c757d; }

    #tabla thead tr th {
        background: #2156af;
        color: #fff;
        font-size: 12px;
        font-weight: 700;
        letter-spacing: .04em;
        text-transform: uppercase;
        border: none !important;
        white-space: nowrap;
        padding: 10px 12px;
    }
    #tabla tbody tr { transition: background .15s; }
    #tabla tbody tr:hover { background: #eef3ff !important; }
    #tabla tbody td { vertical-align: middle; font-size: 13px; padding: 8px 12px; }

    .badge-jefe {
        background: #d4edda; color: #155724;
        border: 1px solid #c3e6cb;
        border-radius: 12px; padding: 2px 10px;
        font-size: 11px; font-weight: 700;
    }
    .badge-empleado {
        background: #e8eeff; color: #2156af;
        border: 1px solid #c5d3f7;
        border-radius: 12px; padding: 2px 10px;
        font-size: 11px; font-weight: 700;
    }
    .nombre-emp { font-weight: 600; color: #1a2d55; }
    .dui-txt    { font-family: 'Courier New', monospace; color: #555; font-size: 12px; }
    .jefe-txt   { font-size: 12px; color: #555; }

    .dataTables_wrapper .dataTables_filter { display: none; }
    div.dataTables_wrapper div.dataTables_length select { min-width: 60px; }
    .dataTables_info { font-size: 12px; color: #888; }

    /* ── Select2 fix z-index ── */
    .select2-container--open { z-index: 99999 !important; }
    .select2-dropdown        { z-index: 99999 !important; }

    /* ── Jefe Directo: oculto por defecto, visible solo si es jefe ── */
    .grupo-jefe-directo { transition: opacity .2s; }
    .grupo-jefe-directo.oculto {
        opacity: 0;
        pointer-events: none;
        height: 0;
        overflow: hidden;
        margin: 0;
        padding: 0;
    }
</style>

<div id="divcontenedor" style="display: none">

    <section class="content-header">
        <div class="row mb-2">
            <div class="col-sm-6">
                {{-- Sin onclick inline: delegación en JS --}}
                <button type="button"
                        id="btn-nuevo-empleado"
                        style="font-weight:bold; background-color:#2156af; color:white !important;"
                        class="button button-3d button-rounded button-pill button-small">
                    <i class="fas fa-pencil-alt"></i> Nuevo Empleado
                </button>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item">Empleados</li>
                    <li class="breadcrumb-item active">Listado de Empleados</li>
                </ol>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">

            {{-- ══ Filtros ══ --}}
            <div class="filtros-panel">
                <div class="row align-items-end">
                    <div class="col-md-3 col-sm-6 mb-2">
                        <label><i class="fas fa-search mr-1"></i>Buscar</label>
                        <input type="text" id="filtro-buscar" class="form-control" placeholder="Nombre, DUI…">
                    </div>
                    <div class="col-md-3 col-sm-6 mb-2">
                        <label><i class="fas fa-map-marker-alt mr-1"></i>Distrito</label>
                        <select id="filtro-distrito" class="form-control custom-select">
                            <option value="">Todos los distritos</option>
                            @foreach($arrayDistrito as $dist)
                                <option value="{{ $dist->nombre }}">{{ $dist->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 col-sm-6 mb-2">
                        <label><i class="fas fa-building mr-1"></i>Unidad</label>
                        <select id="filtro-unidad" class="form-control custom-select">
                            <option value="">Todas las unidades</option>
                        </select>
                    </div>
                    <div class="col-md-2 col-sm-6 mb-2">
                        <label><i class="fas fa-user-tie mr-1"></i>Rol</label>
                        <select id="filtro-rol" class="form-control custom-select">
                            <option value="">Todos</option>
                            <option value="1">Solo Jefes</option>
                            <option value="0">Solo Empleados</option>
                        </select>
                    </div>
                    <div class="col-md-1 col-sm-6 mb-2 d-flex align-items-end">
                        <button class="btn-limpiar" id="btn-limpiar-filtros">
                            <i class="fas fa-times mr-1"></i> Limpiar
                        </button>
                    </div>
                </div>
            </div>

            {{-- ══ Badges ══ --}}
            <div class="resumen-badges">
                <span class="badge-stat"><span class="dot total"></span> Total: <strong id="cnt-total">0</strong></span>
                <span class="badge-stat"><span class="dot jefe"></span> Jefes: <strong id="cnt-jefes">0</strong></span>
                <span class="badge-stat"><span class="dot emp"></span> Empleados: <strong id="cnt-empleados">0</strong></span>
            </div>

            {{-- ══ Tabla ══ --}}
            <div class="row">
                <div class="col-12">
                    <div class="card" style="border-radius:10px; box-shadow:0 2px 16px rgba(0,0,0,.07); border:none;">
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table id="tabla" class="table table-bordered table-striped mb-0" style="table-layout:fixed; width:100%">
                                    <thead>
                                    <tr>
                                        <th style="width:20%">Nombre</th>
                                        <th style="width:13%">Distrito</th>
                                        <th style="width:17%">Unidad</th>
                                        <th style="width:13%">Cargo</th>
                                        <th style="width:11%">DUI</th>
                                        <th style="width:8%">Rol</th>
                                        <th style="width:11%">Jefe Directo</th>
                                        <th style="width:7%">Opciones</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($listado as $dato)
                                        <tr data-distrito="{{ $dato->distrito }}"
                                            data-unidad="{{ $dato->unidad }}"
                                            data-jefe="{{ $dato->jefe ? 1 : 0 }}">
                                            <td class="nombre-emp">{{ $dato->nombre }}</td>
                                            <td>{{ $dato->distrito }}</td>
                                            <td>{{ $dato->unidad }}</td>
                                            <td>{{ $dato->cargo }}</td>
                                            <td class="dui-txt">{{ $dato->dui ?? '—' }}</td>
                                            <td>
                                                @if($dato->jefe)
                                                    <span class="badge-jefe">
                                                        <i class="fas fa-star" style="font-size:9px"></i> Jefe
                                                    </span>
                                                @else
                                                    <span class="badge-empleado">Empleado</span>
                                                @endif
                                            </td>
                                            <td class="jefe-txt">{{ $dato->jefe_nombre ?? '—' }}</td>
                                            <td>
                                                {{-- Sin onclick inline: data-id + clase para delegación --}}
                                                <button type="button"
                                                        class="btn btn-success btn-xs btn-editar-empleado"
                                                        data-id="{{ $dato->id }}"
                                                        title="Editar">
                                                    <i class="fas fa-edit"></i>
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

    {{-- ══ MODAL AGREGAR ══ --}}
    <div class="modal fade" id="modalAgregar" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Nuevo Empleado</h4>
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
                                            <option value="0">Seleccionar opción</option>
                                            @foreach($arrayDistrito as $sel)
                                                <option value="{{ $sel->id }}">{{ $sel->nombre }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label>Unidad:</label>
                                        <select class="form-control" id="select-unidad"></select>
                                    </div>

                                    <div class="form-group">
                                        <label>Nombre</label>
                                        <input type="text" maxlength="100" class="form-control" id="nombre-nuevo" autocomplete="off">
                                    </div>

                                    <div class="form-group">
                                        <label>DUI</label>
                                        <input type="text" maxlength="50" class="form-control" id="dui-nuevo" autocomplete="off">
                                    </div>

                                    <div class="form-group">
                                        <label>Cargo:</label>
                                        <select class="form-control" id="select-cargo">
                                            @foreach($arrayCargo as $sel)
                                                <option value="{{ $sel->id }}">{{ $sel->nombre }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label>ES JEFE?</label><br>
                                        <label class="switch" style="margin-top:10px">
                                            <input type="checkbox" id="check-jefe">
                                            <div class="slider round">
                                                <span class="on">SI</span>
                                                <span class="off">NO</span>
                                            </div>
                                        </label>
                                    </div>

                                    {{-- Solo visible si ES JEFE = SI --}}
                                    <div class="form-group grupo-jefe-directo oculto" id="grupo-jefe-nuevo">
                                        <label>Jefe Directo: <small class="text-muted">(opcional)</small></label>
                                        <select class="form-control" id="select-jefe-nuevo">
                                            <option value="">Sin jefe directo</option>
                                            @foreach($arrayEmpleados as $emp)
                                                <option value="{{ $emp->id }}">{{ $emp->nombre }} ({{ $emp->cargo_nombre }})</option>
                                            @endforeach
                                        </select>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                    <button type="button"
                            id="btn-guardar-nuevo"
                            style="font-weight:bold; background-color:#2156af; color:white !important;"
                            class="button button-rounded button-pill button-small">Guardar</button>
                </div>
            </div>
        </div>
    </div>

    {{-- ══ MODAL EDITAR ══ --}}
    <div class="modal fade" id="modalEditar" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Editar Empleado</h4>
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
                                        <label>Unidad:</label>
                                        <select class="form-control" id="select-unidad-editar"></select>
                                    </div>

                                    <div class="form-group">
                                        <label>Nombre</label>
                                        <input type="text" maxlength="100" class="form-control" id="nombre-editar" autocomplete="off">
                                    </div>

                                    <div class="form-group">
                                        <label>DUI</label>
                                        <input type="text" maxlength="50" class="form-control" id="dui-editar" autocomplete="off">
                                    </div>

                                    <div class="form-group">
                                        <label>Cargo:</label>
                                        <select class="form-control" id="select-cargo-editar"></select>
                                    </div>

                                    <div class="form-group">
                                        <label>ES JEFE?</label><br>
                                        <label class="switch" style="margin-top:10px">
                                            <input type="checkbox" id="check-jefe-editar">
                                            <div class="slider round">
                                                <span class="on">SI</span>
                                                <span class="off">NO</span>
                                            </div>
                                        </label>
                                    </div>

                                    {{-- Solo visible si ES JEFE = SI --}}
                                    <div class="form-group grupo-jefe-directo oculto" id="grupo-jefe-editar">
                                        <label>Jefe Directo: <small class="text-muted">(opcional)</small></label>
                                        <select class="form-control" id="select-jefe-editar"></select>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                    <button type="button"
                            id="btn-guardar-editar"
                            style="font-weight:bold; background-color:#28a745; color:white !important;"
                            class="button button-rounded button-pill button-small">Actualizar</button>
                </div>
            </div>
        </div>
    </div>

</div>{{-- fin #divcontenedor --}}


@extends('backend.menus.footerjs')
@section('archivos-js')

    <script src="{{ asset('js/jquery.dataTables.js') }}"></script>
    <script src="{{ asset('js/dataTables.bootstrap4.js') }}"></script>
    <script src="{{ asset('js/toastr.min.js') }}"></script>
    <script src="{{ asset('js/axios.min.js') }}"></script>
    <script src="{{ asset('js/sweetalert2.all.min.js') }}"></script>
    <script src="{{ asset('js/alertaPersonalizada.js') }}"></script>
    <script src="{{ asset('js/select2.min.js') }}"></script>

    <script>

        /* ══════════════════════════════════════════════════════════════════
         *  HELPERS
         * ══════════════════════════════════════════════════════════════════ */
        function s2opts(parent) {
            return {
                theme: "bootstrap-5",
                dropdownParent: parent || $('body'),
                minimumResultsForSearch: 0,
                width: '100%',
                language: { noResults: function () { return "Búsqueda no encontrada"; } }
            };
        }

        // Muestra u oculta el grupo "Jefe Directo" según el estado del toggle
        function toggleGrupoJefe(checkboxId, grupoId) {
            var esJefe = document.getElementById(checkboxId).checked;
            var $grupo = $('#' + grupoId);
            if (esJefe) {
                $grupo.removeClass('oculto');
            } else {
                $grupo.addClass('oculto');
                // Limpiar selección cuando se oculta
                $grupo.find('select').val('').trigger('change.select2');
            }
        }

        /* ══════════════════════════════════════════════════════════════════
         *  FILTRO PERSONALIZADO DE DATATABLES
         * ══════════════════════════════════════════════════════════════════ */
        $.fn.dataTable.ext.search.push(function (settings, data, dataIndex) {
            if (settings.nTable.id !== 'tabla') return true;

            var buscar   = $('#filtro-buscar').val().toLowerCase().trim();
            var distrito = $('#filtro-distrito').val();
            var unidad   = $('#filtro-unidad').val();
            var rol      = $('#filtro-rol').val();

            var $row        = $('#tabla').DataTable().row(dataIndex).node();
            var rowDistrito = String($($row).data('distrito') ?? '');
            var rowUnidad   = String($($row).data('unidad')   ?? '');
            var rowJefe     = String($($row).data('jefe')     ?? '');

            // Buscar en TODAS las columnas de texto (nombre, distrito, unidad, cargo, dui)
            if (buscar) {
                var textoFila = [
                    data[0] || '', // Nombre
                    data[1] || '', // Distrito
                    data[2] || '', // Unidad
                    data[3] || '', // Cargo
                    data[4] || '', // DUI
                ].join(' ').toLowerCase();
                if (textoFila.indexOf(buscar) === -1) return false;
            }

            if (distrito !== '' && rowDistrito !== distrito) return false;
            if (unidad   !== '' && rowUnidad   !== unidad)   return false;
            if (rol      !== '' && rowJefe     !== rol)      return false;

            return true;
        });

        /* ══════════════════════════════════════════════════════════════════
         *  DOCUMENT READY
         * ══════════════════════════════════════════════════════════════════ */
        $(document).ready(function () {

            /* ── DataTable ── */
            var dt = $("#tabla").DataTable({
                paging:     true,
                searching:  true,
                ordering:   true,
                order:      [[0, 'asc']],
                info:       true,
                autoWidth:  false,
                responsive: true,
                pagingType: "full_numbers",
                lengthMenu: [[25, 50, 100, -1], [25, 50, 100, "Todo"]],
                pageLength: 25,
                language: {
                    sLengthMenu:   "Mostrar _MENU_ registros",
                    sZeroRecords:  "No se encontraron resultados",
                    sEmptyTable:   "Ningún dato disponible",
                    sInfo:         "Registros del _START_ al _END_ de _TOTAL_",
                    sInfoEmpty:    "Registros del 0 al 0 de 0",
                    sInfoFiltered: "(filtrado de _MAX_ registros)",
                    oPaginate: { sFirst: "«", sLast: "»", sNext: "›", sPrevious: "‹" }
                },
                drawCallback: actualizarContadores
            });

            /* ── Poblar #filtro-unidad usando API DataTables (lee TODAS las filas) ── */
            function poblarFiltroUnidad(distritoSel) {
                $('#filtro-unidad').empty().append('<option value="">Todas las unidades</option>');
                var vistas = {};
                dt.rows().every(function () {
                    var $tr     = $(this.node());
                    var rowDist = String($tr.data('distrito') || '');
                    var rowUnid = String($tr.data('unidad')   || '');
                    if (!rowUnid || rowUnid === '—') return;
                    if (distritoSel && rowDist !== distritoSel) return;
                    if (!vistas[rowUnid]) {
                        vistas[rowUnid] = true;
                        $('#filtro-unidad').append($('<option>', { value: rowUnid, text: rowUnid }));
                    }
                });
                // Ordenar alfabéticamente
                var opciones = $('#filtro-unidad option:not(:first)').detach().sort(function (a, b) {
                    return $(a).text().localeCompare($(b).text());
                });
                $('#filtro-unidad').append(opciones);
            }

            poblarFiltroUnidad(''); // carga inicial con todas las unidades

            /* ── Filtros ── */
            $('#filtro-buscar').on('keyup input', function () { dt.draw(); });

            $('#filtro-distrito').on('change', function () {
                poblarFiltroUnidad($(this).val());
                $('#filtro-unidad').val('');
                dt.draw();
            });

            $('#filtro-unidad').on('change', function () { dt.draw(); });
            $('#filtro-rol').on('change',    function () { dt.draw(); });

            /* ══ DELEGACIÓN DE EVENTOS ══ */

            // Botón Nuevo Empleado
            $(document).on('click', '#btn-nuevo-empleado', function () {
                abrirModalAgregar();
            });

            // Botón Editar (en la tabla)
            $(document).on('click', '.btn-editar-empleado', function () {
                var id = $(this).data('id');
                informacion(id);
            });

            // Guardar nuevo
            $(document).on('click', '#btn-guardar-nuevo', function () {
                nuevo();
            });

            // Actualizar editar
            $(document).on('click', '#btn-guardar-editar', function () {
                editar();
            });

            // Toggle ES JEFE (agregar)
            $(document).on('change', '#check-jefe', function () {
                toggleGrupoJefe('check-jefe', 'grupo-jefe-nuevo');
            });

            // Toggle ES JEFE (editar)
            $(document).on('change', '#check-jefe-editar', function () {
                toggleGrupoJefe('check-jefe-editar', 'grupo-jefe-editar');
            });

            // Cambio de distrito en modal agregar → recargar unidades
            $(document).on('change', '#select-distrito', function () {
                buscarUnidad();
            });

            // Cambio de distrito en modal editar → recargar unidades
            $(document).on('change', '#select-distrito-editar', function () {
                buscarUnidadEdicion();
            });

            // Botón limpiar filtros
            $(document).on('click', '#btn-limpiar-filtros', function () {
                limpiarFiltros();
            });

            actualizarContadores();
            document.getElementById("divcontenedor").style.display = "block";
        });

        /* ══════════════════════════════════════════════════════════════════
         *  CONTADORES
         * ══════════════════════════════════════════════════════════════════ */
        function actualizarContadores() {
            var dt    = $('#tabla').DataTable();
            var total = dt.rows({ filter: 'applied' }).count();
            var jefes = 0;
            dt.rows({ filter: 'applied' }).every(function () {
                if ($(this.node()).data('jefe') == 1) jefes++;
            });
            $('#cnt-total').text(total);
            $('#cnt-jefes').text(jefes);
            $('#cnt-empleados').text(total - jefes);
        }

        /* ══════════════════════════════════════════════════════════════════
         *  LIMPIAR FILTROS
         * ══════════════════════════════════════════════════════════════════ */
        function limpiarFiltros() {
            $('#filtro-buscar').val('');
            $('#filtro-rol').val('');
            $('#filtro-distrito').val('').trigger('change'); // recarga unidades y redibuja
        }

        /* ══════════════════════════════════════════════════════════════════
         *  MODAL AGREGAR
         * ══════════════════════════════════════════════════════════════════ */
        function abrirModalAgregar() {
            document.getElementById("formulario-nuevo").reset();
            // Asegurar que "Jefe Directo" empiece oculto
            $('#grupo-jefe-nuevo').addClass('oculto');
            $('#modalAgregar').modal('show');
        }

        $('#modalAgregar').on('shown.bs.modal', function () {
            $('#select-distrito').select2(s2opts($('#modalAgregar')));
            $('#select-unidad').select2(s2opts($('#modalAgregar')));
            $('#select-cargo').select2(s2opts($('#modalAgregar')));
            $('#select-jefe-nuevo').select2(s2opts($('#modalAgregar')));
        });

        $('#modalAgregar').on('hidden.bs.modal', function () {
            $('#select-distrito, #select-unidad, #select-cargo, #select-jefe-nuevo')
                .each(function () {
                    if ($(this).hasClass('select2-hidden-accessible')) $(this).select2('destroy');
                });
        });

        function buscarUnidad() {
            var id = document.getElementById('select-distrito').value;
            if (id == '0') {
                $('#select-unidad').empty().select2(s2opts($('#modalAgregar')));
                return;
            }
            openLoading();
            axios.post(url + '/empleados/buscarunidad', { 'id': id })
                .then(function (r) {
                    closeLoading();
                    if (r.data.success === 1) {
                        $('#select-unidad').empty();
                        $.each(r.data.arrayUnidad, function (k, v) {
                            $('#select-unidad').append('<option value="' + v.id + '">' + v.nombre + '</option>');
                        });
                        $('#select-unidad').select2(s2opts($('#modalAgregar')));
                    } else {
                        toastr.error('Información no encontrada');
                    }
                })
                .catch(function () { closeLoading(); toastr.error('Información no encontrada'); });
        }

        function nuevo() {
            var unidad = document.getElementById('select-unidad').value;
            var cargo  = document.getElementById('select-cargo').value;
            var nombre = document.getElementById('nombre-nuevo').value;
            var dui    = document.getElementById('dui-nuevo').value;
            var jefe   = document.getElementById('check-jefe').checked ? 1 : 0;
            // Si no es jefe, id_jefe siempre vacío
            var idJefe = jefe === 1 ? document.getElementById('select-jefe-nuevo').value : '';

            if (!unidad) { toastr.error('Unidad es requerida'); return; }
            if (!cargo)  { toastr.error('Cargo es requerido');  return; }
            if (!nombre) { toastr.error('Nombre es requerido'); return; }

            openLoading();
            var fd = new FormData();
            fd.append('nombre',  nombre);
            fd.append('unidad',  unidad);
            fd.append('cargo',   cargo);
            fd.append('dui',     dui);
            fd.append('jefe',    jefe);
            fd.append('id_jefe', idJefe);

            axios.post(url + '/empleados/nuevo', fd)
                .then(function (r) {
                    closeLoading();
                    if (r.data.success === 1) {
                        toastr.success('Registrado correctamente');
                        $('#modalAgregar').modal('hide');
                        location.reload();
                    } else {
                        toastr.error('Error al registrar');
                    }
                })
                .catch(function () { closeLoading(); toastr.error('Error al registrar'); });
        }

        /* ══════════════════════════════════════════════════════════════════
         *  MODAL EDITAR
         * ══════════════════════════════════════════════════════════════════ */
        function buscarUnidadEdicion() {
            var id = document.getElementById('select-distrito-editar').value;
            openLoading();
            axios.post(url + '/empleados/buscarunidad', { 'id': id })
                .then(function (r) {
                    closeLoading();
                    if (r.data.success === 1) {
                        // Destruir Select2 antes de vaciar, para evitar estado colgado
                        if ($('#select-unidad-editar').hasClass('select2-hidden-accessible')) {
                            $('#select-unidad-editar').select2('destroy');
                        }
                        $('#select-unidad-editar').empty();
                        $.each(r.data.arrayUnidad, function (k, v) {
                            $('#select-unidad-editar').append('<option value="' + v.id + '">' + v.nombre + '</option>');
                        });
                        $('#select-unidad-editar').select2(s2opts($('#modalEditar')));
                    } else {
                        toastr.error('Información no encontrada');
                    }
                })
                .catch(function () { closeLoading(); toastr.error('Información no encontrada'); });
        }

        function informacion(id) {
            openLoading();
            document.getElementById("formulario-editar").reset();

            axios.post(url + '/empleados/informacion', { 'id': id })
                .then(function (r) {
                    closeLoading();
                    if (r.data.success === 1) {

                        $('#id-editar').val(id);

                        // Poblar selects
                        $('#select-distrito-editar, #select-unidad-editar, #select-cargo-editar, #select-jefe-editar').empty();

                        $.each(r.data.arrayDistrito, function (k, v) {
                            var sel = r.data.infoUniEmpleado.id_distrito == v.id ? 'selected' : '';
                            $('#select-distrito-editar').append('<option value="' + v.id + '" ' + sel + '>' + v.nombre + '</option>');
                        });

                        $.each(r.data.arrayUnidad, function (k, v) {
                            var sel = r.data.info.id_unidad_empleado == v.id ? 'selected' : '';
                            $('#select-unidad-editar').append('<option value="' + v.id + '" ' + sel + '>' + v.nombre + '</option>');
                        });

                        $.each(r.data.arrayCargo, function (k, v) {
                            var sel = r.data.info.id_cargo == v.id ? 'selected' : '';
                            $('#select-cargo-editar').append('<option value="' + v.id + '" ' + sel + '>' + v.nombre + '</option>');
                        });

                        $('#select-jefe-editar').append('<option value="">Sin jefe directo</option>');
                        $.each(r.data.arrayEmpleados, function (k, v) {
                            var sel = r.data.info.id_jefe == v.id ? 'selected' : '';
                            $('#select-jefe-editar').append('<option value="' + v.id + '" ' + sel + '>' + v.nombre_completo + '</option>');
                        });

                        $('#nombre-editar').val(r.data.info.nombre);
                        $('#dui-editar').val(r.data.info.dui);

                        var esJefe = r.data.info.jefe == 1;
                        $('#check-jefe-editar').prop('checked', esJefe);

                        // Mostrar/ocultar Jefe Directo según el valor cargado
                        if (esJefe) {
                            $('#grupo-jefe-editar').removeClass('oculto');
                        } else {
                            $('#grupo-jefe-editar').addClass('oculto');
                        }

                        $('#modalEditar').modal('show');

                    } else {
                        toastr.error('Información no encontrada');
                    }
                })
                .catch(function () { closeLoading(); toastr.error('Información no encontrada'); });
        }

        $('#modalEditar').on('shown.bs.modal', function () {
            $('#select-distrito-editar').select2(s2opts($('#modalEditar')));
            $('#select-unidad-editar').select2(s2opts($('#modalEditar')));
            $('#select-cargo-editar').select2(s2opts($('#modalEditar')));
            $('#select-jefe-editar').select2(s2opts($('#modalEditar')));
        });

        $('#modalEditar').on('hidden.bs.modal', function () {
            $('#select-distrito-editar, #select-unidad-editar, #select-cargo-editar, #select-jefe-editar')
                .each(function () {
                    if ($(this).hasClass('select2-hidden-accessible')) $(this).select2('destroy');
                });
        });

        function editar() {
            var id     = document.getElementById('id-editar').value;
            var nombre = document.getElementById('nombre-editar').value;
            var unidad = document.getElementById('select-unidad-editar').value;
            var cargo  = document.getElementById('select-cargo-editar').value;
            var dui    = document.getElementById('dui-editar').value;
            var jefe   = document.getElementById('check-jefe-editar').checked ? 1 : 0;
            // Si no es jefe, id_jefe siempre vacío
            var idJefe = jefe === 1 ? document.getElementById('select-jefe-editar').value : '';

            if (!nombre) { toastr.error('Nombre es requerido'); return; }
            if (!unidad) { toastr.error('Unidad es requerida'); return; }

            openLoading();
            var fd = new FormData();
            fd.append('id',      id);
            fd.append('nombre',  nombre);
            fd.append('unidad',  unidad);
            fd.append('cargo',   cargo);
            fd.append('dui',     dui);
            fd.append('jefe',    jefe);
            fd.append('id_jefe', idJefe);

            axios.post(url + '/empleados/editar', fd)
                .then(function (r) {
                    closeLoading();
                    if (r.data.success === 1) {
                        toastr.success('Actualizado correctamente');
                        $('#modalEditar').modal('hide');
                        location.reload();
                    } else {
                        toastr.error('Error al actualizar');
                    }
                })
                .catch(function () { closeLoading(); toastr.error('Error al actualizar'); });
        }

    </script>
@endsection
