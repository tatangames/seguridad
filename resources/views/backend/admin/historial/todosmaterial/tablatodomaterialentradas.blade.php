<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <table id="tabla" class="table table-bordered table-striped">
                            <thead>
                            <tr>
                                <th>Fecha Ingreso</th>
                                <th>Lote</th>
                                <th>Descripción</th>
                                <th>Precio Orden Compra</th>
                                <th>N° Item</th>
                                <th>Cantidad Entró</th>
                                <th>Precio Factura</th>
                                <th>Cantidad Entregada</th>
                                <th>Opciones</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($arrayEntradasDetalle as $dato)
                                <tr>
                                    <td>{{ $dato->fechaFormat }}</td>
                                    <td>{{ $dato->lote }}</td>
                                    <td>{{ $dato->nombreMaterial }}</td>
                                    <td>${{ number_format((float)$dato->precio_ordencompra, 2) }}</td>
                                    <td>{{ $dato->numero_item }}</td>
                                    <td>{{ $dato->cantidad_inicial }}</td>
                                    <td>${{ number_format((float)$dato->precio, 2) }}</td>
                                    <td>{{ $dato->cantidad_entregada }}</td>
                                    <td>
                                        <button type="button" class="btn btn-success btn-xs"
                                                onclick="infoEditar({{ $dato->id }})">
                                            <i class="fas fa-edit" title="Editar"></i>&nbsp; Editar
                                        </button>
                                    </td>
                                </tr>
                            @endforeach

                            <script>
                                setTimeout(function () { closeLoading(); }, 1000);
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
    $.fn.dataTable.ext.type.order['date-dd-mm-yyyy-pre'] = function (date) {
        var parts = date.split('-');
        return new Date(parts[2], parts[1] - 1, parts[0]).getTime();
    };

    $(function () {
        $("#tabla").DataTable({
            "paging": true,
            "lengthChange": true,
            "order": [[0, 'desc']],
            "searching": true,
            "ordering": true,
            "info": true,
            "autoWidth": false,
            "pagingType": "full_numbers",
            "lengthMenu": [[500, -1], [500, "Todo"]],
            "language": {
                "sProcessing":     "Procesando...",
                "sLengthMenu":     "Mostrar _MENU_ registros",
                "sZeroRecords":    "No se encontraron resultados",
                "sEmptyTable":     "Ningún dato disponible en esta tabla",
                "sInfo":           "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
                "sInfoEmpty":      "Mostrando registros del 0 al 0 de un total de 0 registros",
                "sInfoFiltered":   "(filtrado de un total de _MAX_ registros)",
                "sSearch":         "Buscar:",
                "oPaginate": {
                    "sFirst":    "Primero",
                    "sLast":     "Último",
                    "sNext":     "Siguiente",
                    "sPrevious": "Anterior"
                }
            },
            "columnDefs": [
                { "targets": 0, "type": "date-dd-mm-yyyy" }
            ],
            "responsive": true
        });
    });
</script>
