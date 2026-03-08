<style>
    :root {
        --bg-base:    #ffffff;
        --bg-card:    #ffffff;
        --bg-card2:   #f4f6fa;
        --accent:     #3b7ef6;
        --accent2:    #0fb87a;
        --danger:     #e74c3c;
        --warning:    #f39c12;
        --text-primary: #1a1f36;
        --text-muted:   #7a849e;
        --border:     #e2e6ef;
        --radius:     10px;
        --radius-sm:  6px;
    }

    /* ── Tabla principal ── */
    #tabla {
        border-collapse: separate;
        border-spacing: 0;
        width: 100% !important;
        color: var(--text-primary);
        font-family: 'DM Sans', sans-serif;
        font-size: .875rem;
    }

    #tabla thead th {
        background: #f4f6fa !important;
        color: var(--text-muted) !important;
        font-size: .7rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .1em;
        border: none !important;
        border-bottom: 2px solid var(--border) !important;
        padding: 12px 14px !important;
        white-space: nowrap;
    }

    #tabla tbody tr {
        background: #fff;
        transition: background .15s;
    }
    #tabla tbody tr:nth-child(even) { background: #fafbfd; }
    #tabla tbody tr:hover { background: #eef3ff !important; }

    #tabla tbody td {
        border: none !important;
        border-bottom: 1px solid var(--border) !important;
        padding: 13px 14px !important;
        vertical-align: middle;
    }

    /* ── Badge fecha ── */
    .badge-fecha {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: rgba(79,142,247,.12);
        color: var(--accent);
        border: 1px solid rgba(79,142,247,.25);
        border-radius: var(--radius-sm);
        padding: 4px 10px;
        font-size: .78rem;
        font-weight: 600;
        font-family: 'DM Mono', monospace;
        white-space: nowrap;
    }
    .badge-fecha i { font-size: .7rem; }

    /* ── Badge distrito ── */
    .badge-distrito {
        display: inline-block;
        background: rgba(34,211,165,.1);
        color: var(--accent2);
        border: 1px solid rgba(34,211,165,.2);
        border-radius: 20px;
        padding: 3px 10px;
        font-size: .75rem;
        font-weight: 600;
    }

    /* ── Nombre empleado ── */
    .empleado-cell { display: flex; align-items: center; gap: 10px; }
    .empleado-avatar {
        width: 30px; height: 30px; flex-shrink: 0;
        background: linear-gradient(135deg, var(--accent), #2f5fc4);
        border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-size: .72rem; font-weight: 700; color: #fff;
    }
    .empleado-avatar.jefe {
        background: linear-gradient(135deg, var(--warning), #d97706);
    }
    .empleado-nombre { font-weight: 500; font-size: .875rem; }
    .empleado-tag {
        font-size: .65rem; font-weight: 700;
        background: rgba(247,185,85,.15);
        color: var(--warning);
        border-radius: 4px;
        padding: 1px 5px;
        text-transform: uppercase;
        letter-spacing: .05em;
    }

    /* ── Descripción truncada ── */
    .desc-cell {
        color: var(--text-muted);
        max-width: 220px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        font-size: .83rem;
    }

    /* ── Material línea ── */
    .linea-cell {
        font-family: 'DM Mono', monospace;
        font-size: .8rem;
        color: var(--text-primary);
        background: rgba(255,255,255,.04);
        border-radius: var(--radius-sm);
        padding: 4px 8px;
        display: inline-block;
        max-width: 160px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    /* ── Botones de acción ── */
    .btn-action {
        display: inline-flex; align-items: center; gap: 5px;
        border: none; border-radius: var(--radius-sm);
        padding: 5px 11px;
        font-size: .75rem; font-weight: 600;
        cursor: pointer;
        transition: opacity .15s, transform .1s;
        text-decoration: none;
    }
    .btn-action:hover { opacity: .85; transform: translateY(-1px); }
    .btn-action.editar  { background: rgba(79,142,247,.2);  color: var(--accent); }
    .btn-action.detalle { background: rgba(34,211,165,.15); color: var(--accent2); }
    .btn-action.pdf     { background: rgba(247,95,95,.15);  color: var(--danger); }

    .opciones-cell { display: flex; gap: 5px; flex-wrap: wrap; }

    /* ── DataTables dark theme ── */
    .dataTables_wrapper { color: var(--text-primary) !important; }

    .dataTables_filter input,
    .dataTables_length select {
        background: #fff !important;
        border: 1px solid var(--border) !important;
        color: var(--text-primary) !important;
        border-radius: var(--radius-sm) !important;
        padding: 6px 12px !important;
        outline: none;
        margin-left: 6px;
    }
    .dataTables_filter input:focus {
        border-color: var(--accent) !important;
        box-shadow: 0 0 0 2px rgba(59,126,246,.15) !important;
    }
    .dataTables_filter label,
    .dataTables_length label,
    .dataTables_info { color: var(--text-muted) !important; font-size: .82rem !important; }

    .paginate_button { border-radius: var(--radius-sm) !important; color: var(--text-muted) !important; }
    .paginate_button.current,
    .paginate_button.current:hover {
        background: var(--accent) !important;
        border-color: var(--accent) !important;
        color: #fff !important;
    }
    .paginate_button:not(.disabled):hover {
        background: #eef2ff !important;
        border-color: var(--border) !important;
        color: var(--accent) !important;
    }
    .paginate_button.disabled { color: #ccc !important; }

    /* fix card body */
    .card, .card-body { background: transparent !important; border: none !important; }

    /* Scroll horizontal */
    .card-body { overflow-x: auto !important; -webkit-overflow-scrolling: touch; }
    #tabla { min-width: 860px; }
    /* ── Filtro de fechas ── */
    .filtro-fechas {
        display: flex;
        align-items: flex-end;
        gap: 12px;
        flex-wrap: wrap;
        background: #fff;
        border: 1px solid var(--border);
        border-radius: var(--radius);
        padding: 16px 20px;
        margin-bottom: 18px;
        box-shadow: 0 1px 4px rgba(0,0,0,.05);
    }
    .filtro-group {
        display: flex;
        flex-direction: column;
        gap: 5px;
    }
    .filtro-group label {
        font-size: .7rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .07em;
        color: var(--text-muted);
    }
    .filtro-group input[type="date"] {
        background: #f4f6fa;
        border: 1px solid var(--border);
        border-radius: var(--radius-sm);
        color: var(--text-primary);
        font-family: 'DM Sans', sans-serif;
        font-size: .88rem;
        padding: 8px 12px;
        outline: none;
        transition: border-color .2s, box-shadow .2s;
        min-width: 150px;
    }
    .filtro-group input[type="date"]:focus {
        border-color: var(--accent);
        box-shadow: 0 0 0 3px rgba(59,126,246,.15);
        background: #fff;
    }
    .filtro-sep {
        font-size: .9rem;
        color: var(--text-muted);
        padding-bottom: 9px;
    }
    .btn-limpiar-filtro {
        display: inline-flex; align-items: center; gap: 6px;
        background: transparent;
        color: var(--text-muted);
        border: 1px solid var(--border);
        border-radius: var(--radius-sm);
        padding: 9px 16px;
        font-size: .82rem;
        font-weight: 500;
        cursor: pointer;
        transition: border-color .2s, color .2s;
        align-self: flex-end;
    }
    .btn-limpiar-filtro:hover { border-color: #e74c3c; color: #e74c3c; }
    .filtro-resultado {
        margin-left: auto;
        align-self: flex-end;
        font-size: .8rem;
        color: var(--text-muted);
        padding-bottom: 9px;
        white-space: nowrap;
    }
    .filtro-resultado strong { color: var(--accent); font-weight: 700; }
    .filtro-activo { border-color: var(--accent) !important; }
</style>

<link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">

<!-- Filtro de fechas -->
<div style="padding: 18px 18px 0">
    <div class="filtro-fechas" id="filtro-fechas-box">
        <div class="filtro-group">
            <label><i class="fas fa-calendar-alt" style="margin-right:4px;color:var(--accent)"></i>Desde</label>
            <input type="date" id="filtro-desde">
        </div>
        <div class="filtro-sep">—</div>
        <div class="filtro-group">
            <label><i class="fas fa-calendar-alt" style="margin-right:4px;color:var(--accent)"></i>Hasta</label>
            <input type="date" id="filtro-hasta">
        </div>
        <button class="btn-limpiar-filtro" id="btn-limpiar-filtro" onclick="limpiarFiltroFechas()" style="display:none">
            <i class="fas fa-times"></i> Limpiar
        </button>
        <span class="filtro-resultado" id="filtro-resultado" style="display:none">
            Mostrando <strong id="filtro-count">0</strong> registros
        </span>
    </div>
</div>

<section class="content" style="padding-top: 4px">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body" style="padding: 0">
                        <table id="tabla" class="table">
                            <thead>
                            <tr>
                                <th style="width:11%">Fecha</th>
                                <th style="width:11%">Distrito</th>
                                <th style="width:12%">Unidad</th>
                                <th style="width:17%">Empleado</th>
                                <th style="width:20%">Descripción</th>
                                <th style="width:14%"># Equipo</th>
                                <th style="width:15%">Opciones</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($listado as $dato)
                                @php
                                    $esJefe = str_contains($dato->empleado, '(JEFE)');
                                    $nombreLimpio = str_replace(' (JEFE)', '', $dato->empleado);
                                    $iniciales = collect(explode(' ', $nombreLimpio))->take(2)->map(fn($p) => strtoupper($p[0] ?? ''))->implode('');
                                @endphp
                                <tr>
                                    <td>
                                        <span class="badge-fecha">
                                            <i class="fas fa-calendar-alt"></i>
                                            {{ $dato->fechaFormat }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge-distrito">{{ $dato->distrito }}</span>
                                    </td>
                                    <td style="color: var(--text-muted); font-size:.85rem">{{ $dato->unidad }}</td>
                                    <td>
                                        <div class="empleado-cell">
                                            <div class="empleado-avatar {{ $esJefe ? 'jefe' : '' }}">{{ $iniciales }}</div>
                                            <div>
                                                <div class="empleado-nombre">{{ $nombreLimpio }}</div>
                                                @if($esJefe)
                                                    <span class="empleado-tag">Jefe</span>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="desc-cell" title="{{ $dato->descripcion }}">
                                            {{ $dato->descripcion ?: '—' }}
                                        </div>
                                    </td>
                                    <td>
                                        @if($dato->material_linea)
                                            <span class="linea-cell" title="{{ $dato->material_linea }}">{{ $dato->material_linea }}</span>
                                        @else
                                            <span style="color:var(--text-muted);font-size:.8rem">—</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="opciones-cell">
                                            <button class="btn-action editar" onclick="informacion({{ $dato->id }})">
                                                <i class="fas fa-edit"></i> Editar
                                            </button>
                                            <button class="btn-action detalle" onclick="vistaDetalle({{ $dato->id }})">
                                                <i class="fas fa-eye"></i> Detalle
                                            </button>
                                            <button class="btn-action pdf" onclick="reportePDF({{ $dato->id }})">
                                                <i class="fas fa-file-pdf"></i> PDF
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach

                            <script>
                                setTimeout(function(){ closeLoading(); }, 500);
                            </script>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
    $(function(){
        $.fn.dataTable.ext.type.order['date-dd-mm-yyyy-pre'] = function(date){
            var p = date.split('-');
            return new Date(p[2], p[1]-1, p[0]).getTime();
        };

        // Filtro de rango de fechas registrado en el motor de DataTables
        $.fn.dataTable.ext.search.push(function(settings, data){
            if(settings.nTable.id !== 'tabla') return true;

            var desdeVal = document.getElementById('filtro-desde')?.value;
            var hastaVal = document.getElementById('filtro-hasta')?.value;
            if(!desdeVal && !hastaVal) return true;

            // data[0] = texto de celda Fecha → "dd-mm-yyyy" (puede tener HTML, limpiamos)
            var txt = data[0].replace(/<[^>]+>/g,'').trim();
            var p   = txt.split('-');
            if(p.length !== 3) return true;

            var fechaTs = new Date(parseInt(p[2]), parseInt(p[1])-1, parseInt(p[0])).getTime();
            if(isNaN(fechaTs)) return true;

            var ok = true;
            if(desdeVal) ok = ok && fechaTs >= new Date(desdeVal).getTime();
            if(hastaVal) ok = ok && fechaTs <= new Date(hastaVal + 'T23:59:59').getTime();
            return ok;
        });

        window._dtInstance = $("#tabla").DataTable({
            paging: true,
            lengthChange: true,
            order: [[0, 'desc']],
            searching: true,
            ordering: true,
            info: true,
            autoWidth: false,
            responsive: true,
            pagingType: "full_numbers",
            lengthMenu: [[25, 100, 500, -1], [25, 100, 500, "Todo"]],
            language: {
                sProcessing:   "Procesando...",
                sLengthMenu:   "Mostrar _MENU_ registros",
                sZeroRecords:  "No se encontraron resultados",
                sEmptyTable:   "Sin datos disponibles",
                sInfo:         "Registros _START_ – _END_ de _TOTAL_",
                sInfoEmpty:    "0 registros",
                sInfoFiltered: "(filtrado de _MAX_)",
                sSearch:       "Buscar:",
                oPaginate: {
                    sFirst: "«", sLast: "»", sNext: "›", sPrevious: "‹"
                }
            },
            columnDefs: [
                { targets: 0, type: "date-dd-mm-yyyy" },
                { targets: 6, orderable: false }
            ]
        });

        // Escuchar cambios en los inputs → filtrar automáticamente
        $('#filtro-desde, #filtro-hasta').on('change', function(){
            var dt = window._dtInstance;
            if(!dt) return;

            dt.draw();

            var desde = document.getElementById('filtro-desde').value;
            var hasta  = document.getElementById('filtro-hasta').value;
            var activo = desde || hasta;

            var count = dt.rows({ search: 'applied' }).count();
            document.getElementById('filtro-count').textContent = count;
            document.getElementById('filtro-resultado').style.display = activo ? 'inline' : 'none';
            document.getElementById('btn-limpiar-filtro').style.display = activo ? 'inline-flex' : 'none';
            document.getElementById('filtro-fechas-box').classList.toggle('filtro-activo', !!activo);
        });
    });

    function limpiarFiltroFechas(){
        document.getElementById('filtro-desde').value = '';
        document.getElementById('filtro-hasta').value = '';
        document.getElementById('filtro-resultado').style.display = 'none';
        document.getElementById('btn-limpiar-filtro').style.display = 'none';
        document.getElementById('filtro-fechas-box').classList.remove('filtro-activo');
        if(window._dtInstance) window._dtInstance.draw();
    }
</script>
