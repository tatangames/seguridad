@extends('backend.menus.superior')

@section('content-admin-css')
    <link href="{{ asset('css/adminlte.min.css') }}" type="text/css" rel="stylesheet" />
    <link href="{{ asset('css/dataTables.bootstrap4.css') }}" type="text/css" rel="stylesheet" />
    <link href="{{ asset('css/toastr.min.css') }}" type="text/css" rel="stylesheet" />
    <link href="{{ asset('css/buttons_estilo.css') }}" rel="stylesheet">
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
        --accent-dim:    rgba(14, 164, 114, 0.10);
        --accent-glow:   rgba(14, 164, 114, 0.20);
        --danger:        #ef4444;
        --danger-dim:    rgba(239, 68, 68, 0.10);
        --warning:       #f59e0b;
        --warning-dim:   rgba(245, 158, 11, 0.10);
        --info:          #0ea5e9;
        --info-dim:      rgba(14, 165, 233, 0.10);
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
        padding: 24px 32px 20px;
        border-bottom: 1px solid var(--border);
        background: var(--bg-card);
        margin-bottom: 0;
    }

    .page-header-left {
        display: flex;
        align-items: center;
        gap: 16px;
    }

    .page-icon {
        width: 44px; height: 44px;
        background: var(--info-dim);
        border: 1px solid rgba(14,165,233,0.25);
        border-radius: 10px;
        display: flex; align-items: center; justify-content: center;
        color: var(--info);
        font-size: 18px;
        flex-shrink: 0;
    }

    .page-title {
        font-family: var(--mono);
        font-size: 17px;
        font-weight: 600;
        color: var(--text-primary);
        letter-spacing: -0.3px;
        margin: 0;
    }

    .page-subtitle {
        font-size: 11px;
        color: var(--text-muted);
        font-family: var(--mono);
        letter-spacing: 0.5px;
        margin: 3px 0 0;
    }

    .btn-back {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        padding: 8px 16px;
        background: transparent;
        border: 1px solid var(--border);
        border-radius: 8px;
        color: var(--text-secondary);
        font-family: var(--mono);
        font-size: 11px;
        font-weight: 500;
        cursor: pointer;
        text-decoration: none;
        transition: all 0.15s;
        letter-spacing: 0.3px;
    }
    .btn-back:hover {
        background: var(--bg-card-alt);
        border-color: var(--border-light);
        color: var(--text-primary);
        text-decoration: none;
    }

    /* ── INFO CARD ── */
    .info-card {
        margin: 20px 32px 0;
        background: var(--bg-card);
        border: 1px solid var(--border);
        border-radius: 12px;
        overflow: hidden;
    }

    .info-card-header {
        padding: 12px 20px;
        background: var(--bg-card-alt);
        border-bottom: 1px solid var(--border);
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .info-card-label {
        font-family: var(--mono);
        font-size: 10px;
        font-weight: 500;
        color: var(--text-muted);
        letter-spacing: 1px;
        text-transform: uppercase;
    }

    .info-card-body {
        padding: 16px 20px;
        display: flex;
        flex-wrap: wrap;
        gap: 24px;
    }

    .info-field {
        display: flex;
        flex-direction: column;
        gap: 3px;
    }

    .info-field-label {
        font-family: var(--mono);
        font-size: 9px;
        font-weight: 500;
        color: var(--text-muted);
        letter-spacing: 1px;
        text-transform: uppercase;
    }

    .info-field-value {
        font-size: 13px;
        font-weight: 500;
        color: var(--text-primary);
    }

    .info-field-value.mono {
        font-family: var(--mono);
        color: var(--warning);
    }

    .info-field-value.date {
        font-family: var(--mono);
        color: var(--info);
    }

    /* ── MAIN CARD ── */
    .main-card {
        margin: 20px 32px 32px;
        background: var(--bg-card);
        border: 1px solid var(--border);
        border-radius: 12px;
        overflow: hidden;
    }

    .main-card-header {
        padding: 14px 24px;
        border-bottom: 1px solid var(--border);
        display: flex;
        align-items: center;
        justify-content: space-between;
        background: var(--bg-card-alt);
    }

    .main-card-title {
        font-family: var(--mono);
        font-size: 11px;
        font-weight: 500;
        color: var(--text-secondary);
        letter-spacing: 1px;
        text-transform: uppercase;
        margin: 0;
    }

    .items-badge {
        font-family: var(--mono);
        font-size: 10px;
        color: var(--accent);
        background: var(--accent-dim);
        border: 1px solid rgba(14,164,114,0.2);
        padding: 2px 10px;
        border-radius: 20px;
    }

    /* ── TABLE ── */
    #tabla_wrapper { padding: 20px; }

    #tabla {
        background: transparent !important;
        color: var(--text-primary) !important;
        border-collapse: separate !important;
        border-spacing: 0 5px !important;
        font-size: 13px;
        width: 100% !important;
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
        padding: 12px 16px !important;
        white-space: nowrap;
    }

    #tabla thead th::after,
    #tabla thead th::before { color: var(--text-muted) !important; }

    #tabla tbody tr { background: transparent !important; }

    #tabla tbody td {
        background: #ffffff !important;
        border: none !important;
        border-top: 1px solid var(--border) !important;
        border-bottom: 1px solid var(--border) !important;
        padding: 14px 16px !important;
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
        background: #f0fdf8 !important;
        border-top-color: #a7f3d0 !important;
        border-bottom-color: #a7f3d0 !important;
    }

    #tabla tbody tr:hover td:first-child {
        border-left-color: var(--info) !important;
    }

    /* chips dentro de la tabla */
    .qty-chip {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        font-family: var(--mono);
        font-size: 12px;
        font-weight: 600;
        color: var(--accent);
        background: var(--accent-dim);
        border: 1px solid rgba(14,164,114,0.2);
        padding: 2px 9px;
        border-radius: 20px;
    }

    .price-chip {
        font-family: var(--mono);
        font-size: 12px;
        font-weight: 600;
        color: var(--warning);
        background: var(--warning-dim);
        border: 1px solid rgba(245,158,11,0.2);
        padding: 2px 9px;
        border-radius: 4px;
    }

    .tag-chip {
        display: inline-block;
        font-size: 11px;
        color: var(--text-secondary);
        background: var(--bg-card-alt);
        border: 1px solid var(--border);
        padding: 1px 7px;
        border-radius: 4px;
        font-family: var(--mono);
        max-width: 100%;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .empty-val {
        color: var(--text-muted);
        font-family: var(--mono);
        font-size: 12px;
    }

    /* ── ACTION BUTTON ── */
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

    .btn-action.delete {
        color: var(--danger);
        border-color: rgba(239,68,68,0.25);
        background: var(--danger-dim);
    }
    .btn-action.delete:hover {
        background: rgba(239,68,68,0.2);
        border-color: var(--danger);
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

    /* stripe */
    table.dataTable.table-striped tbody tr:nth-of-type(odd)  td { background: #fafbfc !important; }
    table.dataTable.table-striped tbody tr:nth-of-type(even) td { background: #ffffff  !important; }

    /* row anim */
    #tabla tbody tr {
        animation: rowIn 0.2s ease both;
    }
    @keyframes rowIn {
        from { opacity: 0; transform: translateY(3px); }
        to   { opacity: 1; transform: translateY(0); }
    }

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
                <i class="fas fa-list-alt"></i>
            </div>
            <div>
                <h1 class="page-title">Detalle de Entrada</h1>
                <p class="page-subtitle">BODEGA / HISTORIAL ENTRADAS / DETALLE</p>
            </div>
        </div>
        <a href="javascript:history.back()" class="btn-back">
            <i class="fas fa-arrow-left"></i> Volver
        </a>
    </div>

    <!-- INFO DEL INGRESO -->
    <div class="info-card">
        <div class="info-card-header">
            <i class="fas fa-info-circle" style="color:var(--info); font-size:12px"></i>
            <span class="info-card-label">Información del Ingreso</span>
        </div>
        <div class="info-card-body">
            <div class="info-field">
                <span class="info-field-label">Fecha</span>
                <span class="info-field-value date">
                    {{ $info ? date('d-m-Y', strtotime($info->fecha)) : '—' }}
                </span>
            </div>
            <div class="info-field">
                <span class="info-field-label">Factura / Lote</span>
                <span class="info-field-value mono">
                    {{ $info->lote ?? '—' }}
                </span>
            </div>
            <div class="info-field">
                <span class="info-field-label">Observación</span>
                <span class="info-field-value">
                    {{ $info->descripcion ?? '—' }}
                </span>
            </div>
        </div>
    </div>

    <!-- TABLA DETALLE -->
    <div class="main-card">
        <div class="main-card-header">
            <span class="main-card-title">Productos Ingresados</span>
            <span class="items-badge" id="itemsBadge">— ítems</span>
        </div>
        <div id="tablaDatatable"></div>
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

    <script type="text/javascript">
        $(document).ready(function () {
            openLoading();
            let id = {{ $id }};
            var ruta = "{{ URL::to('/admin/historial/entradadetalle/tabla') }}/" + id;
            $('#tablaDatatable').load(ruta, function () {
                // Actualizar badge con total de ítems
                setTimeout(function () {
                    var total = $('#tabla').DataTable().rows().count();
                    $('#itemsBadge').text(total + (total === 1 ? ' ítem' : ' ítems'));
                }, 400);
            });

            document.getElementById("divcontenedor").style.display = "block";
        });
    </script>

    <script>
        function recargar() {
            openLoading();
            let id = {{ $id }};
            var ruta = "{{ URL::to('/admin/historial/entradadetalle/tabla') }}/" + id;
            $('#tablaDatatable').load(ruta, function () {
                setTimeout(function () {
                    var total = $('#tabla').DataTable().rows().count();
                    $('#itemsBadge').text(total + (total === 1 ? ' ítem' : ' ítems'));
                }, 400);
            });
        }

        function infoBorrar(id) {
            Swal.fire({
                title: 'Confirmar eliminación',
                html: '<p style="color:#475569; font-size:13px;">Esto eliminará el ingreso de este producto,<br>sus salidas y movimientos asociados.</p>',
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

            axios.post(url + '/historial/entradadetalle/borraritem', formData)
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
    </script>

@endsection
