@extends('backend.menus.superior')

@section('content-admin-css')
    <link href="{{ asset('css/adminlte.min.css') }}" type="text/css" rel="stylesheet"/>
    <link href="{{ asset('css/dataTables.bootstrap4.css') }}" type="text/css" rel="stylesheet"/>
    <link href="{{ asset('css/toastr.min.css') }}" type="text/css" rel="stylesheet"/>
    <link href="{{ asset('css/buttons_estilo.css') }}" rel="stylesheet">
    <link href="{{ asset('css/select2.min.css') }}" type="text/css" rel="stylesheet">
    <link href="{{ asset('css/select2-bootstrap-5-theme.min.css') }}" type="text/css" rel="stylesheet">
@stop

<style>
    table {
        /*Ajustar tablas*/
        table-layout: fixed;
    }

    .checkbox-label {
        display: flex;
        align-items: center;
        font-size: 16px; /* Tamaño de texto más pequeño */
        text-align: left; /* Alineación del texto a la izquierda */
    }

    .checkbox {
        margin: 3; /* Elimina el margen para pegar el checkbox al texto */
        width: 15px; /* Tamaño pequeño para el checkbox */
        height: 15px; /* Ajusta la altura del checkbox */
        margin-right: 3px; /* Pega el checkbox al texto */
    }
</style>

<div id="divcontenedor" style="display: none">


    <section class="content" style="margin-top: 35px; margin-bottom: 60px">
        <div class="container-fluid">
            <div class="card card-gray-dark">
                <div class="card-header">
                    <h3 class="card-title">REPORTES</h3>
                </div>

                <div class="card-body">
                    <section class="content" style="margin-left: 30px">
                        <div class="container-fluid">

                            <p style="font-weight: bold">TODAS LAS ENTREGAS AL EMPLEADO</p>


                            <div class="form-group col-md-6">
                                <label>Distrito:</label>
                                <br>
                                <select width="100%" class="form-control" id="select-distrito" onchange="buscarUnidad(this)">
                                    <option value="0" selected disabled>Seleccionar opción</option>
                                    @foreach($arrayDistritos as $sel)
                                        <option value="{{ $sel->id }}">{{ $sel->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>


                            <div class="form-group col-md-6">
                                <label>Unidad:</label>
                                <br>
                                <select width="100%"  class="form-control" id="select-unidad" onchange="buscarEmpleado(this)">
                                </select>
                            </div>



                                <div class="form-group col-md-6">
                                    <label>Empleado:</label>
                                    <br>
                                    <select class="form-control" id="select-empleado" style="width:100%;">
                                    </select>
                                </div>


                        <br><br>

                            <button type="button" onclick="pdfEncargado()" class="btn" style="margin-left: 15px; border-color: black; border-radius: 0.1px;">
                                <img src="{{ asset('images/logopdf.png') }}" width="48px" height="55px">
                                Generar PDF
                            </button>


                        </div>
                    </section>
                </div>

            </div>
        </div>
    </section>

    <section class="content" style="margin-top: 35px; margin-bottom: 60px">
        <div class="container-fluid">
            <div class="card card-gray-dark">
                <div class="card-header">
                    <h3 class="card-title">REPORTES</h3>
                </div>

                <div class="card-body">
                    <section class="content" style="margin-left: 30px">
                        <div class="container-fluid">

                            <p style="font-weight: bold">KARDEX DE MATERIALES</p>

                            <div class="form-group col-md-6">
                                <label>Material: (Medida) (Marca) (Normativa) (Color) (Talla)</label>
                                <br>
                                <select width="100%" class="form-control" id="select-material">
                                    @foreach($arrayMateriales as $sel)
                                        <option value="{{ $sel->id }}">{{ $sel->nombreCompleto }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <br><br>

                            <button type="button" onclick="pdfKardexMaterial()" class="btn" style="margin-left: 15px; border-color: black; border-radius: 0.1px;">
                                <img src="{{ asset('images/logopdf.png') }}" width="48px" height="55px">
                                Generar PDF
                            </button>


                        </div>
                    </section>
                </div>

            </div>
        </div>
    </section>


</div>


@extends('backend.menus.footerjs')
@section('archivos-js')

    <script src="{{ asset('js/toastr.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/axios.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/sweetalert2.all.min.js') }}"></script>
    <script src="{{ asset('js/alertaPersonalizada.js') }}"></script>
    <script src="{{ asset('js/select2.min.js') }}" type="text/javascript"></script>

    <script type="text/javascript">
        $(document).ready(function () {


            $('#select-empleado').select2({
                theme: "bootstrap-5",
                "language": {
                    "noResults": function(){
                        return "Búsqueda no encontrada";
                    }
                },
            });

            $('#select-distrito').select2({
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



            $('#select-empleado').select2({
                theme: "bootstrap-5",
                "language": {
                    "noResults": function(){
                        return "Busqueda no encontrada";
                    }
                },
            });

            $('#select-material').select2({
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


        function pdfEncargado(){
            var idempleado = document.getElementById('select-empleado').value;

            if(idempleado === ''){
                toastr.error('Empleado es requerido');
                return;
            }

            window.open("{{ URL::to('admin/reportes/pdf/recibe-separados') }}/" + idempleado);
        }


        function buscarUnidad(){
            let id = document.getElementById('select-distrito').value;

            if(id == '0'){
                document.getElementById("select-unidad").options.length = 0;
                document.getElementById("select-empleado").options.length = 0;
                return false;
            }

            $('#jefe-inmediato').val("");

            openLoading();

            axios.post(url+'/empleados/buscarunidad',{
                'id': id
            })
                .then((response) => {
                    closeLoading();
                    if(response.data.success === 1){

                        document.getElementById("select-unidad").options.length = 0;

                        $('#select-unidad').append('<option value=0 disabled selected>Seleccionar opción</option>');

                        // unidad de medida
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

        function buscarEmpleado(){
            let id = document.getElementById('select-unidad').value;

            if(id == '0'){
                document.getElementById("select-unidad").options.length = 0;
                document.getElementById("select-empleado").options.length = 0;
                return false;
            }

            $('#jefe-inmediato').val("");

            openLoading();

            axios.post(url+'/empleados/buscarunidad-empleado',{
                'id': id
            })
                .then((response) => {
                    closeLoading();
                    if(response.data.success === 1){

                        document.getElementById("select-empleado").options.length = 0;

                        $('#select-empleado').append('<option value=0 disabled selected>Seleccionar opción</option>');

                        // empleado
                        $.each(response.data.arrayEmpleados, function( key, val ){
                            $('#select-empleado').append('<option value="' + val.id + '" data-info="' + val.jefe + '">' + val.nombre + '</option>');
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


        function pdfKardexMaterial(){
            var idmaterial = document.getElementById('select-material').value;

            if(idmaterial === ''){
                toastr.error('Material es requerido');
                return;
            }

            window.open("{{ URL::to('admin/reportes/pdf/kardex-material') }}/" + idmaterial);
        }



    </script>

@endsection
