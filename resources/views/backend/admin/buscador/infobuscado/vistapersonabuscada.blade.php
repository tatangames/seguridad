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

    <section class="content" style="margin-top: 8px">
        <div class="container-fluid">
            <div class="card card-gray-dark">
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
                                    <button type="button" class="btn btn-success btn-xs" onclick="infoNuevoFallecido()">
                                        <i class="fas fa-plus" title="Agregar Fallecido"></i>&nbsp; Agregar Fallecido
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




    <div class="modal fade" id="modalNuevoRegistro">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Nuevo Registro</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="formulario-nuevoregistro">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12">

                                    <div class="form-group">
                                        <input type="hidden" id="id-nuevoregistro">
                                    </div>

                                    <div class="card-body">

                                        <div class="row">

                                            <div class="form-group col-md-4">
                                                <label>Nombre Fallecido *:</label>
                                                <input type="text" maxlength="100" class="form-control" autocomplete="off" id="nombre-fallecido-r">
                                            </div>

                                            <div class="form-group col-md-2">
                                                <label>Fecha Fallecimiento *:</label>
                                                <input type="date" class="form-control" autocomplete="off" id="fecha-fallecido-r">
                                            </div>

                                            <div class="form-group col-md-2">
                                                <label>Fecha Vencimiento:</label>
                                                <input type="date" class="form-control" disabled autocomplete="off" id="fecha-vencimiento-r">
                                            </div>

                                            <div class="form-group col-md-2">
                                                <label>Fecha Exhumación:</label>
                                                <input type="date" class="form-control" autocomplete="off" id="fecha-exhumacion-r">
                                            </div>

                                        </div>


                                        <br>
                                        <hr>

                                        <h4><strong>Datos del Contribuyente</strong></h4>

                                        <div class="row">

                                            <div class="form-group col-md-4">
                                                <label>Contribuyente que Cancela:</label>
                                                <input type="text" maxlength="100" class="form-control" autocomplete="off" id="nombre-contribuyente-r">
                                            </div>

                                            <div class="form-group col-md-2">
                                                <label>DUI:</label>
                                                <input type="text" maxlength="9" class="form-control" autocomplete="off" id="dui-contribuyente-r">
                                            </div>

                                            <div class="form-group col-md-2">
                                                <label>Teléfono:</label>
                                                <input type="text" maxlength="9" class="form-control" autocomplete="off" id="telefono-contribuyente-r">
                                            </div>

                                            <div class="form-group col-md-4">
                                                <label>Dirección:</label>
                                                <input type="text" maxlength="300" class="form-control" autocomplete="off" id="direccion-contribuyente-r">
                                            </div>
                                        </div>

                                        <div class="row">

                                            <div class="form-group col-md-2">
                                                <label>Periodo a Cancelar *:</label>
                                                <input type="number" value="1" class="form-control" autocomplete="off" id="periodo-contribuyente-r" min="1" max="10">
                                            </div>

                                            <div class="form-group col-md-2">
                                                <label>Costo sin 5%:</label>
                                                <input type="text" value="20.00" disabled class="form-control" autocomplete="off" id="costono5-contribuyente-r">
                                            </div>

                                            <div class="form-group col-md-2">
                                                <label>Costo con 5%:</label>
                                                <input type="text" value="21.00" disabled class="form-control" autocomplete="off" id="costo5-contribuyente-r">
                                            </div>

                                            <div class="form-group col-md-3">
                                                <label>Recibo Ingreso (Tesoreria):</label>
                                                <input type="text" maxlength="50" class="form-control" autocomplete="off" id="recibo-contribuyente-r">
                                            </div>

                                            <div class="form-group col-md-3">
                                                <label>Fecha Ingreso (Tesoreria):</label>
                                                <input type="date" class="form-control" autocomplete="off" id="fechatesoreria-contribuyente-r">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-primary" onclick="nuevoExtraFallecido()">Agregar</button>
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

            let id = {{ $id }};
            var ruta = "{{ URL::to('/admin/buscador/persona/tabla') }}/" + id;
            $('#tablaDatatable').load(ruta);

            document.getElementById("divcontenedor").style.display = "block";
        });
    </script>

    <script>



        function recargar(){
            let id = {{ $id }};
            var ruta = "{{ URL::to('/admin/buscador/persona/tabla') }}/" + id;
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

                        modalBorrado()
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

        function modalBorrado(){
            Swal.fire({
                title: 'Registro Borrado',
                text: "",
                icon: 'success',
                showCancelButton: false,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#d33',
                cancelButtonText: 'Cancelar',
                confirmButtonText: 'Aceptar',
                allowOutsideClick: false,
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href="{{ url('/admin/buscador/index') }}";
                }
            })
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

        function infoCobros(){
            var id = document.getElementById('id-detalle').value;
            window.location.href="{{ url('/admin/cobros/index') }}/" + id;
        }


        function infoNuevoFallecido(){
            document.getElementById("formulario-nuevoregistro").reset();
            $('#modalNuevoRegistro').modal('show');
        }


        document.getElementById("fecha-fallecido-r").addEventListener("change", function() {
            let fechaFallecido = new Date(this.value);

            if (!isNaN(fechaFallecido.getTime())) {
                let fechaVencimiento = new Date(fechaFallecido);
                fechaVencimiento.setFullYear(fechaVencimiento.getFullYear() + 7);

                let fechaMora = new Date(fechaVencimiento);
                fechaMora.setFullYear(fechaMora.getFullYear() + 7);

                document.getElementById("fecha-vencimiento-r").value = fechaMora.toISOString().split('T')[0];
            }
        });

        document.getElementById("fecha-fallecido-r").addEventListener("change", function () {
            actualizarFechaVencimiento();
        });

        document.getElementById("periodo-contribuyente-r").addEventListener("input", function() {
            let periodo = parseInt(this.value) || 1; // Evitar valores no numéricos
            let costoBase = 20;

            // Validaciones: evitar negativos, ceros, letras y limitar a 10
            if (periodo < 1) periodo = 1;
            if (periodo > 10) periodo = 10;

            this.value = periodo; // Asegurar que el input tenga solo valores válidos

            let costoSin5 = periodo * costoBase;
            let costoCon5 = costoSin5 * 1.05;

            document.getElementById("costono5-contribuyente-r").value = costoSin5.toFixed(2);
            document.getElementById("costo5-contribuyente-r").value = costoCon5.toFixed(2);

            actualizarFechaVencimiento(periodo);
        });


        function actualizarFechaVencimiento(periodo = null) {
            let fechaFallecidoInput = document.getElementById("fecha-fallecido-r").value;
            let fechaVencimientoInput = document.getElementById("fecha-vencimiento-r");

            let fechaFallecido = new Date(fechaFallecidoInput);

            if (!isNaN(fechaFallecido.getTime())) {
                periodo = periodo || parseInt(document.getElementById("periodo-contribuyente-r").value) || 1;

                // Cálculo de los años a sumar: 14 si periodo es 1, caso contrario 14 + (periodo - 1) * 7
                let añosASumar = (periodo === 1) ? 14 : 14 + ((periodo - 1) * 7);

                let fechaVencimiento = new Date(fechaFallecido);
                fechaVencimiento.setFullYear(fechaVencimiento.getFullYear() + añosASumar);

                fechaVencimientoInput.value = fechaVencimiento.toISOString().split('T')[0];
            } else {
                fechaVencimientoInput.value = ""; // Borra la fecha si la fecha de fallecimiento no es válida
            }
        }


        function nuevoExtraFallecido(){

            // nicho_municipal_detalle
            var id = document.getElementById('id-detalle').value;
            var nombreFallecido = document.getElementById('nombre-fallecido-r').value;
            var fechaFallecido = document.getElementById('fecha-fallecido-r').value;
            var fechaExhumacion = document.getElementById('fecha-exhumacion-r').value; //null

            // Datos contribuyente
            var nombreContribuyente = document.getElementById('nombre-contribuyente-r').value;
            var duiContribuyente = document.getElementById('dui-contribuyente-r').value; //null
            var telefonoContribuyente = document.getElementById('telefono-contribuyente-r').value; //null
            var direccionContribuyente = document.getElementById('direccion-contribuyente-r').value; //null
            var periodoContribuyente = document.getElementById('periodo-contribuyente-r').value;
            var reciboTesoreriaContribuyente = document.getElementById('recibo-contribuyente-r').value; //null
            var fechaTesoreriaContribuyente = document.getElementById('fechatesoreria-contribuyente-r').value; //null

            if(id === ''){
                toastr.error('ID es requerido');
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
            if(periodoContribuyente === ''){
                toastr.error('Periodo Contribuyente es requerido');
                return;
            }

            openLoading();
            var formData = new FormData();
            formData.append('id', id);
            formData.append('nombreFallecido', nombreFallecido);
            formData.append('fechaFallecido', fechaFallecido);
            formData.append('fechaExhumacion', fechaExhumacion);

            formData.append('nombreContribuyente', nombreContribuyente);
            formData.append('duiContribuyente', duiContribuyente);
            formData.append('telefonoContribuyente', telefonoContribuyente);
            formData.append('direccionContribuyente', direccionContribuyente);
            formData.append('periodoContribuyente', periodoContribuyente);
            formData.append('reciboTesoreriaContribuyente', reciboTesoreriaContribuyente);
            formData.append('fechaTesoreriaContribuyente', fechaTesoreriaContribuyente);

            axios.post(url+'/librosdetalle/extra/fallecido', formData, {
            })
                .then((response) => {
                    closeLoading();
                    if(response.data.success === 1){
                        toastr.success('Registrado');
                        $('#modalNuevoRegistro').modal('hide');
                        $('#modalBotones').modal('hide');
                        recargar()
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



    </script>

@endsection
