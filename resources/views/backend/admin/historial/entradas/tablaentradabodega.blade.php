<style>
    /* ── FILTROS tfoot ── */
    #tabla tfoot th {
        padding: 6px 8px !important;
        background: #f8fafc !important;
        border-top: 2px solid #e2e8f0 !important;
    }

    .filter-input {
        width: 100%;
        padding: 5px 8px;
        font-size: 11px;
        font-family: 'IBM Plex Mono', monospace;
        border: 1px solid #e2e8f0;
        border-radius: 6px;
        background: #ffffff;
        color: #1e293b;
        outline: none;
        transition: border-color 0.2s, box-shadow 0.2s;
    }

    .filter-input:focus {
        border-color: #0ea472;
        box-shadow: 0 0 0 2px rgba(14,164,114,0.15);
    }

    .filter-input::placeholder { color: #94a3b8; }

    .filter-select {
        width: 100%;
        padding: 5px 6px;
        font-size: 11px;
        font-family: 'IBM Plex Mono', monospace;
        border: 1px solid #e2e8f0;
        border-radius: 6px;
        background: #ffffff;
        color: #1e293b;
        outline: none;
        cursor: pointer;
        transition: border-color 0.2s;
    }

    .filter-select:focus { border-color: #0ea472; }

    .filter-clear-btn {
        width: 100%;
        padding: 5px 8px;
        font-size: 10px;
        font-family: 'IBM Plex Mono', monospace;
        border: 1px solid #e2e8f0;
        border-radius: 6px;
        background: #fff;
        color: #94a3b8;
        cursor: pointer;
        transition: all 0.15s;
        letter-spacing: 0.5px;
    }

    .filter-clear-btn:hover {
        background: #fef2f2;
        border-color: #ef4444;
        color: #ef4444;
    }
</style>

<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div style="padding: 8px 0;">
                    <table id="tabla" class="table table-bordered table-striped">
                        <thead>
                        <tr>
                            <th style="width: 11%">Fecha</th>
                            <th style="width: 32%">Observación</th>
                            <th style="width: 12%">Factura</th>
                            <th style="width: 13%">Proveedor</th>
                            <th style="width: 32%">Opciones</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($listado as $dato)
                            {{--
                                data-order usa formato YYYY-MM-DD para ordenamiento correcto.
                                El texto visible sigue siendo dd-mm-yyyy.
                            --}}
                            <tr>
                                <td data-order="{{ $dato->fecha }}">
                                        <span class="date-chip">
                                            <i class="fas fa-calendar-alt" style="font-size:9px"></i>
                                            {{ $dato->fechaFormat }}
                                        </span>
                                </td>
                                <td>
                                        <span class="obs-text" title="{{ $dato->descripcion }}">
                                            {{ $dato->descripcion ?: '—' }}
                                        </span>
                                </td>
                                <td>
                                    @if($dato->lote)
                                        <span class="factura-chip">{{ $dato->lote }}</span>
                                    @else
                                        <span style="color:var(--text-muted); font-family:var(--mono); font-size:12px">—</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="proveedor-text">{{ $dato->nombreProveedor ?: '—' }}</span>
                                </td>
                                <td>
                                    <div class="actions-group">
                                        <button type="button" class="btn-action edit" onclick="modalEditar({{ $dato->id }})">
                                            <i class="fas fa-edit"></i> Editar
                                        </button>
                                        <button type="button" class="btn-action detail" onclick="vistaDetalle({{ $dato->id }})">
                                            <i class="fas fa-eye"></i> Detalle
                                        </button>
                                        <button type="button" class="btn-action add" onclick="infoNuevoIngreso({{ $dato->id }})">
                                            <i class="fas fa-plus"></i> Ingreso
                                        </button>
                                        <button type="button" class="btn-action delete" onclick="infoBorrar({{ $dato->id }})">
                                            <i class="fas fa-trash"></i> Borrar
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach

                        <script>
                            setTimeout(function () { closeLoading(); }, 500);
                        </script>
                        </tbody>
                        <tfoot>
                        <tr>
                            <th><input type="text" class="filter-input" placeholder="Filtrar fecha..."></th>
                            <th><input type="text" class="filter-input" placeholder="Filtrar observación..."></th>
                            <th><input type="text" class="filter-input" placeholder="Filtrar factura..."></th>
                            <th>
                                <select class="filter-select" id="filtroProveedor">
                                    <option value="">Todos los proveedores</option>
                                </select>
                            </th>
                            <th><button class="filter-clear-btn" id="btnLimpiarFiltros">✕ Limpiar filtros</button></th>
                        </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
    $(function () {

        var table = $("#tabla").DataTable({
            "paging":       true,
            "lengthChange": true,
            "order":        [[0, 'desc']],
            "searching":    true,
            "ordering":     true,
            "info":         true,
            "autoWidth":    false,
            "pagingType":   "full_numbers",
            "lengthMenu":   [[25, 100, 500, -1], [25, 100, 500, "Todo"]],
            "language": {
                "sProcessing":   "Procesando...",
                "sLengthMenu":   "Mostrar _MENU_ registros",
                "sZeroRecords":  "No se encontraron resultados",
                "sEmptyTable":   "Ningún dato disponible",
                "sInfo":         "Registros _START_ – _END_ de _TOTAL_",
                "sInfoEmpty":    "Registros 0 – 0 de 0",
                "sInfoFiltered": "(filtrado de _MAX_ registros)",
                "sSearch":       "Buscar:",
                "oPaginate": { "sFirst": "«", "sLast": "»", "sNext": "›", "sPrevious": "‹" }
            },
            "columnDefs": [
                // Columna 0: ordenar por data-order (YYYY-MM-DD), buscar por texto visible (dd-mm-yyyy)
                {
                    "targets": 0,
                    "orderData": 0,        // usa data-order del <td>
                    "searchable": true
                },
                { "targets": 4, "orderable": false, "searchable": false }
            ],
            "responsive": true,
            // Ocultar buscador global (usamos los filtros por columna)
            "dom": '<"row align-items-center mb-2"<"col-sm-6"l><"col-sm-6 text-right"f>>rtip',
        });

        // ── Filtros por columna (texto) cols: 0,1,2 ──
        $('#tabla tfoot th:not(:nth-child(4)):not(:last-child) .filter-input').each(function (i) {
            var colIndex = $(this).closest('th').index();
            $(this).on('keyup change', function () {
                table.column(colIndex).search(this.value).draw();
            });
        });

        // ── Filtro proveedor (select) col 3 ──
        // Poblar select con valores únicos de la columna Proveedor
        table.column(3).data().unique().sort().each(function (val) {
            if (val && val !== '—') {
                // val puede tener HTML, obtener texto limpio
                var texto = $('<span>').html(val).text().trim();
                if (texto && texto !== '—') {
                    $('#filtroProveedor').append('<option value="' + texto + '">' + texto + '</option>');
                }
            }
        });

        $('#filtroProveedor').on('change', function () {
            // Búsqueda exacta con regex para evitar coincidencias parciales
            var val = $.fn.dataTable.util.escapeRegex($(this).val());
            table.column(3).search(val ? '^' + val + '$' : '', true, false).draw();
        });

        // ── Limpiar todos los filtros ──
        $('#btnLimpiarFiltros').on('click', function () {
            $('#tabla tfoot .filter-input').val('');
            $('#filtroProveedor').val('');
            table.search('').columns().search('').draw();
        });

    });
</script>
