<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <table id="tabla" class="table table-bordered table-striped">
                            <thead>
                            <tr>
                                <th style="width: 20%">Material</th>
                                <th style="width: 11%">Medida</th>
                                <th style="width: 11%">Marca</th>
                                <th style="width: 11%">Normativa</th>
                                <th style="width: 11%">Color</th>
                                <th style="width: 11%">Talla</th>
                                <th style="width: 8%">Cantidad Ingresada</th>
                                <th style="width: 10%">Precio U.</th>
                                <th style="width: 6%">Opción</th>
                            </tr>
                            </thead>
                            <tbody>

                            @foreach($listado as $dato)
                                <tr>
                                    <td>{{ $dato->nombre }}</td>
                                    <td>{{ $dato->nombreUnidad }}</td>
                                    <td>{{ $dato->marca }}</td>
                                    <td>{{ $dato->normativa }}</td>
                                    <td>{{ $dato->color }}</td>
                                    <td>{{ $dato->talla }}</td>
                                    <td>{{ $dato->cantidad }}</td>
                                    <td>{{ $dato->precioFormat }}</td>
                                    <td>
                                        <button style="margin: 3px" type="button" class="btn btn-danger btn-xs"
                                                onclick="infoBorrar({{ $dato->id }})">
                                            <i class="fas fa-trash" title="Borrar"></i>&nbsp; Borrar
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
</section>

<script>
    $(function () {
        $("#tabla").DataTable({
            "paging": true,
            "lengthChange": true,
            "order": [[0, 'asc']],
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
                "sInfoPostFix": "",
                "sSearch": "Buscar:",
                "sUrl": "",
                "sInfoThousands": ",",
                "sLoadingRecords": "Cargando...",
                "oPaginate": {
                    "sFirst": "Primero",
                    "sLast": "Último",
                    "sNext": "Siguiente",
                    "sPrevious": "Anterior"
                },
                "oAria": {
                    "sSortAscending": ": Activar para ordenar la columna de manera ascendente",
                    "sSortDescending": ": Activar para ordenar la columna de manera descendente"
                }

            },
            "responsive": true, "lengthChange": true, "autoWidth": false,
        });
    });


</script>
