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
                    <h3 class="card-title">REPORTE ENTREGAS A ENCARGADO</h3>
                </div>
                <div class="card-body">
                    <section class="content" style="margin-left: 30px">
                        <div class="container-fluid">



                            <!-- Fechas -->
                            <div class="form-group col-md-4">
                                <label>Encargado</label>
                                <select class="form-control" id="select-encargado">
                                    @foreach($arrayEncargados as $item)
                                        <option value="{{$item->id}}">{{$item->nombre}}</option>
                                    @endforeach
                                </select>
                            </div>


                            <div class="form-group" style="margin-top: 5px">
                                <label for="checkbox-todos" class="checkbox-label">
                                    <input type="checkbox" class="checkbox" id="checkbox-recibe">
                                    Entregas Separadas?
                                </label>
                            </div>


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


            $('#select-encargado').select2({
                theme: "bootstrap-5",
                "language": {
                    "noResults": function(){
                        return "Búsqueda no encontrada";
                    }
                },
            });


            document.getElementById("divcontenedor").style.display = "block";
        });
    </script>

    <script>


        function pdfEncargado(){
            var idencargado = document.getElementById('select-encargado').value;
            var checkbox = document.getElementById('checkbox-recibe');
            var valorCheckbox = checkbox.checked ? 1 : 0;

            if(idencargado === ''){
                toastr.error('Persona que Recibe es requerido');
                return;
            }

            if(valorCheckbox === 1){ // separadas
                window.open("{{ URL::to('admin/reportes/pdf/recibe-separados') }}/" + idencargado);
            }else{
                window.open("{{ URL::to('admin/reportes/pdf/recibe-juntos') }}/" + idencargado);
            }
        }


    </script>

@endsection
