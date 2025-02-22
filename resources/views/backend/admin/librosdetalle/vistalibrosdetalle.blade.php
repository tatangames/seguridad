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
                                <select id="select-proyectos" class="form-control">
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
            var id = document.getElementById('select-proyectos').value;
            var ruta = "{{ URL::to('/admin/librosdetalle/tabla') }}/" + id;
            $('#tablaDatatable').load(ruta);
        }


        function mostrarAyuda(){

            let mensaje = `Cuando se hace el Primer Registro para calcular fecha Vencimiento se toma Fecha de Fallecimiento,
                Cuando se hace un Cobro se toma en cuentra Fecha Recibo (Tesoreria).
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

        function infoBorrar(id){
            Swal.fire({
                title: 'Borrar Registro',
                text: "Se eliminara el Nicho",
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

            axios.post(url+'/librosdetalle/borrarnicho/completo', formData, {
            })
                .then((response) => {
                    closeLoading();
                    if(response.data.success === 1){
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


        function vistaDetalle(idsolicitud){
            // window.location.href="{{ url('/admin/bodega/historial/salidadetalle/index') }}/" + idsolicitud;
        }




    </script>


@endsection
