@extends('backend.menus.superior')

@section('content-admin-css')
    <link href="{{ asset('css/adminlte.min.css') }}" type="text/css" rel="stylesheet" />
    <link href="{{ asset('css/dataTables.bootstrap4.css') }}" type="text/css" rel="stylesheet" />
    <link href="{{ asset('css/toastr.min.css') }}" type="text/css" rel="stylesheet" />
    <link href="{{ asset('css/buttons_estilo.css') }}" rel="stylesheet">
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
                    <h3 class="card-title">Listado de Libros</h3>
                </div>
                <div class="card-body">

                    <div class="row d-flex align-items-center">
                        <div class="form-group col-md-3">
                            <label style="color: #686868">Libros</label>
                            <div>
                                <select id="select-libro" class="form-control">
                                    @foreach($arrayLibros as $item)
                                        <option value="{{$item->id}}">{{$item->nombre}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-auto">
                            <button type="button" onclick="buscarListado()" class="btn btn-success btn-sm">
                                <i class="fas fa-search"></i>
                                Buscar
                            </button>
                        </div>
                    </div>

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


    <div class="modal fade" id="modalBotones">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Información</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="formulario-botones">
                        <div class="card-body">

                            <div class="form-group">
                                <input type="hidden" id="id-detalle">
                            </div>

                            <div class="form-group col-md-12">
                                <label>Nombre Fallecido</label><br>
                                <input type="text" disabled class="form-control"  id="nombre-fa">
                            </div>

                            <div style="display: flex; flex-direction: column; align-items: center;">
                                <div class="form-group">
                                    <button type="button" class="btn btn-primary btn-xs" onclick="infoEditar()">
                                        <i class="fas fa-edit" title="Editar"></i>&nbsp; Editar
                                    </button>
                                </div>

                                <div class="form-group">
                                    <button type="button" class="btn btn-warning btn-xs" onclick="infoCobros()">
                                        <i class="fas fa-edit" title="Cobros"></i>&nbsp; Cobros
                                    </button>
                                </div>

                                <div class="form-group">
                                    <button type="button" class="btn btn-danger btn-xs" onclick="infoBorrar()">
                                        <i class="fas fa-trash" title="Borrar"></i>&nbsp; Borrar
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>



    <div class="modal fade" id="modalEditar">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Editar</h4>
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
                                        <label>Nombre Fallecido *:</label>
                                        <input type="text" maxlength="100" class="form-control" autocomplete="off" id="nombre-fallecido">
                                    </div>

                                    <div class="form-group col-md-4">
                                        <label>Fecha Fallecimiento *:</label>
                                        <input type="date" class="form-control" autocomplete="off" id="fecha-fallecido">
                                    </div>

                                    <div class="form-group col-md-4">
                                        <label>Fecha Exhumación:</label>
                                        <input type="date" class="form-control" autocomplete="off" id="fecha-exhumacion">
                                    </div>



                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-primary" onclick="editarCampos()">Actualizar</button>
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

    <script type="text/javascript">
        $(document).ready(function(){

            var id = @json($primerId); // idproyecto

            if (id != null) {
                openLoading()
                var ruta = "{{ URL::to('/admin/librosdetalle/tabla') }}/" + id;
                $('#tablaDatatable').load(ruta);
            }

            document.getElementById("divcontenedor").style.display = "block";
        });
    </script>

    <script>

        function buscarListado(){
            var idlibro = document.getElementById('select-libro').value;

            if(idlibro === ''){
                toastr.error('Libro es requerido');
                return;
            }
            openLoading()
            var ruta = "{{ URL::to('/admin/librosdetalle/tabla') }}/" + idlibro;
            $('#tablaDatatable').load(ruta);
        }

        function recargar(){
            var id = document.getElementById('select-libro').value;

            var ruta = "{{ URL::to('/admin/librosdetalle/tabla') }}/" + id;
            $('#tablaDatatable').load(ruta);
        }


        function mostrarAyuda(){

            let mensaje = `Cuando se hace el Primer Registro para calcular fecha Vencimiento se toma Fecha de Fallecimiento,
                Cuando se hace un Cobro se toma en cuenta Fecha Recibo (Tesoreria).
                Ya no se harán cálculos cuando haya una fecha de exhumación`;

            Swal.fire({
                title: 'Información',
                text: mensaje,
                icon: 'info',
                showCancelButton: false,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#d33',
                cancelButtonText: 'Cancelar',
                confirmButtonText: 'Aceptar'
            }).then((result) => {
                if (result.isConfirmed) {

                }
            })
        }

        function infoBorrar(){
            var nombre = document.getElementById('nombre-fa').value;
            var id = document.getElementById('id-detalle').value;

            Swal.fire({
                title: 'Borrar Registro',
                text: "Se eliminara todos los registros para: " + nombre,
                icon: 'info',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#d33',
                cancelButtonText: 'Cancelar',
                confirmButtonText: 'Eliminar'
            }).then((result) => {
                if (result.isConfirmed) {
                    eliminarCompleto(id)
                }
            })
        }

        function eliminarCompleto(id){
            openLoading();
            var formData = new FormData();
            formData.append('id', id);

            axios.post(url+'/librosdetalle/borrar/fallecido', formData, {
            })
                .then((response) => {
                    closeLoading();
                    if(response.data.success === 1){
                        toastr.success('Borrado correctamente');
                        $('#modalBotones').modal('hide');
                        recargar();
                    }
                    else {
                        toastr.error('Error al borrar');
                    }
                })
                .catch((error) => {
                    toastr.error('Error al borrar');
                    closeLoading();
                });
        }


        function vistaDetalle(id){
            // nicho_municipal_detalle

            document.getElementById("formulario-botones").reset();

            openLoading();
            var formData = new FormData();
            formData.append('id', id);

            axios.post(url+'/librosdetalle/info/fallecido', formData, {
            })
                .then((response) => {
                    closeLoading();
                    if(response.data.success === 1){
                        $('#modalBotones').modal('show');

                        $('#id-detalle').val(id);
                        $('#nombre-fa').val(response.data.info.nombre);


                    }
                    else {
                        toastr.error('Error al buscar');
                    }
                })
                .catch((error) => {
                    toastr.error('Error al buscar');
                    closeLoading();
                });
        }


        function infoEditar(){

            var id = document.getElementById('id-detalle').value;

            openLoading();
            var formData = new FormData();
            formData.append('id', id);

            axios.post(url+'/librosdetalle/info/fallecido', formData, {
            })
                .then((response) => {
                    closeLoading();
                    if(response.data.success === 1){

                        $('#id-editar').val(id);
                        $('#nombre-fallecido').val(response.data.info.nombre);
                        $('#fecha-fallecido').val(response.data.info.fecha_fallecimiento);
                        $('#fecha-exhumacion').val(response.data.info.fecha_exhumacion);

                        $('#modalEditar').modal('show');
                    }
                    else {
                        toastr.error('Error al buscar');
                    }
                })
                .catch((error) => {
                    toastr.error('Error al buscar');
                    closeLoading();
                });
        }



        function editarCampos(){

            var id = document.getElementById('id-editar').value;
            var nombreFallecido = document.getElementById('nombre-fallecido').value;
            var fechaFallecido = document.getElementById('fecha-fallecido').value;
            var fechaExhumacion = document.getElementById('fecha-exhumacion').value; //null


            if(id === ''){
                toastr.error('Libro es requerido');
                return;
            }
            if(nombreFallecido === ''){
                toastr.error('Nombre Fallecido es requerido');
                return;
            }
            if(fechaFallecido === ''){
                toastr.error('Fecha Fallecido es requerido');
                return;
            }

            openLoading();
            var formData = new FormData();
            formData.append('id', id);
            formData.append('nombreFallecido', nombreFallecido);
            formData.append('fechaFallecido', fechaFallecido);
            formData.append('fechaExhumacion', fechaExhumacion);

            axios.post(url+'/librosdetalle/actualizar/datos', formData, {
            })
                .then((response) => {
                    closeLoading();
                    if(response.data.success === 1){

                        toastr.success('Actualizado');
                        $('#modalEditar').modal('hide');
                        $('#modalBotones').modal('hide');
                        recargar()
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
