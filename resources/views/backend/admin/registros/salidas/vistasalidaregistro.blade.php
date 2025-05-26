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

    .checkbox-lg {
        transform: scale(1.3);
    }

    .label-lg {
        font-size: 1.1rem;
    }

    *:focus {
        outline: none;
    }
</style>

<div id="divcontenedor" style="display: none">


    <div class="card-body">
        <div class="tab-content">

            <form>
                <div class="card-body">

                    <section class="content">
                        <div class="container-fluid">
                            <div class="row">
                                <div class="col-md-12">

                                    <div class="card card-gray-dark">
                                        <div class="card-header">
                                            <h3 class="card-title">Información</h3>
                                        </div>

                                        <div class="card-body">
                                            <div class="card-body col-md-6">
                                                <div class="row">
                                                    <label>Fecha:</label>
                                                    <input style="width: 35%; margin-left: 25px;" type="date" class="form-control" id="fecha">
                                                </div>
                                            </div>

                                            <div class="form-group col-md-4">
                                                <label>Persona que Recibe:</label>
                                                <select id="select-encargado" class="form-control">
                                                    @foreach($arrayEncargado as $item)
                                                        <option value="{{$item->id}}">{{ $item->nombre }}</option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            <div class="form-group col-md-4">
                                                <label>Distrito:</label>
                                                <select id="select-distrito" class="form-control">
                                                    @foreach($arrayDistrito as $item)
                                                        <option value="{{$item->id}}">{{ $item->nombre }}</option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            <div class="form-group">
                                                <label>Descripción (Opcional):</label>
                                                <input type="text" class="form-control" autocomplete="off" maxlength="800" id="descripcion">
                                            </div>

                                            <div class="form-group" style="float: right">
                                                <br>
                                                <button type="button" id="botonaddmaterial" onclick="abrirModal()" class="btn btn-primary btn-sm float-right"
                                                        style="margin-top:10px; margin-right: 15px;">
                                                    <i class="fas fa-search" title="Buscar Material"></i> Buscar Material</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>

                </div>
            </form>



            <!-- fin - Tabs -->
        </div>
    </div>



    <!-- LISTADO DE MATERIALES A DESCARGAR DEL BUSCADOR -->



    <section class="content-header">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h2>Detalle de Salida</h2>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="card card-gray-dark">
                <div class="card-header">
                    <h3 class="card-title">Información de Ingreso</h3>
                </div>

                <table class="table" id="matriz" data-toggle="table" style="margin-right: 15px; margin-left: 15px;">
                    <thead>
                    <tr>
                        <th style="width: 3%">#</th>
                        <th style="width: 10%">Material</th>
                        <th style="width: 6%">Salida</th>
                        <th style="width: 6%">Retorno</th>
                        <th style="width: 5%">Opciones</th>
                    </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>

            </div>
        </div>
    </section>

    <div class="modal-footer justify-content-between">
        <button type="button" class="btn btn-success" onclick="preguntaGuardar()">Guardar</button>
    </div>


    <div class="modal fade" id="modalRepuesto" >
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Buscar Material</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">

                    <form id="formulario-repuesto">
                        <div class="card-body">

                            <div class="form-group">
                                <label class="control-label">Material (Regresa: Nombre - Medida - Normativa) (SOLO REGRESA MATERIAL CON INVENTARIO)</label>

                                <table class="table" id="matriz-busqueda" data-toggle="table">
                                    <tbody>
                                    <tr>
                                        <td>
                                            <input id="inputBuscador" autocomplete="off" class='form-control' style='width:100%' onkeyup='buscarMaterial(this)' maxlength='300' type='text'>
                                            <div class='droplista' id="midropmenu" style='position: absolute; z-index: 9; width: 75% !important;'></div>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>

                            <!-- cargara vista de selección -->
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div id="tablaRepuesto">

                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </form>

                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalCantidad">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Salida de Material</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="formulario-material">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12">

                                    <div class="form-group">
                                        <input type="hidden" disabled class="form-control" id="id-entradadetalle" autocomplete="off">
                                    </div>

                                    <div class="form-group">
                                        <label>Material</label>
                                        <input type="text" disabled class="form-control" id="info-material">
                                    </div>

                                    <hr>

                                    <!-- ** TABLA ** -->

                                    <table class="table" id="matrizM" data-toggle="table" style="margin-right: 15px; margin-left: 15px;">
                                        <thead>
                                        <tr>
                                            <th style="width: 5%">Fecha Ingreso</th>
                                            <th style="width: 5%">LOTE</th>
                                            <th style="width: 5%">Cantidad Actual</th>
                                            <th style="width: 5%">Cantidad Salida</th>
                                            <th style="width: 5%">Retornara</th>
                                        </tr>
                                        </thead>
                                        <tbody>

                                        </tbody>
                                    </table>

                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                    <button type="button" style="font-weight: bold; background-color: #28a745; color: white !important;" class="button button-rounded button-pill button-small" onclick="agregarAlDetalle()">Agregar</button>
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
    <script src="{{ asset('js/bootstrap-input-spinner.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/custom-editors.js') }}" type="text/javascript"></script>



    <script type="text/javascript">
        $(document).ready(function(){
            document.getElementById("divcontenedor").style.display = "block";

            var fecha = new Date();
            document.getElementById('fecha').value = fecha.toJSON().slice(0,10);

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

        function abrirModal(){
            document.getElementById('tablaRepuesto').innerHTML = "";
            document.getElementById("formulario-repuesto").reset();
            $('#modalRepuesto').modal('show');
        }

        function validateInput(event) {
            const key = event.key;

            // Permitir teclas de navegación y control
            if (["Backspace", "ArrowLeft", "ArrowRight", "Delete", "Tab"].includes(key)) {
                return true;
            }

            // Bloquear la tecla "e", signos negativos y todos excepto números
            if (key === "e" || key === "E" || key === "-" || isNaN(Number(key))) {
                return false;
            }

            return true;
        }

        function buscarMaterial(e){

            // seguro para evitar errores de busqueda continua
            if(seguroBuscador){
                seguroBuscador = false;

                var row = $(e).closest('tr');
                let texto = e.value;

                axios.post(url+'/buscar/material/disponible', {
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

            openLoading()

            var formData = new FormData();
            formData.append('id', edrop.id); // entradas_detalle
            $("#matrizM tbody tr").remove();

            axios.post(url+'/buscar/material/disponibilidad', formData, {
            })
                .then((response) => {
                    closeLoading();

                    if(response.data.success === 1){

                        if(response.data.disponible === 1){
                            toastr.info('NO HAY INVENTARIO');
                            return
                        }

                        $('#id-entradadetalle').val(edrop.id);
                        $('#info-material').val(response.data.nombreMaterial);

                        $.each(response.data.arrayIngreso, function( key, val ){

                            /*var nFilas = $('#matrizM >tbody >tr').length;
                            nFilas += 1;*/

                            var markup = "<tr>" +

                                "<td>" +
                                "<input disabled value='" + val.fechaIngreso + "' class='form-control' type='text'>" +
                                "</td>" +

                                "<td>" +
                                "<input disabled value='" + val.lote + "' class='form-control' type='text'>" +
                                "</td>" +

                                "<td>" +
                                "<input name='arrayCantidadActual[]' disabled data-cantidadActualFila='" + val.cantidadActual + "'  value='" + val.cantidadActual + "' class='form-control' type='number'>" +
                                "</td>" +

                                "<td>" +
                                "<input " +
                                "class='form-control' data-idfilaentradadetalle='" + val.id + "' name='arrayCantidadSalida[]' min='0' max='" + val.cantidad + "' " +
                                "type='number' " +
                                "onkeydown=\"return validateInput(event);\" " +
                                "oninput=\"validateCantidadSalida(this, " + val.cantidad + ");\">" +
                                "</td>" +

                                "<td>" +
                                "<div class='form-check d-flex align-items-center gap-2'>" +
                                "<input class='form-check-input checkbox-lg' type='checkbox' name='arrayRetornara[]' value='0' onchange='toggleRetornaraLabel(this)'>" +
                                "<label class='form-check-label mb-0 label-lg'>No</label>" +
                                "</div>" +
                                "</td>" +

                                "</tr>";

                            $("#matrizM tbody").append(markup);
                        });

                        $('#modalCantidad').modal('show');
                    }
                    else {
                        toastr.error('Error');
                    }
                })
                .catch((error) => {
                    toastr.error('Error');
                    closeLoading();
                });
        }

        function toggleRetornaraLabel(checkbox) {
            const label = checkbox.nextElementSibling;
            label.textContent = checkbox.checked ? 'Sí' : 'No';
        }


        // AGREGAR AL DETALLE
        function agregarAlDetalle(){

            // id entrada_detalle
            var arrayIdEntradaDetalle = $("input[name='arrayCantidadSalida[]']").map(function(){return $(this).attr("data-idfilaentradadetalle");}).get();
            // cantidad salida
            var arrayCantidadSalida = $("input[name='arrayCantidadSalida[]']").map(function(){return $(this).val();}).get();
            // cantidad actual de cada fila
            var arrayCantidadActual = $("input[name='arrayCantidadActual[]']").map(function(){return $(this).attr("data-cantidadActualFila");}).get();
            // checkbox Retornara
            var checkboxes = $("input[name='arrayRetornara[]']");




            colorBlancoTabla()
            var habraSalida = true;

            // recorrer y verificar
            for(var a = 0; a < arrayCantidadSalida.length; a++){

                let filaCantidad = arrayCantidadSalida[a];
                let infoFilaCantidadActual = arrayCantidadActual[a];

                if(filaCantidad !== ''){
                    if(filaCantidad <= 0){
                        colorRojoTabla(a);
                        alertaMensaje('info', 'Error', 'En la Fila #' + (a+1) + " No se permite ingreso de Cero, por favor borrarlo");
                        return
                    }
                    habraSalida = false;
                }

                // VERIFICAR QUE NO SUPERE CANTIDAD SALIDA AL CANTIDAD ACTUAL DE CADA FILA DE LA TABLA
                if(filaCantidad > Number(infoFilaCantidadActual)){
                    colorRojoTabla(a);
                    alertaMensaje('info', 'Error', 'En la Fila #' + (a+1) + " La cantidad de Salida supera a la Cantidad Actual");
                    return
                }
            }

            if(habraSalida){
                toastr.error('Registrar mínimo 1 salida');
                return
            }


            // RECORRER PARA AGREGAR CADA UNA AL DETALLE

            // nombre TXT del material
            var nombreTexto = document.getElementById('info-material').value;
            var nFilas = $('#matriz >tbody >tr').length;

            for(var z = 0; z < arrayCantidadSalida.length; z++){
                nFilas += 1;
                let infoFilaIdEntradaDetalle = arrayIdEntradaDetalle[z];
                let filaCantidad = arrayCantidadSalida[z];

                var valorRetornara = $(checkboxes[z]).prop("checked") ? 1 : 0;

                var textoCheckbox = "No";
                if(valorRetornara === 1){
                    textoCheckbox = "Si";
                }

                // Puedes usar `valorRetornara` aquí
                console.log("Retornará en fila " + z + ": " + valorRetornara);



                if(filaCantidad !== ''){
                    if(filaCantidad !== 0){
                        var markup = "<tr>" +

                            "<td>" +
                            "<p id='fila" + (nFilas) + "' class='form-control' style='max-width: 65px'>" + (nFilas) + "</p>" +
                            "</td>" +

                            "<td>" +
                            "<input name='idmaterialArray[]' type='hidden' data-idmaterialArray='" + infoFilaIdEntradaDetalle + "'>" +
                            "<input disabled value='" + nombreTexto + "' class='form-control' type='text'>" +
                            "</td>" +

                            "<td>" +
                            "<input name='salidaArray[]' disabled data-cantidadSalida='" + filaCantidad + "'" +
                            " value='" + filaCantidad + "' class='form-control' type='text'>" +
                            "</td>" +

                            "<td>" +
                            "<input name='retornoArray[]' disabled data-retorno='" + valorRetornara + "'" +
                            " value='" + textoCheckbox + "' class='form-control' type='text'>" +
                            "</td>" +


                            "<td>" +
                            "<button type='button' class='btn btn-block btn-danger' onclick='borrarFila(this)'>Borrar</button>" +
                            "</td>" +

                            "</tr>";

                        $("#matriz tbody").append(markup);
                    }
                }
            }


            $('#modalCantidad').modal('hide');
            document.getElementById('inputBuscador').value = '';

            Swal.fire({
                position: 'center',
                icon: 'success',
                title: 'Agregado al Detalle',
                showConfirmButton: false,
                timer: 1500
            })
        }

        function preguntaGuardar(){
            colorBlancoTabla();

            Swal.fire({
                title: 'Guardar Salida?',
                text: "",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#d33',
                cancelButtonText: 'Cancelar',
                confirmButtonText: 'Si'
            }).then((result) => {
                if (result.isConfirmed) {
                    guardarSalida();
                }
            })
        }




        function guardarSalida(){

            // fecha
            var fecha = document.getElementById('fecha').value;
            // id persona que recibe
            var idEncargado = document.getElementById('select-encargado').value;
            // distrito
            var idDistrito = document.getElementById('select-distrito').value;
            // descripcion
            var descripcion = document.getElementById('descripcion').value;

            if(fecha === ''){
                toastr.error('Fecha es requerida');
            }


            var reglaNumeroEntero = /^[0-9]\d*$/;
            var nRegistro = $('#matriz > tbody >tr').length;

            if (nRegistro <= 0){
                toastr.error('Registro Salida son requeridos');
                return;
            }

            var idEntradaDetalle = $("input[name='idmaterialArray[]']").map(function(){return $(this).attr("data-idmaterialArray");}).get();
            var salidaCantidad = $("input[name='salidaArray[]']").map(function(){return $(this).attr("data-cantidadSalida");}).get();

            // checkbox Retornara
            var checkboxes = $("input[name='retornoArray[]']").map(function(){return $(this).attr("data-retorno");}).get();

            //*******************

            // VERIFICAR LO QUE SE INGRESARA
            for(var a = 0; a < idEntradaDetalle.length; a++){

                //let infoIDEntradaDeta = idEntradaDetalle[a];
                let infoCantidad = salidaCantidad[a];

                if (infoCantidad === '') {
                    colorRojoTabla(a);
                    toastr.error('Fila #' + (a + 1) + ' Cantidad es requerida');
                    return;
                }

                if (!infoCantidad.match(reglaNumeroEntero)) {
                    colorRojoTabla(a);
                    toastr.error('Fila #' + (a + 1) + ' Cantidad debe ser Entero y no negativo');
                    return;
                }

                if (infoCantidad <= 0) {
                    colorRojoTabla(a);
                    toastr.error('Fila #' + (a + 1) + ' Cantidad no debe ser negativo');
                    return;
                }

                // Máximo 1 millón
                if (infoCantidad > 1000000) {
                    colorRojoTabla(a);
                    toastr.error('Fila #' + (a + 1) + ' Cantidad máximo 1 millón');
                    return;
                }
            }

            let formData = new FormData();
            const contenedorArray = [];

            for(var p = 0; p < salidaCantidad.length; p++){
                let infoIdEntradaDeta = idEntradaDetalle[p];
                let infoCantidad = salidaCantidad[p];
                var infoRetorno = checkboxes[p];

                contenedorArray.push({ infoIdEntradaDeta, infoCantidad, infoRetorno});
            }

            openLoading();

            formData.append('fecha', fecha);
            formData.append('idencargado', idEncargado);
            formData.append('iddistrito', idDistrito);
            formData.append('descripcion', descripcion);
            formData.append('contenedorArray', JSON.stringify(contenedorArray));

            axios.post(url+'/salida/guardar', formData, {
            })
                .then((response) => {
                    closeLoading();

                    if(response.data.success === 1){
                        // cuando va vacio la salida
                        toastr.error('Se requiere item de Salida');
                    }
                    else if(response.data.success === 2){
                        // VERIFICACION:NO SUPERAR LA CANTIDAD_ENTREGADA TOTAL DE ESE MATERIAL-LOTE
                        let fila = response.data.fila
                        toastr.error('Fila #' + fila + " Supera a las unidades existentes disponibles");
                    }
                    else if(response.data.success === 10){
                        msgActualizado()
                    }
                    else{
                        toastr.error('error al guardar');
                    }
                })
                .catch((error) => {
                    toastr.error('Error al guardar');
                    closeLoading();
                });
        }

        function msgError(fila){

            let msg = "En la Fila: " + fila + ": Se esta superando la cantidad disponible, revisar la salida del mismo Material";

            Swal.fire({
                title: 'Error',
                text: msg,
                icon: 'info',
                showCancelButton: false,
                allowOutsideClick: false,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Aceptar'
            }).then((result) => {
                if (result.isConfirmed) {

                }
            })
        }

        function msgActualizado(){
            Swal.fire({
                title: 'Salida Registrada',
                text: "",
                icon: 'success',
                showCancelButton: false,
                allowOutsideClick: false,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Aceptar'
            }).then((result) => {
                if (result.isConfirmed) {
                    location.reload();
                }
            })
        }



        function divColorRojo(pos){
            var divs = document.getElementsByClassName('arraycolor');
            $(divs[pos]).css("background-color", "red");
        }

        function borrarFila(elemento){
            var tabla = elemento.parentNode.parentNode;
            tabla.parentNode.removeChild(tabla);
            setearFila()
        }

        // cambiar # de fila cada vez que se borra la fila de
        // tabla nuevo material
        function setearFila(){

            var table = document.getElementById('matriz');
            var conteo = 0;
            for (var r = 1, n = table.rows.length; r < n; r++) {
                conteo +=1;
                var element = table.rows[r].cells[0].children[0];
                document.getElementById(element.id).innerHTML = ""+conteo;
            }
        }

        function colorRojoTabla(index){
            $("#matriz tr:eq("+(index+1)+")").css('background', '#F1948A');
        }

        function colorBlancoTabla(){
            $("#matriz tbody tr").css('background', 'white');
        }

        function limpiar(){
            document.getElementById('descripcion').value = '';
            document.getElementById('numero-salida').value = '';

            $("#matriz tbody tr").remove();
        }

        function borrarTabla(e){
            $("#matriz tbody tr").remove();
            $("#tablaInventario tbody tr").remove();
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
