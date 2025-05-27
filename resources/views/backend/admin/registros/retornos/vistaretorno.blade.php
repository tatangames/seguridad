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

    <section class="content-header">
        <div class="row mb-2">
            <div class="col-sm-6">

            </div>

            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item">Retorno</li>
                    <li class="breadcrumb-item active">Listado de Retornos</li>
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
                    <h4 class="modal-title">Retorno de Material</h4>
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
                                        <input type="hidden" id="id-editar">
                                    </div>

                                    <div class="form-group col-md-4">
                                        <label>Cantidad que puede Retornar</label>
                                        <input type="text" disabled class="form-control" id="cantidadFuera" autocomplete="off">
                                    </div>

                                    <div class="form-group col-md-3">
                                        <label>Fecha</label>
                                        <input type="date" class="form-control" id="fecha-nuevo" autocomplete="off">
                                    </div>

                                    <div class="form-group col-md-3">
                                        <label>Cantidad Retorno</label>
                                        <input type="number" min="1"  class="form-control" id="cantidadRetorno-nuevo" autocomplete="off">
                                    </div>


                                    <div class="form-group col-md-5">
                                        <label>Persona que Entrega:</label>
                                        <select id="select-encargado" class="form-control">
                                            @foreach($arrayEncargado as $item)
                                                <option value="{{$item->id}}">{{ $item->nombre }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <br>

                                    <div class="form-group">
                                        <label>Descripción (opcional)</label>
                                        <input type="text" maxlength="800" class="form-control" id="descripcion-nuevo" autocomplete="off">
                                    </div>

                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                    <button type="button" style="font-weight: bold; background-color: #2156af; color: white !important;" class="button button-rounded button-pill button-small" onclick="registrarRetorno()">Guardar</button>
                </div>
            </div>
        </div>
    </div>




    <div class="modal fade" id="modalDescartar">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Descartar Material</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="formulario-descartar">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12">

                                    <div class="form-group">
                                        <input type="hidden" id="id-editar-descarte">
                                    </div>

                                    <div class="form-group col-md-4">
                                        <label>Cantidad que puede Descartar</label>
                                        <input type="text" disabled class="form-control" id="cantidadFueraDescartar" autocomplete="off">
                                    </div>

                                    <div class="form-group col-md-3">
                                        <label>Fecha</label>
                                        <input type="date" class="form-control" id="fechadescarte-nuevo" autocomplete="off">
                                    </div>

                                    <div class="form-group col-md-3">
                                        <label>Cantidad Descartar</label>
                                        <input type="number" min="1"  class="form-control" id="cantidadDescartar-nuevo" autocomplete="off">
                                    </div>

                                    <br>

                                    <div class="form-group">
                                        <label>Descripción</label>
                                        <input type="text" maxlength="800" class="form-control" id="descripcion-descartar" autocomplete="off">
                                    </div>

                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                    <button type="button" style="font-weight: bold; background-color: #2156af; color: white !important;" class="button button-rounded button-pill button-small" onclick="registrarRetornoDescarte()">Guardar</button>
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
            var ruta = "{{ URL::to('/admin/retornos/tabla') }}";
            $('#tablaDatatable').load(ruta);

            document.getElementById("divcontenedor").style.display = "block";


            $('#select-encargado').select2({
                theme: "bootstrap-5",
                "language": {
                    "noResults": function(){
                        return "Búsqueda no encontrada";
                    }
                },
            });

        });
    </script>

    <script>

        function recargar(){
            var ruta = "{{ url('/admin/retorno/tabla') }}";
            $('#tablaDatatable').load(ruta);
        }

        function modalAgregar(){
            document.getElementById("formulario-nuevo").reset();
            $('#modalAgregar').modal('show');
        }

        function informacionRetorno(id){
            openLoading();
            document.getElementById("formulario-nuevo").reset();

            axios.post(url+'/retornos/informacion',{
                'id': id
            })
                .then((response) => {
                    closeLoading();
                    if(response.data.success === 1){
                        $('#modalAgregar').modal('show');
                        $('#id-editar').val(id);

                        const cantidadSalida = response.data.info.cantidad_salida;
                        $('#cantidadFuera').val(cantidadSalida);

                        // Establece el valor máximo permitido dinámicamente
                        const inputCantidadRetorno = document.getElementById('cantidadRetorno-nuevo');
                        inputCantidadRetorno.max = cantidadSalida;

                        // Llama a la función para validar el valor actual (en caso de que ya tenga algo)
                        validateCantidadSalida(inputCantidadRetorno, cantidadSalida);

                        // También puedes vincularlo al evento input, por si el usuario edita manualmente
                        inputCantidadRetorno.addEventListener('input', function () {
                            validateCantidadSalida(this, cantidadSalida);
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

        function registrarRetorno(){
            var id = document.getElementById('id-editar').value;
            var cantidadRetorno = document.getElementById('cantidadRetorno-nuevo').value;
            var fecha = document.getElementById('fecha-nuevo').value;
            var selectEncargado = document.getElementById('select-encargado').value;
            var descripcion = document.getElementById('descripcion-nuevo').value;

            var reglaNumeroEntero = /^[0-9]\d*$/;

            if(cantidadRetorno === ''){
                toastr.error('Cantidad retorno es requerido');
                return;
            }

            if(!cantidadRetorno.match(reglaNumeroEntero)) {
                toastr.error('Cantidad retorno debe ser número entero y no Negativo');
                return;
            }

            if(cantidadRetorno <= 0){
                toastr.error('Cantidad retorno no debe ser negativo o cero');
                return;
            }

            if(cantidadRetorno > 1000000){
                toastr.error('Cantidad retorno máximo 1 millón');
                return;
            }

            if(fecha === ''){
                toastr.error('Fecha es requerido');
                return;
            }

            if(selectEncargado === ''){
                toastr.error('Quien entrega es requerido');
                return;
            }

            openLoading();
            var formData = new FormData();
            formData.append('id', id);
            formData.append('retorno', cantidadRetorno);
            formData.append('fecha', fecha);
            formData.append('encargado', selectEncargado);
            formData.append('descripcion', descripcion);

            axios.post(url+'/retornos/registrar', formData, {
            })
                .then((response) => {
                    closeLoading();

                    if(response.data.success === 1){
                        toastr.success('Registrado correctamente');
                        $('#modalAgregar').modal('hide');
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





        function informacionDescartar(id){
            openLoading();
            document.getElementById("formulario-descartar").reset();

            axios.post(url+'/retornos/informacion',{
                'id': id
            })
                .then((response) => {
                    closeLoading();
                    if(response.data.success === 1){
                        $('#modalDescartar').modal('show');
                        $('#id-editar-descarte').val(id);

                        const cantidadSalida = response.data.info.cantidad_salida;
                        $('#cantidadFueraDescartar').val(cantidadSalida);

                        // Establece el valor máximo permitido dinámicamente
                        const inputCantidadRetorno = document.getElementById('cantidadDescartar-nuevo');
                        inputCantidadRetorno.max = cantidadSalida;

                        // Llama a la función para validar el valor actual (en caso de que ya tenga algo)
                        validateCantidadSalida(inputCantidadRetorno, cantidadSalida);

                        // También puedes vincularlo al evento input, por si el usuario edita manualmente
                        inputCantidadRetorno.addEventListener('input', function () {
                            validateCantidadSalida(this, cantidadSalida);
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




        function registrarRetornoDescarte(){
            var id = document.getElementById('id-editar-descarte').value;
            var cantidadDescarto = document.getElementById('cantidadDescartar-nuevo').value;
            var fecha = document.getElementById('fechadescarte-nuevo').value;
            var descripcion = document.getElementById('descripcion-descartar').value;

            var reglaNumeroEntero = /^[0-9]\d*$/;

            if(cantidadDescarto === ''){
                toastr.error('Cantidad descarto es requerido');
                return;
            }

            if(!cantidadDescarto.match(reglaNumeroEntero)) {
                toastr.error('Cantidad descarto debe ser número entero y no Negativo');
                return;
            }

            if(cantidadDescarto <= 0){
                toastr.error('Cantidad descarto no debe ser negativo o cero');
                return;
            }

            if(cantidadDescarto > 1000000){
                toastr.error('Cantidad descarto máximo 1 millón');
                return;
            }

            if(fecha === ''){
                toastr.error('Fecha es requerido');
                return;
            }

            if(descripcion === ''){
                toastr.error('Descripción es requerido');
                return;
            }

            openLoading();
            var formData = new FormData();
            formData.append('id', id);
            formData.append('descarto', cantidadDescarto);
            formData.append('fecha', fecha);
            formData.append('descripcion', descripcion);

            axios.post(url+'/retornos/descartar/registrar', formData, {
            })
                .then((response) => {
                    closeLoading();

                    if(response.data.success === 1){
                        toastr.success('Descartado correctamente');
                        $('#modalDescartar').modal('hide');
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


        function validateCantidadSalida(input, maxCantidad) {
            // Remueve caracteres no numéricos
            input.value = input.value.replace(/[^0-9]/g, '');

            // Convierte el valor a número y verifica el límite
            if (Number(input.value) > maxCantidad) {
                input.value = maxCantidad; // Restringe el valor al máximo permitido
            }
        }


    </script>


@endsection
