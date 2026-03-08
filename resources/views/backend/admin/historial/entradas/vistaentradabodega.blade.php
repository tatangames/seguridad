@extends('backend.menus.superior')

@section('content-admin-css')
    <link href="{{ asset('css/adminlte.min.css') }}" type="text/css" rel="stylesheet" />
    <link href="{{ asset('css/dataTables.bootstrap4.css') }}" type="text/css" rel="stylesheet" />
    <link href="{{ asset('css/toastr.min.css') }}" type="text/css" rel="stylesheet" />
    <link href="{{ asset('css/buttons_estilo.css') }}" rel="stylesheet">
    <link href="{{ asset('css/select2.min.css') }}" type="text/css" rel="stylesheet">
    <link href="{{ asset('css/select2-bootstrap-5-theme.min.css') }}" type="text/css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Mono:wght@400;500;600&family=IBM+Plex+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
@stop

<style>
    :root {
        --bg-base:       #f4f6f9;
        --bg-card:       #ffffff;
        --bg-card-alt:   #f8fafc;
        --border:        #e2e8f0;
        --border-light:  #cbd5e1;
        --accent:        #0ea472;
        --accent-dim:    rgba(14, 164, 114, 0.18);
        --accent-glow:   rgba(14, 164, 114, 0.25);
        --danger:        #ef4444;
        --danger-dim:    rgba(239, 68, 68, 0.14);
        --warning:       #f59e0b;
        --warning-dim:   rgba(245, 158, 11, 0.14);
        --info:          #0ea5e9;
        --info-dim:      rgba(14, 165, 233, 0.14);
        --text-primary:  #1e293b;
        --text-secondary:#475569;
        --text-muted:    #94a3b8;
        --mono:          'IBM Plex Mono', monospace;
        --sans:          'IBM Plex Sans', sans-serif;
    }

    * { box-sizing: border-box; }

    body {
        background: var(--bg-base) !important;
        font-family: var(--sans) !important;
        color: var(--text-primary) !important;
    }

    /* ── PAGE HEADER ── */
    .page-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 28px 32px 20px;
        border-bottom: 1px solid var(--border);
        margin-bottom: 0;
    }

    .page-header-left {
        display: flex;
        align-items: center;
        gap: 16px;
    }

    .page-icon {
        width: 44px; height: 44px;
        background: var(--accent-dim);
        border: 1px solid var(--accent-glow);
        border-radius: 10px;
        display: flex; align-items: center; justify-content: center;
        color: var(--accent);
        font-size: 18px;
        flex-shrink: 0;
    }

    .page-title {
        font-family: var(--mono);
        font-size: 18px;
        font-weight: 600;
        color: var(--text-primary);
        letter-spacing: -0.3px;
        margin: 0;
    }

    .page-subtitle {
        font-size: 12px;
        color: var(--text-muted);
        font-family: var(--mono);
        letter-spacing: 0.5px;
        margin: 2px 0 0;
    }

    /* ── STATS BAR ── */
    .stats-bar {
        display: flex;
        gap: 1px;
        background: var(--border);
        border-bottom: 1px solid var(--border);
    }

    .stat-item {
        flex: 1;
        background: var(--bg-card);
        padding: 16px 24px;
        display: flex;
        align-items: center;
        gap: 12px;
        transition: background 0.2s;
    }

    .stat-item:hover { background: var(--bg-card-alt); }

    .stat-dot {
        width: 8px; height: 8px;
        border-radius: 50%;
        flex-shrink: 0;
    }

    .stat-dot.green  { background: var(--accent); box-shadow: 0 0 8px var(--accent); }
    .stat-dot.blue   { background: var(--info);   box-shadow: 0 0 8px var(--info); }
    .stat-dot.yellow { background: var(--warning); box-shadow: 0 0 8px var(--warning); }

    .stat-label {
        font-size: 11px;
        color: var(--text-muted);
        font-family: var(--mono);
        letter-spacing: 0.5px;
        text-transform: uppercase;
        display: block;
    }

    .stat-value {
        font-size: 20px;
        font-weight: 600;
        font-family: var(--mono);
        color: var(--text-primary);
        line-height: 1;
    }

    /* ── MAIN CARD ── */
    .main-card {
        margin: 24px 32px;
        background: var(--bg-card);
        border: 1px solid var(--border);
        border-radius: 12px;
        overflow: hidden;
    }

    .main-card-header {
        padding: 16px 24px;
        border-bottom: 1px solid var(--border);
        display: flex;
        align-items: center;
        justify-content: space-between;
        background: var(--bg-card-alt);
    }

    .main-card-title {
        font-family: var(--mono);
        font-size: 12px;
        font-weight: 500;
        color: var(--text-secondary);
        letter-spacing: 1px;
        text-transform: uppercase;
        margin: 0;
    }

    @keyframes pulse {
        0%, 100% { opacity: 1; transform: scale(1); }
        50%       { opacity: 0.5; transform: scale(0.8); }
    }

    /* ── TABLE OVERRIDES ── */
    #tabla_wrapper {
        padding: 20px;
    }

    #tabla {
        background: transparent !important;
        color: var(--text-primary) !important;
        border-collapse: separate !important;
        border-spacing: 0 4px !important;
        font-size: 13px;
        table-layout: fixed;
    }

    #tabla thead th {
        background: transparent !important;
        color: var(--text-muted) !important;
        border-bottom: 2px solid var(--border) !important;
        border-top: none !important;
        font-family: var(--mono);
        font-size: 10px;
        font-weight: 500;
        letter-spacing: 1px;
        text-transform: uppercase;
        padding: 10px 14px !important;
        white-space: nowrap;
    }

    #tabla thead th::after,
    #tabla thead th::before {
        color: var(--text-muted) !important;
    }

    #tabla tbody tr {
        background: transparent !important;
    }

    #tabla tbody td {
        background: #ffffff !important;
        border: none !important;
        border-top: 1px solid var(--border) !important;
        border-bottom: 1px solid var(--border) !important;
        padding: 12px 14px !important;
        color: var(--text-primary) !important;
        vertical-align: middle !important;
        transition: all 0.15s ease;
    }

    #tabla tbody td:first-child {
        border-left: 1px solid var(--border) !important;
        border-radius: 8px 0 0 8px !important;
    }

    #tabla tbody td:last-child {
        border-right: 1px solid var(--border) !important;
        border-radius: 0 8px 8px 0 !important;
    }

    #tabla tbody tr:hover td {
        background: #f1f5f9 !important;
        border-top-color: #cbd5e1 !important;
        border-bottom-color: #cbd5e1 !important;
    }

    #tabla tbody tr:hover td:first-child {
        border-left-color: var(--border-light) !important;
    }

    /* ── DATATABLE CONTROLS ── */
    .dataTables_wrapper .dataTables_filter input {
        background: #ffffff !important;
        border: 1px solid var(--border) !important;
        color: var(--text-primary) !important;
        border-radius: 6px !important;
        padding: 6px 12px !important;
        font-family: var(--mono) !important;
        font-size: 12px !important;
        outline: none !important;
        transition: border-color 0.2s;
    }
    .dataTables_wrapper .dataTables_filter input:focus {
        border-color: var(--accent) !important;
        box-shadow: 0 0 0 2px var(--accent-glow) !important;
    }

    .dataTables_wrapper .dataTables_length select {
        background: #ffffff !important;
        border: 1px solid var(--border) !important;
        color: var(--text-primary) !important;
        border-radius: 6px !important;
        padding: 4px 8px !important;
        font-family: var(--mono) !important;
        font-size: 12px !important;
    }

    .dataTables_wrapper .dataTables_filter label,
    .dataTables_wrapper .dataTables_length label,
    .dataTables_wrapper .dataTables_info {
        color: var(--text-muted) !important;
        font-family: var(--mono) !important;
        font-size: 11px !important;
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button {
        background: #ffffff !important;
        border: 1px solid var(--border) !important;
        color: var(--text-secondary) !important;
        border-radius: 6px !important;
        font-family: var(--mono) !important;
        font-size: 11px !important;
        padding: 4px 10px !important;
        margin: 0 2px !important;
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
        background: var(--bg-card-alt) !important;
        border-color: var(--border-light) !important;
        color: var(--text-primary) !important;
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button.current,
    .dataTables_wrapper .dataTables_paginate .paginate_button.current:hover {
        background: var(--accent-dim) !important;
        border-color: var(--accent) !important;
        color: var(--accent) !important;
    }

    /* ── STRIPES ── */
    table.dataTable.table-striped tbody tr:nth-of-type(odd) td {
        background: #f8f9fa !important;
    }
    table.dataTable.table-striped tbody tr:nth-of-type(even) td {
        background: #ffffff !important;
    }

    /* ── CHIPS & TEXT ── */
    .date-chip {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-family: var(--mono);
        font-size: 12px;
        color: var(--info);
        background: var(--info-dim);
        border: 1px solid rgba(56,189,248,0.2);
        padding: 3px 9px;
        border-radius: 20px;
    }

    .factura-chip {
        font-family: var(--mono);
        font-size: 12px;
        color: #2563eb;
        background: #eff6ff;
        border: 1px solid #bfdbfe;
        padding: 3px 9px;
        border-radius: 4px;
        letter-spacing: 0.5px;
    }

    .proveedor-text {
        font-size: 12px;
        font-weight: 500;
        color: var(--text-secondary);
    }

    .obs-text {
        font-size: 12px;
        color: var(--text-secondary);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 280px;
    }

    /* ── ACTION BUTTONS ── */
    .actions-group {
        display: flex;
        flex-wrap: wrap;
        gap: 4px;
        align-items: center;
    }

    .btn-action {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 5px 10px;
        border: 1px solid transparent;
        border-radius: 6px;
        font-size: 11px;
        font-weight: 500;
        font-family: var(--mono);
        cursor: pointer;
        transition: all 0.15s ease;
        white-space: nowrap;
        background: transparent;
        letter-spacing: 0.3px;
    }

    .btn-action.edit {
        color: #059c6a;
        border-color: #059c6a;
        background: transparent;
    }
    .btn-action.edit:hover {
        background: #059c6a;
        color: #ffffff;
        box-shadow: none;
    }

    .btn-action.detail {
        color: #0284c7;
        border-color: #0284c7;
        background: transparent;
    }
    .btn-action.detail:hover {
        background: #0284c7;
        color: #ffffff;
    }

    .btn-action.add {
        color: #d97706;
        border-color: #d97706;
        background: transparent;
    }
    .btn-action.add:hover {
        background: #d97706;
        color: #ffffff;
    }

    .btn-action.delete {
        color: #dc2626;
        border-color: #dc2626;
        background: transparent;
    }
    .btn-action.delete:hover {
        background: #dc2626;
        color: #ffffff;
    }

    /* ── MODAL ── */
    .modal-content {
        background: #ffffff !important;
        border: 1px solid var(--border) !important;
        border-radius: 14px !important;
        overflow: hidden;
        box-shadow: 0 20px 60px rgba(0,0,0,0.12) !important;
    }

    .modal-header {
        background: var(--bg-card-alt) !important;
        border-bottom: 1px solid var(--border) !important;
        padding: 18px 24px !important;
    }

    .modal-title {
        font-family: var(--mono) !important;
        font-size: 14px !important;
        font-weight: 600 !important;
        color: var(--text-primary) !important;
        letter-spacing: -0.2px;
    }

    .modal-header .close {
        color: var(--text-muted) !important;
        opacity: 1 !important;
        text-shadow: none !important;
    }
    .modal-header .close:hover { color: var(--text-primary) !important; }

    .modal-body {
        background: #ffffff !important;
        padding: 24px !important;
    }

    .modal-footer {
        background: var(--bg-card-alt) !important;
        border-top: 1px solid var(--border) !important;
        padding: 14px 24px !important;
    }

    .form-label-modal {
        display: block;
        font-family: var(--mono);
        font-size: 10px;
        font-weight: 500;
        color: var(--text-muted);
        letter-spacing: 1px;
        text-transform: uppercase;
        margin-bottom: 6px;
    }

    .form-control-modal {
        width: 100%;
        background: #ffffff !important;
        border: 1px solid var(--border) !important;
        color: var(--text-primary) !important;
        border-radius: 8px !important;
        padding: 10px 14px !important;
        font-family: var(--sans) !important;
        font-size: 13px !important;
        transition: border-color 0.2s, box-shadow 0.2s;
        outline: none;
    }

    .form-control-modal:focus {
        border-color: var(--accent) !important;
        box-shadow: 0 0 0 3px var(--accent-glow) !important;
    }

    .form-group-modal { margin-bottom: 18px; }

    .btn-modal-cancel {
        background: transparent;
        border: 1px solid var(--border);
        color: var(--text-secondary);
        border-radius: 8px;
        padding: 9px 18px;
        font-family: var(--mono);
        font-size: 12px;
        cursor: pointer;
        transition: all 0.15s;
    }
    .btn-modal-cancel:hover {
        border-color: var(--border-light);
        color: var(--text-primary);
        background: var(--bg-card-alt);
    }

    .btn-modal-save {
        background: var(--accent);
        border: none;
        color: #ffffff;
        border-radius: 8px;
        padding: 9px 22px;
        font-family: var(--mono);
        font-size: 12px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.15s;
        letter-spacing: 0.3px;
    }
    .btn-modal-save:hover {
        background: #0bbf85;
        box-shadow: 0 4px 16px var(--accent-glow);
    }

    /* ── SELECT2 ── */
    .select2-container--bootstrap-5 .select2-selection {
        background: #ffffff !important;
        border: 1px solid var(--border) !important;
        color: var(--text-primary) !important;
        border-radius: 8px !important;
        min-height: 40px !important;
    }
    .select2-dropdown {
        background: #ffffff !important;
        border: 1px solid var(--border-light) !important;
        border-radius: 8px !important;
        box-shadow: 0 8px 24px rgba(0,0,0,0.10) !important;
    }
    .select2-results__option {
        color: var(--text-secondary) !important;
        font-size: 13px !important;
    }
    .select2-results__option--highlighted {
        background: var(--accent-dim) !important;
        color: var(--accent) !important;
    }
    .select2-search__field {
        background: #ffffff !important;
        border: 1px solid var(--border) !important;
        color: var(--text-primary) !important;
        border-radius: 6px !important;
    }

    /* ── ROW ANIMATION ── */
    #tabla tbody tr {
        animation: rowIn 0.25s ease both;
    }
    @keyframes rowIn {
        from { opacity: 0; transform: translateY(4px); }
        to   { opacity: 1; transform: translateY(0); }
    }

    /* ── SCROLLBAR ── */
    ::-webkit-scrollbar { width: 6px; height: 6px; }
    ::-webkit-scrollbar-track { background: var(--bg-base); }
    ::-webkit-scrollbar-thumb { background: var(--border-light); border-radius: 3px; }
    ::-webkit-scrollbar-thumb:hover { background: var(--text-muted); }
</style>


<div id="divcontenedor" style="display:none">

    <!-- PAGE HEADER -->
    <div class="page-header">
        <div class="page-header-left">
            <div class="page-icon">
                <i class="fas fa-boxes"></i>
            </div>
            <div>
                <h1 class="page-title">Historial de Entradas</h1>
                <p class="page-subtitle">BODEGA / INGRESOS</p>
            </div>
        </div>
    </div>

    <!-- STATS BAR -->
    <div class="stats-bar" id="statsBar">
        <div class="stat-item">
            <div class="stat-dot green"></div>
            <div>
                <span class="stat-label">Total Ingresos</span>
                <div class="stat-value" id="statTotal">—</div>
            </div>
        </div>
        <div class="stat-item">
            <div class="stat-dot yellow"></div>
            <div>
                <span class="stat-label">Último Ingreso</span>
                <div class="stat-value" id="statUltimo" style="font-size:13px">—</div>
            </div>
        </div>
    </div>

    <!-- MAIN CARD -->
    <div class="main-card">
        <div class="main-card-header">
            <span class="main-card-title">Registros</span>
        </div>
        <div id="tablaDatatable"></div>
    </div>


    <!-- MODAL EDITAR -->
    <div class="modal fade" id="modalEditar">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">
                        <i class="fas fa-edit" style="color:var(--accent); margin-right:8px; font-size:13px"></i>
                        Editar Registro
                    </h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="formulario-editar">
                        <input type="hidden" id="id-editar">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group-modal">
                                    <label class="form-label-modal">Fecha</label>
                                    <input type="date" class="form-control-modal" id="fecha-editar" autocomplete="off">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group-modal">
                                    <label class="form-label-modal">Factura / Lote</label>
                                    <input type="text" maxlength="100" class="form-control-modal" id="lote-editar" autocomplete="off" placeholder="Ej: FAC-0001">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group-modal">
                                    <label class="form-label-modal">Descripción / Observación</label>
                                    <input type="text" maxlength="800" class="form-control-modal" id="descripcion-editar" autocomplete="off" placeholder="Ingrese una descripción...">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group-modal">
                                    <label class="form-label-modal">Proveedor</label>
                                    <select width="100%" class="form-control" id="select-proveedor-editar"></select>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-modal-cancel" data-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn-modal-save" onclick="guardarEdicion()">
                        <i class="fas fa-save" style="margin-right:6px"></i>Actualizar
                    </button>
                </div>
            </div>
        </div>
    </div>

</div>


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
        $(document).ready(function () {
            openLoading();
            var ruta = "{{ URL::to('/admin/historial/entrada/tabla') }}";
            $('#tablaDatatable').load(ruta, function () {
                actualizarStats();
            });

            $('#select-proveedor-editar').select2({
                theme: "bootstrap-5",
                dropdownParent: $('#modalEditar'),
                language: {
                    noResults: function () { return "Búsqueda no encontrada"; }
                },
            });

            document.getElementById("divcontenedor").style.display = "block";
        });

        function actualizarStats() {
            setTimeout(function () {
                var table = $('#tabla').DataTable();
                var total = table.rows().count();
                $('#statTotal').text(total);

                // Última fecha (primera fila ordenada desc)
                var primeraFila = table.row(':first').node();
                if (primeraFila) {
                    var fecha = $(primeraFila).find('td').eq(0).text().trim();
                    $('#statUltimo').text(fecha);
                }
            }, 800);
        }
    </script>

    <script>
        function recargar() {
            var ruta = "{{ URL::to('/admin/historial/entrada/tabla') }}";
            $('#tablaDatatable').load(ruta, function () {
                actualizarStats();
            });
        }

        function vistaDetalle(idsolicitud) {
            window.location.href = "{{ url('/admin/historial/entradadetalle/index') }}/" + idsolicitud;
        }

        function infoBorrar(id) {
            Swal.fire({
                title: 'Confirmar eliminación',
                html: '<p style="color:#475569; font-size:13px;">Esto eliminará todo el ingreso de productos,<br>sus salidas y movimientos asociados.</p>',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#94a3b8',
                confirmButtonText: '<i class="fas fa-trash"></i> Eliminar',
                cancelButtonText: 'Cancelar',
            }).then((result) => {
                if (result.isConfirmed) borrarRegistro(id);
            });
        }

        function borrarRegistro(id) {
            openLoading();
            var formData = new FormData();
            formData.append('id', id);
            axios.post(url + '/historial/entrada/borrarlote', formData)
                .then((response) => {
                    closeLoading();
                    if (response.data.success === 1) {
                        toastr.success('Registro eliminado correctamente');
                        recargar();
                    } else {
                        toastr.error('Error al eliminar');
                    }
                })
                .catch(() => { toastr.error('Error al eliminar'); closeLoading(); });
        }

        function infoNuevoIngreso(id) {
            window.location.href = "{{ url('/admin/historial/nuevoingresoentradadetalle/index') }}/" + id;
        }

        function modalEditar(id) {
            openLoading();
            document.getElementById("formulario-editar").reset();

            axios.post(url + '/historial/entrada/informacion', { 'id': id })
                .then((response) => {
                    closeLoading();
                    if (response.data.success === 1) {
                        $('#modalEditar').modal('show');
                        $('#id-editar').val(id);
                        $('#fecha-editar').val(response.data.info.fecha);
                        $('#descripcion-editar').val(response.data.info.descripcion);
                        $('#lote-editar').val(response.data.info.lote);

                        document.getElementById("select-proveedor-editar").options.length = 0;
                        $.each(response.data.arrayProveedor, function (key, val) {
                            var selected = (response.data.info.id_proveedor == val.id) ? ' selected="selected"' : '';
                            $('#select-proveedor-editar').append('<option value="' + val.id + '"' + selected + '>' + val.nombre + '</option>');
                        });
                    } else {
                        toastr.error('Información no encontrada');
                    }
                })
                .catch(() => { closeLoading(); toastr.error('Información no encontrada'); });
        }

        function guardarEdicion() {
            var id          = $('#id-editar').val();
            var fecha       = $('#fecha-editar').val();
            var descripcion = $('#descripcion-editar').val();
            var lote        = $('#lote-editar').val();
            var proveedor   = $('#select-proveedor-editar').val();

            if (!fecha) { toastr.error('La fecha es requerida'); return; }

            openLoading();
            var formData = new FormData();
            formData.append('id', id);
            formData.append('fecha', fecha);
            formData.append('descripcion', descripcion);
            formData.append('lote', lote);
            formData.append('proveedor', proveedor);

            axios.post(url + '/historial/entrada/editar', formData)
                .then((response) => {
                    closeLoading();
                    if (response.data.success === 1) {
                        toastr.success('Actualizado correctamente');
                        $('#modalEditar').modal('hide');
                        recargar();
                    } else {
                        toastr.error('Error al actualizar');
                    }
                })
                .catch(() => { toastr.error('Error al actualizar'); closeLoading(); });
        }
    </script>

@endsection
