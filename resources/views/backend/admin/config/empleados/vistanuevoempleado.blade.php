@extends('backend.menus.superior')

@section('content-admin-css')
    <link href="{{ asset('css/adminlte.min.css') }}" type="text/css" rel="stylesheet" />
    <link href="{{ asset('css/dataTables.bootstrap4.css') }}" type="text/css" rel="stylesheet" />
    <link href="{{ asset('css/toastr.min.css') }}" type="text/css" rel="stylesheet" />
    <link href="{{ asset('css/buttons_estilo.css') }}" rel="stylesheet">
    <link href="{{ asset('css/select2.min.css') }}" type="text/css" rel="stylesheet">
    <link href="{{ asset('css/select2-bootstrap-5-theme.min.css') }}" type="text/css" rel="stylesheet">
    <link href="{{ asset('css/estiloToggle.css') }}" type="text/css" rel="stylesheet" />
@stop

<style>
    .card-form {
        border-radius: 12px;
        border: none;
        box-shadow: 0 4px 24px rgba(33,86,175,.12);
    }
    .card-form .card-header {
        background: linear-gradient(135deg, #1a3a6b 0%, #2156af 100%);
        border-radius: 12px 12px 0 0;
        padding: 16px 24px;
    }
    .card-form .card-header h4 {
        color: #fff;
        margin: 0;
        font-size: 16px;
        font-weight: 700;
    }
    .campo-label {
        font-size: 12px;
        font-weight: 700;
        color: #4a5568;
        text-transform: uppercase;
        letter-spacing: .05em;
        margin-bottom: 4px;
    }
    .grupo-jefe-directo.oculto { display: none; }
    .select2-container--open   { z-index: 99999 !important; }
    .select2-dropdown          { z-index: 99999 !important; }
</style>

<div id="divcontenedor" style="display:none">

    <section class="content-header">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 style="font-size:18px; color:#1a2d55; font-weight:700;">
                    <i class="fas fa-user-plus mr-2"></i>Nuevo Empleado
                </h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.empleados.index') }}">Empleados</a>
                    </li>
                    <li class="breadcrumb-item active">Nuevo Empleado</li>
                </ol>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="col-lg-7 col-md-9">

                    <div class="card card-form">
                        <div class="card-header">
                            <h4><i class="fas fa-user-plus mr-2"></i>Datos del Empleado</h4>
                        </div>
                        <div class="card-body" style="padding: 28px 32px;">

                            <div class="form-group">
                                <label class="campo-label">Distrito</label>
                                <select class="form-control" id="select-distrito">
                                    <option value="0">Seleccionar opción</option>
                                    @foreach($arrayDistrito as $sel)
                                        <option value="{{ $sel->id }}">{{ $sel->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group">
                                <label class="campo-label">Unidad</label>
                                <select class="form-control" id="select-unidad">
                                    <option value="">— Seleccione un distrito primero —</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label class="campo-label">Nombre</label>
                                <input type="text" maxlength="100" class="form-control"
                                       id="nombre-nuevo" placeholder="Nombre completo" autocomplete="off">
                            </div>

                            <div class="form-group">
                                <label class="campo-label">DUI</label>
                                <input type="text" maxlength="50" class="form-control"
                                       id="dui-nuevo" placeholder="00000000-0" autocomplete="off">
                            </div>

                            <div class="form-group">
                                <label class="campo-label">Cargo</label>
                                <select class="form-control" id="select-cargo">
                                    @foreach($arrayCargo as $sel)
                                        <option value="{{ $sel->id }}">{{ $sel->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group">
                                <label class="campo-label">¿Es Jefe?</label><br>
                                <label class="switch" style="margin-top:10px">
                                    <input type="checkbox" id="check-jefe">
                                    <div class="slider round">
                                        <span class="on">SI</span>
                                        <span class="off">NO</span>
                                    </div>
                                </label>
                            </div>

                            <div class="form-group grupo-jefe-directo oculto" id="grupo-jefe-nuevo">
                                <label class="campo-label">Jefe Directo
                                    <small class="text-muted font-weight-normal">(opcional)</small>
                                </label>
                                <select class="form-control" id="select-jefe-nuevo">
                                    <option value="">Sin jefe directo</option>
                                    @foreach($arrayEmpleados as $emp)
                                        <option value="{{ $emp->id }}">{{ $emp->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>

                        </div>
                        <div class="card-footer d-flex justify-content-between"
                             style="border-radius:0 0 12px 12px; background:#f8faff;">
                            <a href="{{ route('admin.empleados.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left mr-1"></i> Volver
                            </a>
                            <button type="button" id="btn-guardar-nuevo"
                                    style="font-weight:bold; background-color:#2156af; color:white;"
                                    class="button button-rounded button-pill button-small">
                                <i class="fas fa-save mr-1"></i> Guardar
                            </button>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </section>

</div>

@extends('backend.menus.footerjs')
@section('archivos-js')
    <script src="{{ asset('js/jquery.dataTables.js') }}"></script>
    <script src="{{ asset('js/dataTables.bootstrap4.js') }}"></script>
    <script src="{{ asset('js/toastr.min.js') }}"></script>
    <script src="{{ asset('js/axios.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/select2.min.js') }}"></script>
    <script src="{{ asset('js/sweetalert2.all.min.js') }}"></script>
    <script src="{{ asset('js/alertaPersonalizada.js') }}"></script>

    <script>

        /* ══════════════════════════════════════════════════════════════════
         *  HELPERS SELECT2
         * ══════════════════════════════════════════════════════════════════ */
        function s2opts() {
            return {
                theme: "bootstrap-5",
                dropdownParent: $('body'),
                minimumResultsForSearch: 0,
                width: '100%',
                language: { noResults: function () { return "No encontrado"; } }
            };
        }

        function initSelect2(selector) {
            if ($(selector).hasClass('select2-hidden-accessible')) {
                $(selector).select2('destroy');
            }
            $(selector).select2(s2opts());
        }

        /* ══════════════════════════════════════════════════════════════════
         *  DOCUMENT READY
         * ══════════════════════════════════════════════════════════════════ */
        $(document).ready(function () {

            // Inicializar todos los Select2
            initSelect2('#select-distrito');
            initSelect2('#select-unidad');
            initSelect2('#select-cargo');
            initSelect2('#select-jefe-nuevo');

            /* ── Toggle jefe directo ── */
            $('#check-jefe').on('change', function () {
                if (this.checked) {
                    $('#grupo-jefe-nuevo').removeClass('oculto');
                } else {
                    $('#grupo-jefe-nuevo').addClass('oculto');
                    $('#select-jefe-nuevo').val('').trigger('change.select2');
                }
            });

            /* ── Cambio de distrito → cargar unidades ── */
            $('#select-distrito').on('change', function () {
                var id = $(this).val();

                if (!id || id == '0') {
                    if ($('#select-unidad').hasClass('select2-hidden-accessible')) {
                        $('#select-unidad').select2('destroy');
                    }
                    $('#select-unidad').empty()
                        .append('<option value="">— Seleccione un distrito primero —</option>');
                    initSelect2('#select-unidad');
                    return;
                }

                openLoading();
                axios.post(url + '/empleados/buscarunidad', { id: id })
                    .then(function (r) {
                        closeLoading();
                        if (r.data.success === 1) {
                            if ($('#select-unidad').hasClass('select2-hidden-accessible')) {
                                $('#select-unidad').select2('destroy');
                            }
                            $('#select-unidad').empty();
                            $.each(r.data.arrayUnidad, function (k, v) {
                                $('#select-unidad').append(
                                    '<option value="' + v.id + '">' + v.nombre + '</option>'
                                );
                            });
                            initSelect2('#select-unidad');
                        } else {
                            toastr.error('No se encontraron unidades para este distrito');
                        }
                    })
                    .catch(function () {
                        closeLoading();
                        toastr.error('Error al cargar unidades');
                    });
            });

            /* ── Guardar nuevo empleado ── */
            /* ── Guardar nuevo empleado ── */
            $('#btn-guardar-nuevo').on('click', function () {
                var unidad = $('#select-unidad').val();
                var cargo  = $('#select-cargo').val();
                var nombre = $('#nombre-nuevo').val().trim();
                var dui    = $('#dui-nuevo').val().trim();
                var jefe   = $('#check-jefe').is(':checked') ? 1 : 0;
                var idJefe = jefe === 1 ? $('#select-jefe-nuevo').val() : '';

                if (!unidad || unidad == '') { toastr.error('Seleccione una unidad');  return; }
                if (!cargo)                  { toastr.error('Seleccione un cargo');    return; }
                if (!nombre)                 { toastr.error('El nombre es requerido'); return; }

                openLoading();
                var fd = new FormData();
                fd.append('nombre',  nombre);
                fd.append('unidad',  unidad);
                fd.append('cargo',   cargo);
                fd.append('dui',     dui);
                fd.append('jefe',    jefe);
                fd.append('id_jefe', idJefe);

                axios.post(url + '/empleados/nuevo', fd)
                    .then(function (r) {
                        closeLoading();
                        if (r.data.success === 1) {
                            toastr.success('Empleado registrado correctamente');

                            // ── Limpiar todos los campos ──
                            $('#nombre-nuevo').val('');
                            $('#dui-nuevo').val('');
                            $('#check-jefe').prop('checked', false);
                            $('#grupo-jefe-nuevo').addClass('oculto');

                            // Resetear selects con Select2
                            $('#select-distrito').val('0').trigger('change');  // esto también limpia unidades
                            $('#select-cargo').val($('#select-cargo option:first').val()).trigger('change.select2');
                            $('#select-jefe-nuevo').val('').trigger('change.select2');

                            setTimeout(function () {
                                window.location.href = "{{ route('admin.empleados.index') }}";
                            }, 1200);

                        } else {
                            toastr.error('Error al registrar');
                        }
                    })
                    .catch(function () {
                        closeLoading();
                        toastr.error('Error al registrar');
                    });
            });

            document.getElementById('divcontenedor').style.display = 'block';
        });

    </script>
@endsection
