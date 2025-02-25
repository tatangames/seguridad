@extends('backend.menus.superior')

@section('content-admin-css')
    <link href="{{ asset('css/adminlte.min.css') }}" type="text/css" rel="stylesheet" />
    <link href="{{ asset('css/dataTables.bootstrap4.css') }}" type="text/css" rel="stylesheet" />
    <link href="{{ asset('css/toastr.min.css') }}" type="text/css" rel="stylesheet" />
    <link href="{{ asset('css/select2.min.css') }}" type="text/css" rel="stylesheet">
    <link href="{{ asset('css/select2-bootstrap-5-theme.min.css') }}" type="text/css" rel="stylesheet">
    <link href="{{ asset('css/buttons_estilo.css') }}" rel="stylesheet">
@stop

<style>
    table{
        /*Ajustar tablas*/
        table-layout:fixed;
    }

    .cursor-pointer:hover {
        cursor: pointer;
        color: #401fd2;
        font-weight: bold;
    }

    *:focus {
        outline: none;
    }
</style>

<div id="divcontenedor" style="display: none">


    <section class="content" style="margin-top: 10px">
        <div class="container-fluid">
            <div class="row">

                <div class="col-md-12">

                    <div class="card card-gray-dark">
                        <div class="card-header">
                            <h3 class="card-title">Información de Ingreso</h3>
                        </div>

                        <div class="card-body">
                            <div class="row">
                                <div class="form-group col-md-2">
                                    <label>Libro *:</label>
                                    <select id="select-libro" class="form-control" onchange="buscarCorrelativo()">
                                        <option value="0">Seleccionar Libro</option>
                                        @foreach($arrayLibros as $item)
                                            <option value="{{$item->id}}">{{ $item->nombre }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group col-md-2">
                                    <label>Número de Nicho:</label>
                                    <input type="text" disabled class="form-control" autocomplete="off" id="correlativo">
                                </div>
                            </div>


                            <div class="row">

                                <div class="form-group col-md-4">
                                    <label>Nombre Fallecido *:</label>
                                    <input type="text" maxlength="100" class="form-control" autocomplete="off" id="nombre-fallecido">
                                </div>

                                <div class="form-group col-md-2">
                                    <label>Fecha Fallecimiento *:</label>
                                    <input type="date" class="form-control" autocomplete="off" id="fecha-fallecido">
                                </div>

                                <div class="form-group col-md-2">
                                    <label>Fecha Vencimiento:</label>
                                    <input type="date" class="form-control" disabled autocomplete="off" id="fecha-vencimiento">
                                </div>

                                <div class="form-group col-md-2">
                                    <label>Fecha Exhumación:</label>
                                    <input type="date" class="form-control" autocomplete="off" id="fecha-exhumacion">
                                </div>

                            </div>


                            <br>
                            <hr>

                            <h4><strong>Datos del Contribuyente</strong></h4>

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
                                    <label>Fecha Ingreso (Tesoreria):</label>
                                    <input type="date" class="form-control" autocomplete="off" id="fechatesoreria-contribuyente">
                                </div>
                            </div>
                        </div>

                    </div>

                </div>



            </div>
            <div style="float: right; margin: 30px">
                <button type="button"
                        style="font-weight: bold; background-color: #28a745; color: white !important;"
                        class="button button-rounded button-pill button-small"
                        onclick="guardarRegistro()">Registrar Datos
                </button>
            </div>
        </div>
    </section>

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
            document.getElementById("divcontenedor").style.display = "block";

        });
    </script>

    <script>


        document.getElementById("fecha-fallecido").addEventListener("change", function() {
            let fechaFallecido = new Date(this.value);

            if (!isNaN(fechaFallecido.getTime())) {
                let fechaVencimiento = new Date(fechaFallecido);
                fechaVencimiento.setFullYear(fechaVencimiento.getFullYear() + 7);

                let fechaMora = new Date(fechaVencimiento);
                fechaMora.setFullYear(fechaMora.getFullYear() + 7);

                document.getElementById("fecha-vencimiento").value = fechaMora.toISOString().split('T')[0];
            }
        });

        document.getElementById("fecha-fallecido").addEventListener("change", function () {
            actualizarFechaVencimiento();
        });

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


        function actualizarFechaVencimiento(periodo = null) {
            let fechaFallecidoInput = document.getElementById("fecha-fallecido").value;
            let fechaVencimientoInput = document.getElementById("fecha-vencimiento");

            let fechaFallecido = new Date(fechaFallecidoInput);

            if (!isNaN(fechaFallecido.getTime())) {
                periodo = periodo || parseInt(document.getElementById("periodo-contribuyente").value) || 1;

                // Cálculo de los años a sumar: 14 si periodo es 1, caso contrario 14 + (periodo - 1) * 7
                let añosASumar = (periodo === 1) ? 14 : 14 + ((periodo - 1) * 7);

                let fechaVencimiento = new Date(fechaFallecido);
                fechaVencimiento.setFullYear(fechaVencimiento.getFullYear() + añosASumar);

                fechaVencimientoInput.value = fechaVencimiento.toISOString().split('T')[0];
            } else {
                fechaVencimientoInput.value = ""; // Borra la fecha si la fecha de fallecimiento no es válida
            }
        }


        function buscarCorrelativo(){

            var id = document.getElementById('select-libro').value;

            if(id == '0'){
                $('#correlativo').val('');
            }else{
                openLoading();
                var formData = new FormData();
                formData.append('id', id);

                axios.post(url+'/registro/buscar/correlativo', formData, {
                })
                .then((response) => {
                    closeLoading();
                    if(response.data.success === 1){
                        $('#correlativo').val(response.data.correlativo);
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
        }


        function guardarRegistro(){

            var idlibro = document.getElementById('select-libro').value;
            var nombreFallecido = document.getElementById('nombre-fallecido').value;
            var fechaFallecido = document.getElementById('fecha-fallecido').value;
            var fechaExhumacion = document.getElementById('fecha-exhumacion').value; //null

            // Datos contribuyente
            var nombreContribuyente = document.getElementById('nombre-contribuyente').value;
            var duiContribuyente = document.getElementById('dui-contribuyente').value; //null
            var telefonoContribuyente = document.getElementById('telefono-contribuyente').value; //null
            var direccionContribuyente = document.getElementById('direccion-contribuyente').value; //null
            var periodoContribuyente = document.getElementById('periodo-contribuyente').value;
            var reciboTesoreriaContribuyente = document.getElementById('recibo-contribuyente').value; //null
            var fechaTesoreriaContribuyente = document.getElementById('fechatesoreria-contribuyente').value; //null


            if(idlibro === ''){
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
            if(periodoContribuyente === ''){
                toastr.error('Periodo Contribuyente es requerido');
                return;
            }


            openLoading();
            var formData = new FormData();
            formData.append('idlibro', idlibro);
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

            axios.post(url+'/registro/nuevo', formData, {
            })
                .then((response) => {
                    closeLoading();
                    if(response.data.success === 1){
                        completado()
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

        function completado(){
            Swal.fire({
                title: 'Registrado',
                text: "",
                icon: 'success',
                showCancelButton: false,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#d33',
                cancelButtonText: 'Cancelar',
                confirmButtonText: 'Aceptar',
                closeOnClickOutside: false,
            }).then((result) => {
                if (result.isConfirmed) {
                    location.reload();
                }
            })
        }


    </script>


@endsection
