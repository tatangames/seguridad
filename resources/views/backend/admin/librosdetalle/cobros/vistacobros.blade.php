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

    <section class="content-header">
        <div class="row mb-2">
            <div class="col-sm-6">
                Fallecido: {{ $infoNichoMuniDeta->nombre }}
            </div>
        </div>
        <button type="button" style="margin-left: 15px" onclick="modalAgregar()" class="btn btn-primary btn-sm">
            <i class="fas fa-plus-square"></i>
            Nuevo Registro
        </button>
    </section>

    <section class="content">
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


    <div class="modal fade" id="modalAgregar">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Registro</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="formulario-nuevo">
                        <div class="card-body">


                            <div class="row">

                                <div class="form-group col-md-4">
                                    <label>Contribuyente que Cancela:</label>
                                    <input type="text" maxlength="100" class="form-control" autocomplete="off" id="nombre-contribuyente">
                                </div>

                                <div class="form-group col-md-2">
                                    <label>DUI:</label>
                                    <input type="text" maxlength="9" class="form-control" autocomplete="off" id="dui-contribuyente">
                                </div>

                                <div class="form-group col-md-2">
                                    <label>Teléfono:</label>
                                    <input type="text" maxlength="9" class="form-control" autocomplete="off" id="telefono-contribuyente">
                                </div>

                                <div class="form-group col-md-4">
                                    <label>Dirección:</label>
                                    <input type="text" maxlength="300" class="form-control" autocomplete="off" id="direccion-contribuyente">
                                </div>
                            </div>

                            <div class="row">

                                <div class="form-group col-md-2">
                                    <label>Periodo a Cancelar *:</label>
                                    <input type="number" value="1" class="form-control" autocomplete="off" id="periodo-contribuyente" min="1" max="10">
                                </div>

                                <div class="form-group col-md-2">
                                    <label>Costo sin 5%:</label>
                                    <input type="text" value="20.00" disabled class="form-control" autocomplete="off" id="costono5-contribuyente">
                                </div>

                                <div class="form-group col-md-2">
                                    <label>Costo con 5%:</label>
                                    <input type="text" value="21.00" disabled class="form-control" autocomplete="off" id="costo5-contribuyente">
                                </div>

                                <div class="form-group col-md-3">
                                    <label>Recibo Ingreso (Tesoreria):</label>
                                    <input type="text" maxlength="50" class="form-control" autocomplete="off" id="recibo-contribuyente">
                                </div>

                                <div class="form-group col-md-3">
                                    <label>Fecha Ingreso (Tesoreria): *</label>
                                    <input type="date" class="form-control" autocomplete="off" id="fechatesoreria-contribuyente">
                                </div>
                            </div>


                        </div>
                    </form>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-primary" onclick="nuevoRegistro()">Guardar</button>
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
            var ruta = "{{ URL::to('/admin/cobros/tabla') }}/" + id;
            $('#tablaDatatable').load(ruta);

            document.getElementById("divcontenedor").style.display = "block";
        });
    </script>

    <script>

        function recargar(){
            let id = {{ $id }};
            var ruta = "{{ URL::to('/admin/cobros/tabla') }}/" + id;
            $('#tablaDatatable').load(ruta);
        }

        function infoBorrar(id){
            Swal.fire({
                title: 'Borrar Registro',
                text: "",
                icon: 'info',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#d33',
                cancelButtonText: 'Cancelar',
                confirmButtonText: 'Eliminar'
            }).then((result) => {
                if (result.isConfirmed) {
                    eliminar(id)
                }
            })
        }

        function eliminar(id){
            openLoading();
            var formData = new FormData();
            formData.append('id', id);

            axios.post(url+'/cobros/borrar', formData, {
            })
                .then((response) => {
                    closeLoading();
                    if(response.data.success === 1){
                        toastr.error('Se requiere mínimo 1 Registro');
                    }
                    else if(response.data.success === 2){
                        toastr.success('Borrado correctamente');
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



        function modalAgregar(){
            document.getElementById("formulario-nuevo").reset();
            $('#modalAgregar').modal('show');
        }


        document.getElementById("periodo-contribuyente").addEventListener("input", function() {
            let periodo = parseInt(this.value) || 1; // Evitar valores no numéricos
            let costoBase = 20;

            // Validaciones: evitar negativos, ceros, letras y limitar a 10
            if (periodo < 1) periodo = 1;
            if (periodo > 10) periodo = 10;

            this.value = periodo; // Asegurar que el input tenga solo valores válidos

            let costoSin5 = periodo * costoBase;
            let costoCon5 = costoSin5 * 1.05;

            document.getElementById("costono5-contribuyente").value = costoSin5.toFixed(2);
            document.getElementById("costo5-contribuyente").value = costoCon5.toFixed(2);

            actualizarFechaVencimiento(periodo);
        });


        function nuevoRegistro(){

            var id = {{ $id }};

            // Datos contribuyente
            var nombreContribuyente = document.getElementById('nombre-contribuyente').value;
            var duiContribuyente = document.getElementById('dui-contribuyente').value; //null
            var telefonoContribuyente = document.getElementById('telefono-contribuyente').value; //null
            var direccionContribuyente = document.getElementById('direccion-contribuyente').value; //null
            var periodoContribuyente = document.getElementById('periodo-contribuyente').value;
            var reciboTesoreriaContribuyente = document.getElementById('recibo-contribuyente').value; //null
            var fechaTesoreriaContribuyente = document.getElementById('fechatesoreria-contribuyente').value; //null

            if(periodoContribuyente === ''){
                toastr.error('Periodo Contribuyente es requerido');
                return;
            }

            if(fechaTesoreriaContribuyente === ''){
                toastr.error('Fecha Ingreso es requerido');
                return;
            }


            openLoading();
            var formData = new FormData();
            formData.append('id', id);
            formData.append('nombreContribuyente', nombreContribuyente);
            formData.append('duiContribuyente', duiContribuyente);
            formData.append('telefonoContribuyente', telefonoContribuyente);
            formData.append('direccionContribuyente', direccionContribuyente);
            formData.append('periodoContribuyente', periodoContribuyente);
            formData.append('reciboTesoreriaContribuyente', reciboTesoreriaContribuyente);
            formData.append('fechaTesoreriaContribuyente', fechaTesoreriaContribuyente);

            axios.post(url+'/cobros/nuevoRegistro', formData, {
            })
                .then((response) => {
                    closeLoading();
                    if(response.data.success === 1){
                        $('#modalAgregar').modal('hide');
                        toastr.success('Registrado');
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
