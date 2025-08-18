@extends('backend.menus.superior')

@section('content-admin-css')
    <link href="{{ asset('css/adminlte.min.css') }}" type="text/css" rel="stylesheet" />
    <link href="{{ asset('css/dataTables.bootstrap4.css') }}" type="text/css" rel="stylesheet" />
    <link href="{{ asset('css/toastr.min.css') }}" type="text/css" rel="stylesheet" />
    <link href="{{ asset('css/buttons_estilo.css') }}" rel="stylesheet">
    <link href="{{ asset('css/select2.min.css') }}" type="text/css" rel="stylesheet">
    <link href="{{ asset('css/select2-bootstrap-5-theme.min.css') }}" type="text/css" rel="stylesheet">
    <link href="{{ asset('css/estiloToggle.css') }}" type="text/css" rel="stylesheet" />
@stop

<style>
    table{
        /*Ajustar tablas*/
        table-layout:fixed;
    }
</style>

<div id="divcontenedor" style="display: none">

    <section class="content-header">
        <div class="row mb-2">
            <div class="col-sm-6">
                <button type="button" style="font-weight: bold; background-color: #2156af; color: white !important;" onclick="modalAgregar()"
                        class="button button-3d button-rounded button-pill button-small">
                    <i class="fas fa-pencil-alt"></i>
                    Nuevo Empleado
                </button>
            </div>

            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item">Empleados</li>
                    <li class="breadcrumb-item active">Listado de Empleados</li>
                </ol>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="card card-blue">
                <div class="card-header">
                    <h3 class="card-title">Listado</h3>
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

    <div class="modal fade" id="modalAgregar">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Nuevo Empleado</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="formulario-nuevo">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12">

                                    <div class="form-group">
                                        <label>Distrito:</label>
                                        <br>
                                        <select width="100%" class="form-control" id="select-distrito" onchange="buscarUnidad(this)">
                                            <option value="0">Seleccionar opción</option>
                                            @foreach($arrayDistrito as $sel)
                                                <option value="{{ $sel->id }}">{{ $sel->nombre }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label>Unidad:</label>
                                        <br>
                                        <select width="100%"  class="form-control" id="select-unidad">
                                        </select>
                                    </div>


                                    <div class="form-group">
                                        <label>Empleado</label>
                                        <input type="text" maxlength="100" class="form-control" id="nombre-nuevo" autocomplete="off">
                                    </div>


                                    <div class="form-group">
                                        <label>DUI</label>
                                        <input type="text" maxlength="50" class="form-control" id="dui-nuevo" autocomplete="off">
                                    </div>


                                    <div class="form-group">
                                        <label>Cargo:</label>
                                        <br>
                                        <select width="100%"  class="form-control" id="select-cargo">
                                            @foreach($arrayCargo as $sel)
                                                <option value="{{ $sel->id }}">{{ $sel->nombre }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label>ES JEFE?</label><br>
                                        <label class="switch" style="margin-top:10px">
                                            <input type="checkbox" id="check-jefe">
                                            <div class="slider round">
                                                <span class="on">SI</span>
                                                <span class="off">NO</span>
                                            </div>
                                        </label>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                    <button type="button" style="font-weight: bold; background-color: #2156af; color: white !important;" class="button button-rounded button-pill button-small" onclick="nuevo()">Guardar</button>
                </div>
            </div>
        </div>
    </div>

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
                                        <label>Distrito:</label>
                                        <br>
                                        <select width="100%" class="form-control" id="select-distrito-editar" onchange="buscarUnidadEdicion(this)">
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label>Unidad:</label>
                                        <br>
                                        <select width="100%" class="form-control" id="select-unidad-editar">
                                        </select>
                                    </div>


                                    <div class="form-group">
                                        <label>Empleado</label>
                                        <input type="text" maxlength="100" class="form-control" id="nombre-editar" autocomplete="off">
                                    </div>

                                    <div class="form-group">
                                        <label>DUI</label>
                                        <input type="text" maxlength="50" class="form-control" id="dui-editar" autocomplete="off">
                                    </div>

                                    <div class="form-group">
                                        <label>Cargo:</label>
                                        <br>
                                        <select width="100%" class="form-control" id="select-cargo-editar">
                                        </select>
                                    </div>


                                    <div class="form-group">
                                        <label>ES JEFE?</label><br>
                                        <label class="switch" style="margin-top:10px">
                                            <input type="checkbox" id="check-jefe-editar">
                                            <div class="slider round">
                                                <span class="on">SI</span>
                                                <span class="off">NO</span>
                                            </div>
                                        </label>
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
            var ruta = "{{ URL::to('/admin/empleados/tabla') }}";
            $('#tablaDatatable').load(ruta);

            $('#select-distrito').select2({
                theme: "bootstrap-5",
                "language": {
                    "noResults": function(){
                        return "Busqueda no encontrada";
                    }
                },
            });

            $('#select-cargo').select2({
                theme: "bootstrap-5",
                "language": {
                    "noResults": function(){
                        return "Busqueda no encontrada";
                    }
                },
            });

            $('#select-unidad').select2({
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

            $('#select-cargo-editar').select2({
                theme: "bootstrap-5",
                "language": {
                    "noResults": function(){
                        return "Busqueda no encontrada";
                    }
                },
            });

            $('#select-unidad-editar').select2({
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
            var ruta = "{{ url('/admin/empleados/tabla') }}";
            $('#tablaDatatable').load(ruta);
        }

        function buscarUnidad(){
            let id = document.getElementById('select-distrito').value;

            if(id == '0'){
                document.getElementById("select-unidad").options.length = 0;
                return false;
            }

            openLoading();

            axios.post(url+'/empleados/buscarunidad',{
                'id': id
            })
                .then((response) => {
                    closeLoading();
                    if(response.data.success === 1){

                        document.getElementById("select-unidad").options.length = 0;

                        $.each(response.data.arrayUnidad, function( key, val ){
                            $('#select-unidad').append('<option value="' +val.id +'">'+ val.nombre +'</option>');
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

        function buscarUnidadEdicion(){
            let id = document.getElementById('select-distrito-editar').value;

            openLoading();

            axios.post(url+'/empleados/buscarunidad',{
                'id': id
            })
                .then((response) => {
                    closeLoading();
                    if(response.data.success === 1){

                        document.getElementById("select-unidad-editar").options.length = 0;

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

        function modalAgregar(){
            document.getElementById("formulario-nuevo").reset();

            document.getElementById("select-distrito").selectedIndex = 0;
            $('#select-distrito').val($('#select-distrito option:first').val()).trigger('change');
            $('#modalAgregar').modal('show');
        }

        function nuevo(){

            var unidad = document.getElementById('select-unidad').value;
            var cargo = document.getElementById('select-cargo').value;
            var nombre = document.getElementById('nombre-nuevo').value;
            var dui = document.getElementById('dui-nuevo').value;

            var checkboxJefe = document.getElementById('check-jefe');
            var valorCheckboxJefe = checkboxJefe.checked ? 1 : 0;

            if(unidad === ''){
                toastr.error('Unidad es requerido');
                return;
            }

            if(cargo === ''){
                toastr.error('Cargo es requerido');
                return;
            }

            if(nombre === ''){
                toastr.error('Nombre es requerido');
                return;
            }

            openLoading();
            var formData = new FormData();
            formData.append('nombre', nombre);
            formData.append('unidad', unidad);
            formData.append('cargo', cargo);
            formData.append('dui', dui);
            formData.append('jefe', valorCheckboxJefe);

            axios.post(url+'/empleados/nuevo', formData, {
            })
                .then((response) => {
                    closeLoading();
                    if(response.data.success === 1){
                        toastr.success('Registrado correctamente');
                        $('#modalAgregar').modal('hide');
                        recargar();
                    }
                    else {
                        toastr.error('Error al registrar');
                    }
                })
                .catch((error) => {
                    toastr.error('Error al registrar');
                    closeLoading();
                });
        }

        function informacion(id){
            openLoading();
            document.getElementById("formulario-editar").reset();

            axios.post(url+'/empleados/informacion',{
                'id': id
            })
                .then((response) => {
                    closeLoading();
                    if(response.data.success === 1){
                        $('#modalEditar').modal('show');
                        $('#id-editar').val(id);

                        document.getElementById("select-distrito-editar").options.length = 0;
                        document.getElementById("select-unidad-editar").options.length = 0;
                        document.getElementById("select-cargo-editar").options.length = 0;

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

                        $.each(response.data.arrayCargo, function( key, val ){
                            if(response.data.info.id_cargo == val.id){
                                $('#select-cargo-editar').append('<option value="' +val.id +'" selected="selected">'+ val.nombre +'</option>');
                            }else{
                                $('#select-cargo-editar').append('<option value="' +val.id +'">'+ val.nombre +'</option>');
                            }
                        });

                        $('#nombre-editar').val(response.data.info.nombre);
                        $('#dui-editar').val(response.data.info.dui);

                        if(response.data.info.jefe === 0){
                            $("#check-jefe-editar").prop("checked", false);
                        }else{
                            $("#check-jefe-editar").prop("checked", true);
                        }

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
            var nombre = document.getElementById('nombre-editar').value;
            var unidad = document.getElementById('select-unidad-editar').value;
            var cargo = document.getElementById('select-cargo-editar').value;
            var dui = document.getElementById('dui-editar').value;

            var checkboxJefe = document.getElementById('check-jefe-editar');
            var valorCheckboxJefe = checkboxJefe.checked ? 1 : 0;

            if(nombre === ''){
                toastr.error('Nombre es requerido');
                return;
            }

            if(unidad === ''){
                toastr.error('Unidad es requerido');
                return;
            }

            openLoading();
            var formData = new FormData();
            formData.append('id', id);
            formData.append('nombre', nombre);
            formData.append('unidad', unidad);
            formData.append('cargo', cargo);
            formData.append('dui', dui);
            formData.append('jefe', valorCheckboxJefe);

            axios.post(url+'/empleados/editar', formData, {
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
