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

    .card-header {
        display: flex;
        justify-content: center; /* Centra horizontalmente */
        align-items: center; /* Centra verticalmente */
    }

</style>

<div id="divcontenedor" style="display: none">

    <section class="content" style="margin-top: 15px">
        <div class="container-fluid">
            <div class="card card-gray-dark">
                <div class="card-header">
                    <h3 class="card-title">BUSCADOR DE PERSONAS</h3>
                </div>
                <div class="card-body">

                    <div class="form-group col-md-8">
                        <label class="control-label"></label>

                        <table class="table" id="matriz-busqueda" data-toggle="table">
                            <tbody>
                            <tr>
                                <td>
                                    <input id="inputBuscador" autocomplete="off" class='form-control' style='width:100%' onkeyup='buscarPersona(this)' maxlength='300' type='text'>
                                    <div class='droplista' id="midropmenu" style='position: absolute; z-index: 9; width: 75% !important;'></div>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>

                </div>
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

    <script type="text/javascript">
        $(document).ready(function(){

            window.seguroBuscador = true;

            $(document).click(function(){
                $(".droplista").hide();
            });

            $(document).ready(function() {
                $('[data-toggle="popover"]').popover({
                    placement: 'top',
                    trigger: 'hover'
                });
            });

            document.getElementById("divcontenedor").style.display = "block";
        });
    </script>

    <script>

        function buscarPersona(e){

            // seguro para evitar errores de busqueda continua
            if(seguroBuscador){
                seguroBuscador = false;

                var row = $(e).closest('tr');
                let texto = e.value;

                axios.post(url+'/buscar/persona', {
                    'query' : texto,
                })
                    .then((response) => {

                        seguroBuscador = true;
                        $(row).each(function (index, element) {
                            $(this).find(".droplista").fadeIn();
                            $(this).find(".droplista").html(response.data);
                        });
                    })
                    .catch((error) => {
                        seguroBuscador = true;
                    });
            }
        }

        function modificarValor(edrop) {
            window.location.href="{{ url('/admin/buscador/persona') }}/" + edrop.id;
        }

    </script>


@endsection
