<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div style="padding: 8px 0; overflow-x: auto; -webkit-overflow-scrolling: touch;">
                    <table id="tabla" class="table table-bordered table-striped" style="min-width: 900px;">
                        <thead>
                        <tr>
                            <th>Material</th>
                            <th>Medida</th>
                            <th>Marca</th>
                            <th>Normativa</th>
                            <th>Color</th>
                            <th>Talla</th>
                            <th>Cantidad</th>
                            <th>Precio U.</th>
                            <th>Opción</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($listado as $dato)
                            <tr>
                                <td title="{{ $dato->nombre }}">{{ $dato->nombre }}</td>
                                <td>
                                    @if($dato->nombreUnidad)
                                        <span class="tag-chip">{{ $dato->nombreUnidad }}</span>
                                    @else
                                        <span class="empty-val">—</span>
                                    @endif
                                </td>
                                <td>
                                    @if($dato->marca)
                                        <span class="tag-chip">{{ $dato->marca }}</span>
                                    @else
                                        <span class="empty-val">—</span>
                                    @endif
                                </td>
                                <td>
                                    @if($dato->normativa)
                                        <span class="tag-chip">{{ $dato->normativa }}</span>
                                    @else
                                        <span class="empty-val">—</span>
                                    @endif
                                </td>
                                <td>
                                    @if($dato->color)
                                        <span class="tag-chip">{{ $dato->color }}</span>
                                    @else
                                        <span class="empty-val">—</span>
                                    @endif
                                </td>
                                <td>
                                    @if($dato->talla)
                                        <span class="tag-chip">{{ $dato->talla }}</span>
                                    @else
                                        <span class="empty-val">—</span>
                                    @endif
                                </td>
                                <td>
                                        <span class="qty-chip">
                                            <i class="fas fa-cubes" style="font-size:9px"></i>
                                            {{ $dato->cantidad }}
                                        </span>
                                </td>
                                <td>
                                    <span class="price-chip">{{ $dato->precioFormat }}</span>
                                </td>
                                <td>
                                    <button type="button" class="btn-action delete" onclick="infoBorrar({{ $dato->id }})">
                                        <i class="fas fa-trash"></i> Borrar
                                    </button>
                                </td>
                            </tr>
                        @endforeach

                        <script>
                            setTimeout(function () { closeLoading(); }, 500);
                        </script>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
    $(function () {

        $("#tabla").DataTable({
            "paging":       true,
            "lengthChange": true,
            "order":        [[0, 'asc']],
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
                { "targets": [6, 7, 8], "orderable": false, "searchable": false }
            ],
            "responsive": true,
            "dom": '<"row align-items-center mb-2"<"col-sm-6"l><"col-sm-6 text-right"f>>rtip',
        });

    });
</script>
