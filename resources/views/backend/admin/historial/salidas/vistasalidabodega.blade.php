@extends('backend.menus.superior')

@section('content-admin-css')
    <link href="{{ asset('css/adminlte.min.css') }}" type="text/css" rel="stylesheet" />
    <link href="{{ asset('css/dataTables.bootstrap4.css') }}" type="text/css" rel="stylesheet" />
    <link href="{{ asset('css/toastr.min.css') }}" type="text/css" rel="stylesheet" />
    <link href="{{ asset('css/buttons_estilo.css') }}" rel="stylesheet">
    <link href="{{ asset('css/select2.min.css') }}" type="text/css" rel="stylesheet">
    <link href="{{ asset('css/select2-bootstrap-5-theme.min.css') }}" type="text/css" rel="stylesheet">
@stop

<style>
    table{
        /*Ajustar tablas*/
        table-layout:fixed;
    }
</style>

<div id="divcontenedor" style="display: none">

    <section class="content" style="margin-top: 15px">
        <div class="container-fluid">
            <div class="card card-gray-dark">
                <div class="card-header">
                    <h3 class="card-title">Listado de Salidas</h3>
                </div>
                <div class="card-body">

                    <div class="row">
                        <div class="col-md-12">
                            <div id="tablaDatatable">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>



    <!-- modal editar -->
    <div class="modal fade" id="modalEditar">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Editar Empleado</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="formulario-editar">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12">

                                    <div class="form-group">
                                        <input type="hidden" id="id-editar">
                                    </div>

                                    <div class="form-group">
                                        <label>Fecha</label>
                                        <input type="date" class="form-control" id="fecha-editar" autocomplete="off">
                                    </div>

                                    <div class="form-group">
                                        <label>Descripción</label>
                                        <input type="text" maxlength="800" class="form-control" id="descripcion-editar" autocomplete="off">
                                    </div>

                                    <div class="form-group">
                                        <label>Distrito:</label>
                                        <br>
                                        <select width="100%" class="form-control" id="select-distrito-editar" onchange="buscarUnidadEdicion(this)">
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label>Unidad:</label>
                                        <br>
                                        <select width="100%" class="form-control" id="select-unidad-editar" onchange="buscarEmpleadosEdicion(this)">
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label>Empleados:</label>
                                        <br>
                                        <select width="100%" class="form-control" id="select-empleados-editar">
                                        </select>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                    <button type="button" style="font-weight: bold; background-color: #28a745; color: white !important;" class="button button-rounded button-pill button-small" onclick="editar()">Actualizar</button>
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
        $(document).ready(function(){

            openLoading()
            var ruta = "{{ URL::to('/admin/historial/salidas/tabla') }}";
            $('#tablaDatatable').load(ruta);


            $('#select-unidad-editar').select2({
                theme: "bootstrap-5",
                "language": {
                    "noResults": function(){
                        return "Busqueda no encontrada";
                    }
                },
            });

            $('#select-distrito-editar').select2({
                theme: "bootstrap-5",
                "language": {
                    "noResults": function(){
                        return "Busqueda no encontrada";
                    }
                },
            });

            $('#select-empleados-editar').select2({
                theme: "bootstrap-5",
                "language": {
                    "noResults": function(){
                        return "Busqueda no encontrada";
                    }
                },
            });

            document.getElementById("divcontenedor").style.display = "block";
        });
    </script>

    <script>

        function recargar(){
            var ruta = "{{ URL::to('/admin/historial/salidas/tabla') }}";
            $('#tablaDatatable').load(ruta);
        }

        function vistaDetalle(idsalida){
            window.location.href="{{ url('/admin/historial/salidadetalle/index') }}/" + idsalida;
        }

        function informacion(id){
            openLoading();
            document.getElementById("formulario-editar").reset();

            axios.post(url+'/historial/salidas/informacion',{
                'id': id
            })
                .then((response) => {
                    closeLoading();
                    if(response.data.success === 1){

                        $('#modalEditar').modal('show');
                        $('#id-editar').val(id);
                        $('#fecha-editar').val(response.data.info.fecha);
                        $('#descripcion-editar').val(response.data.info.descripcion);

                        document.getElementById("select-distrito-editar").options.length = 0;
                        document.getElementById("select-unidad-editar").options.length = 0;

                        $.each(response.data.arrayDistrito, function( key, val ){
                            if(response.data.infoUniEmpleado.id_distrito == val.id){
                                $('#select-distrito-editar').append('<option value="' +val.id +'" selected="selected">'+ val.nombre +'</option>');
                            }else{
                                $('#select-distrito-editar').append('<option value="' +val.id +'">'+ val.nombre +'</option>');
                            }
                        });

                        $.each(response.data.arrayUnidad, function( key, val ){
                            if(response.data.info.id_unidad_empleado == val.id){
                                $('#select-unidad-editar').append('<option value="' +val.id +'" selected="selected">'+ val.nombre +'</option>');
                            }else{
                                $('#select-unidad-editar').append('<option value="' +val.id +'">'+ val.nombre +'</option>');
                            }
                        });

                        $.each(response.data.arrayEmpleados, function( key, val ){
                            if(response.data.info.id_empleado == val.id){
                                $('#select-empleados-editar').append('<option value="' +val.id +'" selected="selected">'+ val.nombre +'</option>');
                            }else{
                                $('#select-empleados-editar').append('<option value="' +val.id +'">'+ val.nombre +'</option>');
                            }
                        });

                    }else{
                        toastr.error('Información no encontrada');
                    }
                })
                .catch((error) => {
                    closeLoading();
                    toastr.error('Información no encontrada');
                });
        }

        // al cambiar distrito
        function buscarUnidadEdicion(){
            let id = document.getElementById('select-distrito-editar').value;

            if(id == '0'){
                return;
            }

            openLoading();

            axios.post(url+'/empleados/buscarunidad',{
                'id': id
            })
                .then((response) => {
                    closeLoading();
                    if(response.data.success === 1){

                        document.getElementById("select-unidad-editar").options.length = 0;
                        document.getElementById("select-empleados-editar").options.length = 0;

                        $('#select-unidad-editar').append('<option value="0" selected disabled>Seleccionar opción</option>');

                        $.each(response.data.arrayUnidad, function( key, val ){
                            $('#select-unidad-editar').append('<option value="' +val.id +'">'+ val.nombre +'</option>');
                        });



                    }else{
                        toastr.error('Información no encontrada');
                    }
                })
                .catch((error) => {
                    closeLoading();
                    toastr.error('Información no encontrada');
                });
        }


        function buscarEmpleadosEdicion(){

            let id = document.getElementById('select-unidad-editar').value;

            openLoading();

            axios.post(url+'/empleados/buscarunidad-empleado',{
                'id': id
            })
                .then((response) => {
                    closeLoading();
                    if(response.data.success === 1){

                        document.getElementById("select-empleados-editar").options.length = 0;
                        $('#select-empleados-editar').append('<option value="0" selected disabled>Seleccionar opción</option>');

                        $.each(response.data.arrayEmpleados, function( key, val ){
                            $('#select-empleados-editar').append('<option value="' +val.id +'">'+ val.nombre +'</option>');
                        });

                    }else{
                        toastr.error('Información no encontrada');
                    }
                })
                .catch((error) => {
                    closeLoading();
                    toastr.error('Información no encontrada');
                });

        }

        function editar(){

            var id = document.getElementById('id-editar').value;
            var fecha = document.getElementById('fecha-editar').value;
            var descripcion = document.getElementById('descripcion-editar').value;
            var empleado = document.getElementById('select-empleados-editar').value;

            if(fecha === ''){
                toastr.error('Fecha es requerido');
                return;
            }

            if(empleado === ''){
                toastr.error('Empleado es requerido');
                return;
            }

            openLoading();
            var formData = new FormData();
            formData.append('id', id);
            formData.append('fecha', fecha);
            formData.append('descripcion', descripcion);
            formData.append('empleado', empleado);

            axios.post(url+'/historial/salidas/editar', formData, {
            })
                .then((response) => {
                    closeLoading();

                    if(response.data.success === 1){
                        toastr.success('Actualizado correctamente');
                        $('#modalEditar').modal('hide');
                        recargar();
                    }
                    else {
                        toastr.error('Error al actualizar');
                    }

                })
                .catch((error) => {
                    toastr.error('Error al actualizar');
                    closeLoading();
                });
        }




    </script>


@endsection
