@extends('backend.menus.superior')

@section('content-admin-css')
    <link href="{{ asset('css/adminlte.min.css') }}"          type="text/css" rel="stylesheet" />
    <link href="{{ asset('css/dataTables.bootstrap4.css') }}" type="text/css" rel="stylesheet" />
    <link href="{{ asset('css/toastr.min.css') }}"            type="text/css" rel="stylesheet" />
@stop

<style>
    .dataTables_wrapper .dataTables_info     { float:left !important; text-align:left; }
    .dataTables_wrapper .dataTables_paginate { float:left !important; text-align:left; padding-left:10px; }

    .btn-back {
        display: inline-flex; align-items: center; gap: 7px;
        padding: 7px 16px; font-size: 13px; font-weight: 600;
        border: none; border-radius: 7px; cursor: pointer;
        background: #f1f5f9; color: #475569; transition: all .15s;
        text-decoration: none;
    }
    .btn-back:hover { background: #e2e8f0; color: #1e293b; text-decoration: none; }

    .material-card {
        background: #fff; border-radius: 12px; border: 1px solid #e2e8f0;
        box-shadow: 0 2px 10px rgba(0,0,0,.06); padding: 20px 24px;
        margin-bottom: 20px; display: flex; align-items: flex-start;
        gap: 20px; flex-wrap: wrap;
    }
    .material-icon-wrap {
        width: 52px; height: 52px; border-radius: 12px;
        background: #eff6ff; color: #3b82f6;
        display: flex; align-items: center; justify-content: center;
        font-size: 22px; flex-shrink: 0;
    }
    .material-title { font-size: 20px; font-weight: 700; color: #1e293b; margin: 0 0 4px; }
    .material-meta  { display: flex; flex-wrap: wrap; gap: 10px; margin-top: 8px; }
    .meta-chip {
        display: inline-flex; align-items: center; gap: 5px;
        padding: 3px 10px; border-radius: 20px; font-size: 12px;
        background: #f1f5f9; color: #475569;
    }
    .meta-chip i { color: #94a3b8; font-size: 11px; }

    .stock-badge {
        display: inline-flex; align-items: center; gap: 5px;
        padding: 3px 10px; border-radius: 20px; font-size: 12px; font-weight: 600;
    }
    .stock-badge.ok     { background: #dcfce7; color: #166534; }
    .stock-badge.warn   { background: #fef9c3; color: #854d0e; }
    .stock-badge.danger { background: #fee2e2; color: #991b1b; }
    .stock-badge .dot   { width: 6px; height: 6px; border-radius: 50%; background: currentColor; }

    .lote-badge {
        font-size: 11px; background: #f1f5f9; color: #475569;
        padding: 2px 8px; border-radius: 4px; font-family: monospace;
    }
    .btn-action-mov {
        display: inline-flex; align-items: center; gap: 5px;
        padding: 5px 12px; border-radius: 6px; font-size: 12px; font-weight: 600;
        border: none; cursor: pointer; transition: all .15s;
        background: #6366f1; color: #fff;
    }
    .btn-action-mov:hover { background: #4f46e5; }

    .fecha-chip { display: inline-flex; align-items: center; gap: 4px; font-size: 12px; color: #64748b; }

    .empty-state { text-align: center; padding: 48px 20px; color: #94a3b8; }
    .empty-state i { font-size: 40px; margin-bottom: 12px; display: block; }
    .empty-state p { font-size: 14px; margin: 0; }

    @media (max-width: 768px) {
        .material-card { flex-direction: column; }
    }
</style>

<div id="divcontenedor" style="display:none">

    <section class="content-header">
        <div style="display:flex; align-items:center; gap:12px; margin-bottom:18px; flex-wrap:wrap">
            <a href="{{ url('/admin/materiales/index') }}" class="btn-back">
                <i class="fas fa-arrow-left"></i> Volver
            </a>
            <h1 style="margin:0; font-size:20px; font-weight:700; color:#1e293b">
                <i class="fas fa-box-open" style="color:#3b82f6; margin-right:8px"></i>Detalle del Material
            </h1>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">

            {{-- INFO CARD --}}
            <div class="material-card" id="infoCard">
                <div class="material-icon-wrap">
                    <i class="fas fa-box"></i>
                </div>
                <div style="flex:1">
                    <div class="material-title" id="mat-nombre">Cargando...</div>
                    <div style="font-size:12px; color:#94a3b8; margin-top:2px">
                        Código: <span id="mat-codigo" style="font-family:monospace">—</span>
                    </div>
                    <div class="material-meta" id="mat-meta"></div>
                </div>
            </div>

            {{-- TABLA LOTES --}}
            <div class="card card-gray-dark">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h3 class="card-title" style="margin:0">
                        <i class="fas fa-layer-group" style="margin-right:6px"></i>Lotes con Stock Disponible
                    </h3>
                </div>
                <div class="card-body">
                    <div id="tablaDatatable"></div>
                </div>
            </div>

        </div>
    </section>

</div>


@extends('backend.menus.footerjs')
@section('archivos-js')

    <script src="{{ asset('js/jquery.dataTables.js') }}"     type="text/javascript"></script>
    <script src="{{ asset('js/dataTables.bootstrap4.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/toastr.min.js') }}"            type="text/javascript"></script>
    <script src="{{ asset('js/axios.min.js') }}"             type="text/javascript"></script>
    <script src="{{ asset('js/sweetalert2.all.min.js') }}"></script>
    <script src="{{ asset('js/alertaPersonalizada.js') }}"></script>

    <script>
        $(document).ready(function(){
            let id = {{ $id }};

            // ── Info del material ─────────────────────────────────────────────
            axios.post(url + '/materiales/informacion', { id: id })
                .then(res => {
                    if (res.data.success !== 1) return;
                    var m = res.data.material;

                    $('#mat-nombre').text(m.nombre);
                    $('#mat-codigo').text(m.codigo || '—');

                    var meta = '';
                    if (res.data.marca && res.data.marca.length)
                        meta += '<span class="meta-chip"><i class="fas fa-tag"></i>' + (res.data.marca.find(x => x.id == m.id_marca)?.nombre || '—') + '</span>';
                    if (res.data.unidad && res.data.unidad.length)
                        meta += '<span class="meta-chip"><i class="fas fa-ruler"></i>' + (res.data.unidad.find(x => x.id == m.id_medida)?.nombre || '—') + '</span>';
                    if (res.data.normativa && res.data.normativa.length)
                        meta += '<span class="meta-chip"><i class="fas fa-shield-alt"></i>' + (res.data.normativa.find(x => x.id == m.id_normativa)?.nombre || '—') + '</span>';
                    if (m.meses_cambio && m.meses_cambio > 0)
                        meta += '<span class="meta-chip"><i class="fas fa-clock"></i>' + m.meses_cambio + ' meses estimados</span>';
                    if (m.otros)
                        meta += '<span class="meta-chip"><i class="fas fa-sticky-note"></i>' + m.otros + '</span>';

                    $('#mat-meta').html(meta);
                })
                .catch(() => {});

            // ── Cargar tabla de lotes ─────────────────────────────────────────
            var ruta = "{{ URL::to('/admin/material/detalle/tabla') }}/" + id;
            $('#tablaDatatable').load(ruta, function(){
                document.getElementById("divcontenedor").style.display = "block";
                closeLoading();
            });
        });

        // ── Movimientos ───────────────────────────────────────────────────────
        function movimientos(id){
            window.location.href = "{{ url('/admin/material/movimientos/detalle') }}/" + id;
        }
    </script>

@endsection
