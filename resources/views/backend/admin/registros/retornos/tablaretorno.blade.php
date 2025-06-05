<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div style="overflow-x: auto; width: 100%">
                            <table id="tabla" class="table table-bordered table-striped">
                            <thead>
                            <tr>
                                <th  style="width: 6%">Fecha Salida</th>
                                <th  style="width: 6%">Lote</th>
                                <th  style="width: 6%">Distrito</th>
                                <th  style="width: 6%">Recibio</th>
                                <th  style="width: 15%">Material</th>
                                <th  style="width: 6%">U/M</th>

                                <th  style="width: 8%">Marca</th>
                                <th  style="width: 8%">Normativa</th>
                                <th  style="width: 8%">Color</th>
                                <th  style="width: 8%">Talla</th>
                                <th  style="width: 8%">Otros</th>

                                <th  style="width: 10%">Cantidad Retornar</th>
                                <th  style="width: 8%">Opciones</th>
                            </tr>
                            </thead>
                            <tbody>

                            @foreach($lista as $dato)
                                <tr>
                                    <td>{{ $dato->fechaSalida }}</td>
                                    <td>{{ $dato->lote }}</td>
                                    <td>{{ $dato->nombreDistrito }}</td>
                                    <td>{{ $dato->nombreEncargado }}</td>
                                    <td>{{ $dato->nombreMaterial }}</td>
                                    <td>{{ $dato->nombreMedida }}</td>

                                    <td>{{ $dato->nombreMarca }}</td>
                                    <td>{{ $dato->nombreNormativa }}</td>
                                    <td>{{ $dato->nombreColor }}</td>
                                    <td>{{ $dato->nombreTalla }}</td>

                                    <td>{{ $dato->nombreOtros }}</td>

                                    <td>{{ $dato->cantidad_salida }}</td>
                                    <td>
                                        <button type="button" style="font-weight: bold; color: white !important;"
                                                class="btn btn-primary btn-xs" onclick="informacionRetorno({{ $dato->id }})">
                                            <i class="fas fa-edit" title="Retorno"></i>&nbsp; Retorno
                                        </button>

                                        <button type="button" style="font-weight: bold; color: white !important;"
                                                class="btn btn-danger btn-xs" onclick="informacionDescartar({{ $dato->id }})">
                                            <i class="fas fa-edit" title="Descartar"></i>&nbsp; Descartar
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
