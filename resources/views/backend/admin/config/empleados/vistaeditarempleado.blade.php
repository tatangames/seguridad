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
        background: linear-gradient(135deg, #1a5c2e 0%, #28a745 100%);
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
                    <i class="fas fa-user-edit mr-2"></i>Editar Empleado
                </h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.empleados.index') }}">Empleados</a>
                    </li>
                    <li class="breadcrumb-item active">Editar</li>
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
                            <h4><i class="fas fa-user-edit mr-2"></i>Editando</h4>
                        </div>
                        <div class="card-body" style="padding: 28px 32px;">

                            <input type="hidden" id="id-editar" value="{{ $empleado->id }}">

                            <div class="form-group">
                                <label class="campo-label">Distrito</label>
                                <select class="form-control" id="select-distrito-editar">
                                    @foreach($arrayDistrito as $dist)
                                        <option value="{{ $dist->id }}"
                                            {{ $empleado->unidadEmpleado?->id_distrito == $dist->id ? 'selected' : '' }}>
                                            {{ $dist->nombre }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group">
                                <label class="campo-label">Unidad</label>
                                <select class="form-control" id="select-unidad-editar">
                                    @foreach($arrayUnidad as $uni)
                                        <option value="{{ $uni->id }}"
                                            {{ $empleado->id_unidad_empleado == $uni->id ? 'selected' : '' }}>
                                            {{ $uni->nombre }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group">
                                <label class="campo-label">Nombre</label>
                                <input type="text" maxlength="100" class="form-control"
                                       id="nombre-editar" value="{{ $empleado->nombre }}" autocomplete="off">
                            </div>

                            <div class="form-group">
                                <label class="campo-label">DUI</label>
                                <input type="text" maxlength="50" class="form-control"
                                       id="dui-editar" value="{{ $empleado->dui }}" autocomplete="off">
                            </div>

                            <div class="form-group">
                                <label class="campo-label">Cargo</label>
                                <select class="form-control" id="select-cargo-editar">
                                    @foreach($arrayCargo as $cargo)
                                        <option value="{{ $cargo->id }}"
                                            {{ $empleado->id_cargo == $cargo->id ? 'selected' : '' }}>
                                            {{ $cargo->nombre }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group">
                                <label class="campo-label">¿Es Jefe?</label><br>
                                <label class="switch" style="margin-top:10px">
                                    <input type="checkbox" id="check-jefe-editar"
                                        {{ $empleado->jefe ? 'checked' : '' }}>
                                    <div class="slider round">
                                        <span class="on">SI</span>
                                        <span class="off">NO</span>
                                    </div>
                                </label>
                            </div>

                            <div class="form-group grupo-jefe-directo {{ $empleado->jefe ? '' : 'oculto' }}"
                                 id="grupo-jefe-editar">
                                <label class="campo-label">Jefe Directo
                                    <small class="text-muted font-weight-normal">(opcional)</small>
                                </label>
                                <select class="form-control" id="select-jefe-editar">
                                    <option value="">Sin jefe directo</option>
                                    @foreach($arrayEmpleados as $emp)
                                        <option value="{{ $emp->id }}"
                                            {{ $empleado->id_jefe == $emp->id ? 'selected' : '' }}>
                                            {{ $emp->nombre }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                        </div>
                        <div class="card-footer d-flex justify-content-between"
                             style="border-radius:0 0 12px 12px; background:#f8faff;">
                            <a href="{{ route('admin.empleados.index') }}"
                               class="btn btn-secondary">
                                <i class="fas fa-arrow-left mr-1"></i> Volver
                            </a>
                            <button type="button" id="btn-guardar-editar"
                                    style="font-weight:bold; background-color:#28a745; color:white;"
                                    class="button button-rounded button-pill button-small">
                                <i class="fas fa-save mr-1"></i> Actualizar
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
            initSelect2('#select-distrito-editar');
            initSelect2('#select-unidad-editar');
            initSelect2('#select-cargo-editar');
            initSelect2('#select-jefe-editar');

            /* ── Toggle jefe directo ── */
            $('#check-jefe-editar').on('change', function () {
                if (this.checked) {
                    $('#grupo-jefe-editar').removeClass('oculto');
                } else {
                    $('#grupo-jefe-editar').addClass('oculto');
                    $('#select-jefe-editar').val('').trigger('change.select2');
                }
            });

            /* ── Cambio de distrito → recargar unidades ── */
            $('#select-distrito-editar').on('change', function () {
                var id = $(this).val();
                openLoading();
                axios.post(url + '/empleados/buscarunidad', { id: id })
                    .then(function (r) {
                        closeLoading();
                        if (r.data.success === 1) {
                            if ($('#select-unidad-editar').hasClass('select2-hidden-accessible')) {
                                $('#select-unidad-editar').select2('destroy');
                            }
                            $('#select-unidad-editar').empty();
                            $.each(r.data.arrayUnidad, function (k, v) {
                                $('#select-unidad-editar').append(
                                    '<option value="' + v.id + '">' + v.nombre + '</option>'
                                );
                            });
                            initSelect2('#select-unidad-editar');
                        } else {
                            toastr.error('No se encontraron unidades');
                        }
                    })
                    .catch(function () { closeLoading(); toastr.error('Error al cargar unidades'); });
            });

            /* ── Actualizar empleado ── */
            $('#btn-guardar-editar').on('click', function () {
                var id     = $('#id-editar').val();
                var nombre = $('#nombre-editar').val().trim();
                var unidad = $('#select-unidad-editar').val();
                var cargo  = $('#select-cargo-editar').val();
                var dui    = $('#dui-editar').val().trim();
                var jefe   = $('#check-jefe-editar').is(':checked') ? 1 : 0;
                var idJefe = jefe === 1 ? $('#select-jefe-editar').val() : '';

                if (!nombre) { toastr.error('El nombre es requerido'); return; }
                if (!unidad) { toastr.error('Seleccione una unidad');  return; }

                openLoading();
                var fd = new FormData();
                fd.append('id',      id);
                fd.append('nombre',  nombre);
                fd.append('unidad',  unidad);
                fd.append('cargo',   cargo);
                fd.append('dui',     dui);
                fd.append('jefe',    jefe);
                fd.append('id_jefe', idJefe);

                axios.post(url + '/empleados/actualizar', fd)
                    .then(function (r) {
                        closeLoading();
                        if (r.data.success === 1) {
                            toastr.success('Empleado actualizado correctamente');

                            // ── Limpiar todos los campos ──
                            $('#nombre-editar').val('');
                            $('#dui-editar').val('');
                            $('#check-jefe-editar').prop('checked', false);
                            $('#grupo-jefe-editar').addClass('oculto');
                            $('#select-jefe-editar').val('').trigger('change.select2');
                            $('#select-cargo-editar').val($('#select-cargo-editar option:first').val()).trigger('change.select2');
                            $('#select-distrito-editar').val($('#select-distrito-editar option:first').val()).trigger('change.select2');
                            $('#select-unidad-editar').empty()
                                .append('<option value="">— Sin unidad —</option>');
                            initSelect2('#select-unidad-editar');

                            setTimeout(function () {
                                window.location.href = "{{ route('admin.empleados.index') }}";
                            }, 1200);

                        } else {
                            toastr.error('Error al actualizar');
                        }
                    })
                    .catch(function () { closeLoading(); toastr.error('Error al actualizar'); });
            });

            document.getElementById('divcontenedor').style.display = 'block';
        });

    </script>
@endsection
