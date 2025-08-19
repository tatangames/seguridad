

<section class="content">
    <div class="container-fluid">
        <div class="row">

            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <table id="tabla" class="table table-bordered table-striped">
                            <thead>
                            <tr>
                                <th style="width: 4%">Fecha Salida</th>
                                <th style="width: 10%">Distrito</th>
                                <th style="width: 10%">Unidad</th>
                                <th style="width: 15%">Empleado</th>
                                <th style="width: 20%">Descripción</th>
                                <th style="width: 6%">Opciones</th>
                            </tr>
                            </thead>
                            <tbody>

                            @foreach($listado as $dato)
                                <tr>
                                    <td>{{ $dato->fechaFormat }}</td>
                                    <td>{{ $dato->distrito }}</td>
                                    <td>{{ $dato->unidad }}</td>
                                    <td>{{ $dato->empleado }}</td>
                                    <td>{{ $dato->descripcion }}</td>

                                    <td>
                                        <button type="button" class="btn btn-success btn-xs"
                                                onclick="informacion({{ $dato->id }})">
                                            <i class="fas fa-edit" title="Editar"></i>&nbsp; Editar
                                        </button>

                                        <button type="button" style="margin: 5px" class="btn btn-info btn-xs"
                                                onclick="vistaDetalle({{ $dato->id }})">
                                            <i class="fas fa-eye" title="Detalle"></i>&nbsp; Detalle
                                        </button>

                                        <button type="button" style="margin: 5px" class="btn btn-info btn-xs"
                                                onclick="reportePDF({{ $dato->id }})">
                                            <i class="fas fa-file-pdf" title="PDF"></i>&nbsp; PDF
                                        </button>

                                    </td>
                                </tr>
                            @endforeach

                            <script>
                                setTimeout(function () {
                                    closeLoading();
                                }, 500);
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
    $(function () {
        $.fn.dataTable.ext.type.order['date-dd-mm-yyyy-pre'] = function (date) {
            var parts = date.split('-'); // Dividimos por guiones
            return new Date(parts[2], parts[1] - 1, parts[0]).getTime(); // Convertimos a timestamp
        };

        $(function () {
            $("#tabla").DataTable({
                "paging": true,
                "lengthChange": true,
                "order": [[0, 'desc']], // Orden descendente por fecha
                "searching": true,
                "ordering": true,
                "info": true,
                "autoWidth": false,
                "pagingType": "full_numbers",
                "lengthMenu": [[500, -1], [500, "Todo"]],
                "language": {
                    "sProcessing": "Procesando...",
                    "sLengthMenu": "Mostrar _MENU_ registros",
                    "sZeroRecords": "No se encontraron resultados",
                    "sEmptyTable": "Ningún dato disponible en esta tabla",
                    "sInfo": "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
                    "sInfoEmpty": "Mostrando registros del 0 al 0 de un total de 0 registros",
                    "sInfoFiltered": "(filtrado de un total de _MAX_ registros)",
                    "sSearch": "Buscar:",
                    "oPaginate": {
                        "sFirst": "Primero",
                        "sLast": "Último",
                        "sNext": "Siguiente",
                        "sPrevious": "Anterior"
                    }
                },
                "columnDefs": [
                    {
                        "targets": 0,
                        "type": "date-dd-mm-yyyy" // Usamos el tipo de fecha personalizado
                    }
                ],
                "responsive": true,
                "lengthChange": true,
                "autoWidth": false,
            });
        });
    });


</script>
