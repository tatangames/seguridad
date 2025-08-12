@extends('backend.menus.superior')

@section('content-admin-css')
    <link href="{{ asset('css/adminlte.min.css') }}" type="text/css" rel="stylesheet" />
    <link href="{{ asset('css/dataTables.bootstrap4.css') }}" type="text/css" rel="stylesheet" />
    <link href="{{ asset('css/toastr.min.css') }}" type="text/css" rel="stylesheet" />
    <link href="{{ asset('css/select2.min.css') }}" type="text/css" rel="stylesheet">
    <link href="{{ asset('css/select2-bootstrap-5-theme.min.css') }}" type="text/css" rel="stylesheet">
@stop

<style>
    .dataTables_wrapper .dataTables_info {
        float: left !important;
        text-align: left;
    }

    .dataTables_wrapper .dataTables_paginate {
        float: left !important;
        text-align: left;
        padding-left: 10px;
    }
</style>

<div id="divcontenedor" style="display: none">

    <section class="content-header">
        <div class="row">
            <h1 style="margin-left: 5px">Materiales</h1>

                <button type="button" style="margin-left: 15px" onclick="modalAgregar()" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus-square"></i>
                    Registrar
                </button>

        </div>
    </section>

    <section class="content" style="margin-top: 15px">
        <div class="container-fluid">
            <div class="card card-gray-dark">
                <div class="card-header">
                    <h3 class="card-title">Listado de Materiales</h3>
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
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Nuevo Material</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="formulario-nuevo">
                        <div class="card-body">

                            <div class="form-group">
                                <label>Nombre del Material:</label>
                                <input type="text" class="form-control" autocomplete="off" onpaste="contarcaracteresIngreso();" onkeyup="contarcaracteresIngreso();" maxlength="300" id="nombre-nuevo">
                                <div id="res-caracter-nuevo" style="float: right">0/300</div>
                            </div>

                            <br>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Código (Opcional):</label>
                                    <input type="text" class="form-control" autocomplete="off" id="codigo-nuevo" maxlength="100">
                                </div>
                            </div>


                            <div class="col-md-5">
                                <div class="form-group">
                                    <label>Unidad de Medida:</label>
                                    <br>
                                    <select width="70%"  class="form-control" id="select-unidad-nuevo">
                                        <option value="" selected>Seleccione una opción</option>
                                        @foreach($arrayUnidades as $sel)
                                            <option value="{{ $sel->id }}">{{ $sel->nombre }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>


                            <div class="col-md-5">
                                <div class="form-group">
                                    <label>Marca:</label>
                                    <br>
                                    <select width="70%"  class="form-control" id="select-marca-nuevo">
                                        <option value="" selected>Seleccione una opción</option>
                                        @foreach($arrayMarcas as $sel)
                                            <option value="{{ $sel->id }}">{{ $sel->nombre }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-5">
                                <div class="form-group">
                                    <label>Normativa:</label>
                                    <br>
                                    <select width="70%"  class="form-control" id="select-normativa-nuevo">
                                        <option value="" selected>Seleccione una opción</option>
                                        @foreach($arrayNormativa as $sel)
                                            <option value="{{ $sel->id }}">{{ $sel->nombre }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>


                            <div class="col-md-5">
                                <div class="form-group">
                                    <label>Color (Opcional):</label>
                                    <br>
                                    <select width="70%"  class="form-control" id="select-color-nuevo">
                                        <option value="" selected>Seleccione una opción</option>
                                        @foreach($arrayColor as $sel)
                                            <option value="{{ $sel->id }}">{{ $sel->nombre }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>


                            <div class="col-md-5">
                                <div class="form-group">
                                    <label>Talla (Opcional):</label>
                                    <br>
                                    <select width="70%"  class="form-control" id="select-talla-nuevo">
                                        <option value="" selected>Seleccione una opción</option>
                                        @foreach($arrayTalla as $sel)
                                            <option value="{{ $sel->id }}">{{ $sel->nombre }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <br>

                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Otros (Opcional):</label>
                                    <input type="text" class="form-control" autocomplete="off" id="otros-nuevo" maxlength="500">
                                </div>
                            </div>


                        </div>
                    </form>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-primary" onclick="nuevo()">Guardar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- modal editar -->
    <div class="modal fade" id="modalEditar">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Editar Material</h4>
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
                                        <label>Nombre del Material:</label>
                                        <input type="text" class="form-control" autocomplete="off" onpaste="contarcaracteresEditar();" onkeyup="contarcaracteresEditar();" maxlength="300" id="nombre-editar" >
                                        <div id="res-caracter-editar" style="float: right">0/300</div>
                                    </div>

                                    <br>

                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Código:</label>
                                            <input type="text" class="form-control" autocomplete="off" id="codigo-editar" maxlength="12">
                                        </div>
                                    </div>

                                    <div class="col-md-5">
                                        <div class="form-group">
                                            <label>Unidad de Medida:</label>
                                            <br>
                                            <select style="width: 70%; height: 45px"  class="form-control" id="select-unidad-editar">
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-5">
                                        <div class="form-group">
                                            <label>Marca:</label>
                                            <br>
                                            <select style="width: 70%; height: 45px"  class="form-control" id="select-marca-editar">
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-5">
                                        <div class="form-group">
                                            <label>Normativa:</label>
                                            <br>
                                            <select style="width: 70%; height: 45px"  class="form-control" id="select-normativa-editar">
                                            </select>
                                        </div>
                                    </div>



                                    <div class="col-md-5">
                                        <div class="form-group">
                                            <label>Color (Opcional):</label>
                                            <br>
                                            <select style="width: 70%; height: 45px"  class="form-control" id="select-color-editar">
                                            </select>
                                        </div>
                                    </div>


                                    <div class="col-md-5">
                                        <div class="form-group">
                                            <label>Talla (Opcional):</label>
                                            <br>
                                            <select style="width: 70%; height: 45px"  class="form-control" id="select-talla-editar">
                                            </select>
                                        </div>
                                    </div>

                                    <br>

                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label>Otros (Opcional):</label>
                                            <input type="text" class="form-control" autocomplete="off" id="otros-editar" maxlength="500">
                                        </div>
                                    </div>


                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-primary" onclick="editar()">Actualizar</button>
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

            var ruta = "{{ URL::to('/admin/materiales/tabla/index') }}";
            $('#tablaDatatable').load(ruta);

            $('#select-unidad-nuevo').select2({
                theme: "bootstrap-5",
                "language": {
                    "noResults": function(){
                        return "Busqueda no encontrada";
                    }
                },
            });

            $('#select-marca-nuevo').select2({
                theme: "bootstrap-5",
                "language": {
                    "noResults": function(){
                        return "Busqueda no encontrada";
                    }
                },
            });

            $('#select-normativa-nuevo').select2({
                theme: "bootstrap-5",
                "language": {
                    "noResults": function(){
                        return "Busqueda no encontrada";
                    }
                },
            });

            $('#select-color-nuevo').select2({
                theme: "bootstrap-5",
                "language": {
                    "noResults": function(){
                        return "Busqueda no encontrada";
                    }
                },
            });

            $('#select-talla-nuevo').select2({
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

            $('#select-marca-editar').select2({
                theme: "bootstrap-5",
                "language": {
                    "noResults": function(){
                        return "Busqueda no encontrada";
                    }
                },
            });

            $('#select-normativa-editar').select2({
                theme: "bootstrap-5",
                "language": {
                    "noResults": function(){
                        return "Busqueda no encontrada";
                    }
                },
            });


            $('#select-color-editar').select2({
                theme: "bootstrap-5",
                "language": {
                    "noResults": function(){
                        return "Busqueda no encontrada";
                    }
                },
            });


            $('#select-talla-editar').select2({
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
            var ruta = "{{ url('/admin/materiales/tabla/index') }}";
            $('#tablaDatatable').load(ruta);
        }

        function modalAgregar(){
            document.getElementById("formulario-nuevo").reset();
            document.getElementById('res-caracter-nuevo').innerHTML = '0/300 ';


            $('#select-unidad-nuevo').prop('selectedIndex', 0).change();
            $('#select-marca-nuevo').prop('selectedIndex', 0).change();
            $('#select-normativa-nuevo').prop('selectedIndex', 0).change();
            $('#select-color-nuevo').prop('selectedIndex', 0).change();
            $('#select-talla-nuevo').prop('selectedIndex', 0).change();

            $('#modalAgregar').modal({backdrop: 'static', keyboard: false})
        }

        function nuevo(){

            var nombre = document.getElementById('nombre-nuevo').value;
            var codigo = document.getElementById('codigo-nuevo').value;
            var unidad = document.getElementById('select-unidad-nuevo').value;
            var marca = document.getElementById('select-marca-nuevo').value;
            var normativa = document.getElementById('select-normativa-nuevo').value;
            var color = document.getElementById('select-color-nuevo').value;
            var talla = document.getElementById('select-talla-nuevo').value;
            var otros = document.getElementById('otros-nuevo').value;

            if(nombre === ''){
                toastr.error('Nombre es requerido');
                return;
            }

            if(unidad === ''){
                toastr.error('Unidad Medida es requerido');
                return
            }

            if(marca === ''){
                toastr.error('Marca es requerido');
                return
            }

            if(normativa === ''){
                toastr.error('Normativa es requerido');
                return
            }

            // COLOR Y TALLA OPCIONAL

            openLoading();
            var formData = new FormData();
            formData.append('nombre', nombre);
            formData.append('codigo', codigo);
            formData.append('unidad', unidad);
            formData.append('marca', marca);
            formData.append('normativa', normativa);
            formData.append('color', color);
            formData.append('talla', talla);
            formData.append('otros', otros);

            axios.post(url+'/materiales/nuevo', formData, {
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

            axios.post(url+'/materiales/informacion',{
                'id': id
            })
                .then((response) => {
                    closeLoading();
                    if(response.data.success === 1){
                        $('#modalEditar').modal({backdrop: 'static', keyboard: false})

                        $('#id-editar').val(id);
                        $('#nombre-editar').val(response.data.material.nombre);
                        $('#codigo-editar').val(response.data.material.codigo);
                        $('#otros-editar').val(response.data.material.otros);

                        contarcaracteresEditar();

                        document.getElementById("select-unidad-editar").options.length = 0;
                        document.getElementById("select-marca-editar").options.length = 0;
                        document.getElementById("select-normativa-editar").options.length = 0;
                        document.getElementById("select-color-editar").options.length = 0;
                        document.getElementById("select-talla-editar").options.length = 0;

                        // unidad de medida
                        $.each(response.data.unidad, function( key, val ){
                            if(response.data.material.id_medida == val.id){
                                $('#select-unidad-editar').append('<option value="' +val.id +'" selected="selected">'+ val.nombre +'</option>');
                            }else{
                                $('#select-unidad-editar').append('<option value="' +val.id +'">'+ val.nombre +'</option>');
                            }
                        });

                        // marca
                        $.each(response.data.marca, function( key, val ){
                            if(response.data.material.id_marca == val.id){
                                $('#select-marca-editar').append('<option value="' +val.id +'" selected="selected">'+ val.nombre +'</option>');
                            }else{
                                $('#select-marca-editar').append('<option value="' +val.id +'">'+ val.nombre +'</option>');
                            }
                        });

                        // normativa
                        $.each(response.data.normativa, function( key, val ){
                            if(response.data.material.id_normativa == val.id){
                                $('#select-normativa-editar').append('<option value="' +val.id +'" selected="selected">'+ val.nombre +'</option>');
                            }else{
                                $('#select-normativa-editar').append('<option value="' +val.id +'">'+ val.nombre +'</option>');
                            }
                        });


                        $('#select-color-editar').append('<option value="">Seleccionar opción</option>');

                        // color
                        $.each(response.data.color, function( key, val ){
                            if(response.data.material.id_color == val.id){
                                $('#select-color-editar').append('<option value="' +val.id +'" selected="selected">'+ val.nombre +'</option>');
                            }else{
                                $('#select-color-editar').append('<option value="' +val.id +'">'+ val.nombre +'</option>');
                            }
                        });


                        $('#select-talla-editar').append('<option value="">Seleccionar opción</option>');

                        // talla
                        $.each(response.data.talla, function( key, val ){
                            if(response.data.material.id_talla == val.id){
                                $('#select-talla-editar').append('<option value="' +val.id +'" selected="selected">'+ val.nombre +'</option>');
                            }else{
                                $('#select-talla-editar').append('<option value="' +val.id +'">'+ val.nombre +'</option>');
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

        function editar(){

            var id = document.getElementById('id-editar').value;
            var nombre = document.getElementById('nombre-editar').value;
            var codigo = document.getElementById('codigo-editar').value;
            var unidad = document.getElementById('select-unidad-editar').value;
            var marca = document.getElementById('select-marca-editar').value;
            var normativa = document.getElementById('select-normativa-editar').value;
            var color = document.getElementById('select-color-editar').value;
            var talla = document.getElementById('select-talla-editar').value;
            var otros = document.getElementById('otros-editar').value;

            if(nombre === ''){
                toastr.error('Nombre es requerido');
                return;
            }

            if(unidad === ''){
                toastr.error('Unidad Medida es requerido');
                return
            }

            if(marca === ''){
                toastr.error('Marca es requerido');
                return
            }

            if(normativa === ''){
                toastr.error('Normativa es requerido');
                return
            }

            // COLOR Y TALLA ES OPCIONAL

            openLoading();
            var formData = new FormData();
            formData.append('id', id);
            formData.append('nombre', nombre);
            formData.append('codigo', codigo);
            formData.append('unidad', unidad);
            formData.append('marca', marca);
            formData.append('normativa', normativa);
            formData.append('color', color);
            formData.append('talla', talla);
            formData.append('otros', otros);

            axios.post(url+'/materiales/editar', formData, {
            })
                .then((response) => {
                    closeLoading();
                    if(response.data.success === 1){
                        toastr.success('Actualizado correctamente');
                        $('#modalEditar').modal('hide');
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

        function contarcaracteresIngreso(){
            setTimeout(function(){
                var valor = document.getElementById('nombre-nuevo');
                var cantidad = valor.value.length;
                document.getElementById('res-caracter-nuevo').innerHTML = cantidad + '/300 ';
            },10);
        }

        function contarcaracteresEditar(){
            setTimeout(function(){
                var valor = document.getElementById('nombre-editar');
                var cantidad = valor.value.length;
                document.getElementById('res-caracter-editar').innerHTML = cantidad + '/300 ';
            },10);
        }

        // mostrara que materiales quedan aun
        function infoDetalle(id){
            window.location.href="{{ url('/admin/material/detalle') }}/" + id;
        }

    </script>


@endsection
