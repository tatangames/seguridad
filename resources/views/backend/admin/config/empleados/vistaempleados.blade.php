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
</style>

<div id="divcontenedor" style="display: none">

    <section class="content-header">
        <div class="row mb-2">
            <div class="col-sm-6">
                {{-- Enlace directo a la vista de crear --}}
                <a href="{{ route('admin.empleados.crear') }}"
                   style="font-weight:bold; background-color:#2156af; color:white !important;"
                   class="button button-3d button-rounded button-pill button-small">
                    <i class="fas fa-pencil-alt"></i> Nuevo Empleado
                </a>
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
                        <input type="text" id="filtro-buscar" autocomplete="off" class="form-control" placeholder="Nombre, DUI…">
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
                                <table id="tabla" class="table table-bordered table-striped mb-0"
                                       style="table-layout:fixed; width:100%">
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
                                                {{-- Enlace directo a la vista de editar --}}
                                                <a href="{{ route('admin.empleados.editar', $dato->id) }}"
                                                   class="btn btn-success btn-xs"
                                                   title="Editar">
                                                    <i class="fas fa-edit"></i>
                                                </a>
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

</div>{{-- fin #divcontenedor --}}


@extends('backend.menus.footerjs')
@section('archivos-js')

    <script src="{{ asset('js/jquery.dataTables.js') }}"></script>
    <script src="{{ asset('js/dataTables.bootstrap4.js') }}"></script>
    <script src="{{ asset('js/toastr.min.js') }}"></script>
    <script src="{{ asset('js/select2.min.js') }}"></script>

    <script>

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

            if (buscar) {
                var textoFila = [
                    data[0] || '',
                    data[1] || '',
                    data[2] || '',
                    data[3] || '',
                    data[4] || '',
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

            /* ── Poblar #filtro-unidad ── */
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
                var opciones = $('#filtro-unidad option:not(:first)').detach().sort(function (a, b) {
                    return $(a).text().localeCompare($(b).text());
                });
                $('#filtro-unidad').append(opciones);
            }

            poblarFiltroUnidad('');

            /* ── Eventos de filtros ── */
            $('#filtro-buscar').on('keyup input', function () { dt.draw(); });

            $('#filtro-distrito').on('change', function () {
                poblarFiltroUnidad($(this).val());
                $('#filtro-unidad').val('');
                dt.draw();
            });

            $('#filtro-unidad').on('change', function () { dt.draw(); });
            $('#filtro-rol').on('change',    function () { dt.draw(); });

            $('#btn-limpiar-filtros').on('click', function () {
                $('#filtro-buscar').val('');
                $('#filtro-rol').val('');
                $('#filtro-distrito').val('').trigger('change');
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

    </script>
@endsection
