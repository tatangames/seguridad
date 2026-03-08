@extends('backend.menus.superior')

@section('content-admin-css')
    <link href="{{ asset('css/adminlte.min.css') }}" type="text/css" rel="stylesheet" />
    <link href="{{ asset('css/dataTables.bootstrap4.css') }}" type="text/css" rel="stylesheet" />
    <link href="{{ asset('css/toastr.min.css') }}" type="text/css" rel="stylesheet" />
    <link href="{{ asset('css/buttons_estilo.css') }}" rel="stylesheet">
    <link href="{{ asset('css/select2.min.css') }}" type="text/css" rel="stylesheet">
    <link href="{{ asset('css/select2-bootstrap-5-theme.min.css') }}" type="text/css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600;700&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">
@stop

<style>
    :root {
        --bg-base:      #ffffff;
        --bg-card:      #ffffff;
        --bg-card2:     #f4f6fa;
        --accent:       #3b7ef6;
        --accent-glow:  rgba(59,126,246,.18);
        --accent2:      #0fb87a;
        --danger:       #e74c3c;
        --warning:      #f39c12;
        --text-primary: #1a1f36;
        --text-muted:   #7a849e;
        --border:       #e2e6ef;
        --radius:       14px;
        --radius-sm:    8px;
    }

    * { box-sizing: border-box; margin: 0; padding: 0; }

    body, .wrapper, .content-wrapper {
        background: #f0f2f8 !important;
        font-family: 'DM Sans', sans-serif;
        color: var(--text-primary);
    }

    /* ── Encabezado de página ── */
    .page-header {
        display: flex;
        align-items: center;
        gap: 18px;
        padding: 28px 32px 0;
    }
    .page-header-icon {
        width: 52px; height: 52px;
        background: linear-gradient(135deg, var(--accent), #2f5fc4);
        border-radius: var(--radius-sm);
        display: flex; align-items: center; justify-content: center;
        box-shadow: 0 0 24px var(--accent-glow);
        font-size: 22px; color: #fff; flex-shrink: 0;
    }
    .page-header h1 {
        font-size: 1.55rem; font-weight: 700;
        letter-spacing: -.3px; color: var(--text-primary);
    }
    .page-header p {
        font-size: .82rem; color: var(--text-muted); margin-top: 2px;
    }

    /* ── Contenedor principal ── */
    .salidas-wrapper {
        padding: 22px 32px 40px;
    }

    /* ── Stats row ── */
    .stats-row {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
        gap: 14px;
        margin-bottom: 22px;
    }
    .stat-card {
        background: var(--bg-card);
        border: 1px solid var(--border);
        border-radius: var(--radius);
        padding: 18px 20px;
        display: flex; flex-direction: column; gap: 6px;
        transition: border-color .2s, transform .2s;
    }
    .stat-card:hover { border-color: var(--accent); transform: translateY(-2px); }
    .stat-label { font-size: .72rem; text-transform: uppercase; letter-spacing: .08em; color: var(--text-muted); }
    .stat-value { font-size: 1.75rem; font-weight: 700; font-family: 'DM Mono', monospace; color: var(--text-primary); }
    .stat-value.accent  { color: var(--accent); }
    .stat-value.accent2 { color: var(--accent2); }

    /* ── Card tabla ── */
    .tabla-card {
        background: var(--bg-card);
        border: 1px solid var(--border);
        border-radius: var(--radius);
        overflow: hidden;
    }
    .tabla-card-header {
        display: flex; align-items: center; justify-content: space-between;
        padding: 18px 22px;
        border-bottom: 1px solid var(--border);
        background: var(--bg-card2);
    }
    .tabla-card-header-title {
        display: flex; align-items: center; gap: 10px;
        font-weight: 600; font-size: 1rem;
    }
    .dot { width: 8px; height: 8px; border-radius: 50%; background: var(--accent); box-shadow: 0 0 8px var(--accent); }
    .tabla-card-body { padding: 18px 22px; }

    /* ── Modal mejorado ── */
    .modal-content {
        background: #fff !important;
        border: 1px solid var(--border) !important;
        border-radius: var(--radius) !important;
        color: var(--text-primary) !important;
        box-shadow: 0 20px 60px rgba(0,0,0,.12) !important;
    }
    .modal-header {
        background: var(--bg-card2) !important;
        border-bottom: 1px solid var(--border) !important;
        padding: 18px 24px !important;
        border-radius: var(--radius) var(--radius) 0 0 !important;
    }
    .modal-title {
        font-weight: 700; font-size: 1.05rem; color: var(--text-primary);
        display: flex; align-items: center; gap: 10px;
    }
    .modal-title::before {
        content: '✦';
        color: var(--accent);
        font-size: .9rem;
    }
    .modal-footer {
        background: var(--bg-card2) !important;
        border-top: 1px solid var(--border) !important;
        padding: 14px 24px !important;
        border-radius: 0 0 var(--radius) var(--radius) !important;
    }
    .close { color: var(--text-muted) !important; opacity: 1 !important; }
    .close:hover { color: var(--danger) !important; }

    /* ── Form controls en modal ── */
    .modal-body .form-group label {
        font-size: .78rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: .06em;
        color: var(--text-muted);
        margin-bottom: 6px;
        display: block;
    }
    .modal-body .form-control,
    .select2-container--bootstrap-5 .select2-selection {
        background: #fff !important;
        border: 1px solid var(--border) !important;
        border-radius: var(--radius-sm) !important;
        color: var(--text-primary) !important;
        font-family: 'DM Sans', sans-serif !important;
        font-size: .9rem !important;
        padding: 10px 14px !important;
        height: auto !important;
        transition: border-color .2s, box-shadow .2s;
    }
    .modal-body .form-control:focus {
        border-color: var(--accent) !important;
        box-shadow: 0 0 0 3px var(--accent-glow) !important;
        outline: none !important;
    }
    .select2-container--bootstrap-5 .select2-selection--single .select2-selection__rendered {
        color: var(--text-primary) !important;
        line-height: 1.6 !important;
        padding-left: 0 !important;
    }
    .select2-dropdown {
        background: #fff !important;
        border: 1px solid var(--border) !important;
        border-radius: var(--radius-sm) !important;
        box-shadow: 0 8px 24px rgba(0,0,0,.1) !important;
    }
    .select2-results__option {
        color: var(--text-primary) !important;
        font-size: .88rem !important;
    }
    .select2-results__option--highlighted {
        background: var(--accent) !important;
        color: #fff !important;
    }
    .select2-search--dropdown .select2-search__field {
        background: #f4f6fa !important;
        border: 1px solid var(--border) !important;
        color: var(--text-primary) !important;
        border-radius: var(--radius-sm) !important;
    }

    /* ── Botones ── */
    .btn-accion-primary {
        background: linear-gradient(135deg, var(--accent), #2f5fc4);
        color: #fff !important;
        border: none;
        border-radius: var(--radius-sm);
        padding: 10px 22px;
        font-weight: 600;
        font-size: .88rem;
        letter-spacing: .02em;
        cursor: pointer;
        transition: opacity .2s, transform .15s, box-shadow .2s;
        box-shadow: 0 4px 14px var(--accent-glow);
        display: inline-flex; align-items: center; gap: 7px;
    }
    .btn-accion-primary:hover {
        opacity: .9; transform: translateY(-1px);
        box-shadow: 0 6px 20px var(--accent-glow);
    }
    .btn-cancelar {
        background: transparent;
        border: 1px solid var(--border);
        color: var(--text-muted);
        border-radius: var(--radius-sm);
        padding: 10px 20px;
        font-weight: 500;
        font-size: .88rem;
        cursor: pointer;
        transition: border-color .2s, color .2s;
    }
    .btn-cancelar:hover { border-color: var(--text-muted); color: var(--text-primary); }

    /* ── Separador de sección en modal ── */
    .section-divider {
        display: flex; align-items: center; gap: 10px;
        margin: 18px 0 14px;
        font-size: .72rem; font-weight: 600;
        text-transform: uppercase; letter-spacing: .1em;
        color: var(--accent);
    }
    .section-divider::after {
        content: ''; flex: 1; height: 1px;
        background: linear-gradient(90deg, var(--accent-glow), transparent);
    }

    /* ── DataTables overrides (light) ── */
    #tablaDatatable .dataTables_wrapper { color: var(--text-primary) !important; }
    #tablaDatatable .dataTables_filter input,
    #tablaDatatable .dataTables_length select {
        background: #fff !important;
        border: 1px solid var(--border) !important;
        color: var(--text-primary) !important;
        border-radius: var(--radius-sm) !important;
        padding: 6px 10px !important;
        box-shadow: none !important;
    }
    #tablaDatatable .dataTables_filter input:focus {
        border-color: var(--accent) !important;
        box-shadow: 0 0 0 2px var(--accent-glow) !important;
        outline: none;
    }
    #tablaDatatable .dataTables_filter label,
    #tablaDatatable .dataTables_length label,
    #tablaDatatable .dataTables_info { color: var(--text-muted) !important; font-size: .82rem !important; }
    #tablaDatatable .paginate_button { color: var(--text-muted) !important; border-radius: var(--radius-sm) !important; }
    #tablaDatatable .paginate_button.current,
    #tablaDatatable .paginate_button.current:hover {
        background: var(--accent) !important;
        border-color: var(--accent) !important;
        color: #fff !important;
    }
    #tablaDatatable .paginate_button:not(.disabled):hover {
        background: #eef2ff !important;
        border-color: var(--border) !important;
        color: var(--accent) !important;
    }
    #tablaDatatable .paginate_button.disabled { color: #ccc !important; }

    /* ── Scroll horizontal ── */
    .tabla-card-body { overflow-x: auto !important; -webkit-overflow-scrolling: touch; }
    #tabla { min-width: 860px; }

    /* Animación de entrada */
    @keyframes fadeUp {
        from { opacity: 0; transform: translateY(18px); }
        to   { opacity: 1; transform: translateY(0); }
    }
    .anim-in { animation: fadeUp .4s ease both; }
    .anim-in:nth-child(2) { animation-delay: .07s; }
    .anim-in:nth-child(3) { animation-delay: .14s; }
    .anim-in:nth-child(4) { animation-delay: .21s; }

    table { table-layout: fixed; }
</style>

<div id="divcontenedor" style="display:none">

    <!-- Encabezado -->
    <div class="page-header anim-in">
        <div class="page-header-icon">
            <i class="fas fa-box-open"></i>
        </div>
        <div>
            <h1>Historial de Salidas</h1>
            <p>Registro y gestión de salidas de bodega</p>
        </div>
    </div>

    <!-- Stats -->
    <div class="salidas-wrapper">
        <div class="stats-row">
            <div class="stat-card anim-in">
                <span class="stat-label">Total Salidas</span>
                <span class="stat-value accent" id="stat-total">—</span>
            </div>
        </div>

        <!-- Tabla -->
        <div class="tabla-card anim-in">
            <div class="tabla-card-header">
                <div class="tabla-card-header-title">
                    <span class="dot"></span>
                    Listado de Salidas
                </div>
            </div>
            <div class="tabla-card-body">
                <div id="tablaDatatable"></div>
            </div>
        </div>
    </div>

    <!-- Modal Editar -->
    <div class="modal fade" id="modalEditar" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Editar Salida</h4>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body" style="padding: 24px">
                    <form id="formulario-editar">
                        <input type="hidden" id="id-editar">

                        <div class="section-divider">Información General</div>
                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label>Fecha</label>
                                <input type="date" class="form-control" id="fecha-editar" autocomplete="off">
                            </div>
                            <div class="col-md-6 form-group">
                                <label># Equipo / Material Línea</label>
                                <input type="text" maxlength="100" class="form-control" id="linea-editar" autocomplete="off">
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Descripción</label>
                            <input type="text" maxlength="800" class="form-control" id="descripcion-editar" autocomplete="off">
                        </div>

                        <div class="section-divider">Asignación</div>
                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label>Distrito</label>
                                <select class="form-control" id="select-distrito-editar" onchange="buscarUnidadEdicion(this)"></select>
                            </div>
                            <div class="col-md-6 form-group">
                                <label>Unidad</label>
                                <select class="form-control" id="select-unidad-editar" onchange="buscarEmpleadosEdicion(this)"></select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Empleado</label>
                            <select class="form-control" id="select-empleados-editar"></select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-cancelar" data-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn-accion-primary" onclick="editar()">
                        <i class="fas fa-save"></i> Guardar Cambios
                    </button>
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

    <script>
        $(document).ready(function(){
            openLoading();
            var ruta = "{{ URL::to('/admin/historial/salidas/tabla') }}";
            $('#tablaDatatable').load(ruta, function(){
                setTimeout(calcularStats, 600);
            });

            // Inicializar select2 DENTRO del modal, con dropdownParent para evitar bloqueos
            const selectOpts = {
                theme: "bootstrap-5",
                dropdownParent: $('#modalEditar'),
                language: { noResults: () => "Búsqueda no encontrada" }
            };
            $('#select-unidad-editar').select2(selectOpts);
            $('#select-distrito-editar').select2(selectOpts);
            $('#select-empleados-editar').select2(selectOpts);

            document.getElementById("divcontenedor").style.display = "block";
        });

        function calcularStats(){
            const filas = document.querySelectorAll('#tabla tbody tr');
            animateCount('stat-total', filas.length);
        }

        function animateCount(id, target){
            const el = document.getElementById(id);
            if(!el) return;
            let start = 0;
            const step = Math.ceil(target / 30);
            const timer = setInterval(() => {
                start = Math.min(start + step, target);
                el.textContent = start;
                if(start >= target) clearInterval(timer);
            }, 25);
        }
    </script>

    <script>
        function recargar(){
            var ruta = "{{ URL::to('/admin/historial/salidas/tabla') }}";
            $('#tablaDatatable').load(ruta, function(){ setTimeout(calcularStats, 600); });
        }

        function vistaDetalle(idsalida){
            window.location.href = "{{ url('/admin/historial/salidadetalle/index') }}/" + idsalida;
        }

        function informacion(id){
            openLoading();
            document.getElementById("formulario-editar").reset();

            axios.post(url+'/historial/salidas/informacion', { id })
                .then(({ data }) => {
                    closeLoading();
                    if(data.success === 1){
                        $('#modalEditar').modal('show');
                        $('#id-editar').val(id);
                        $('#fecha-editar').val(data.info.fecha);
                        $('#descripcion-editar').val(data.info.descripcion);
                        $('#linea-editar').val(data.info.material_linea);

                        // Destruir select2 antes de manipular opciones
                        ['#select-distrito-editar','#select-unidad-editar','#select-empleados-editar'].forEach(s => {
                            if($(s).hasClass('select2-hidden-accessible')) $(s).select2('destroy');
                        });

                        document.getElementById("select-distrito-editar").options.length = 0;
                        document.getElementById("select-unidad-editar").options.length = 0;
                        document.getElementById("select-empleados-editar").options.length = 0;

                        data.arrayDistrito.forEach(v => {
                            const sel = data.infoUniEmpleado.id_distrito == v.id ? ' selected' : '';
                            $('#select-distrito-editar').append(`<option value="${v.id}"${sel}>${v.nombre}</option>`);
                        });
                        data.arrayUnidad.forEach(v => {
                            const sel = data.infoUniEmpleado.id == v.id ? ' selected' : '';
                            $('#select-unidad-editar').append(`<option value="${v.id}"${sel}>${v.nombre}</option>`);
                        });
                        data.arrayEmpleados.forEach(v => {
                            const sel = data.info.id_empleado == v.id ? ' selected' : '';
                            $('#select-empleados-editar').append(`<option value="${v.id}"${sel}>${v.nombre}</option>`);
                        });

                        // Reinicializar select2 con dropdownParent al modal
                        const opts = {
                            theme: "bootstrap-5",
                            dropdownParent: $('#modalEditar'),
                            language: { noResults: () => "Búsqueda no encontrada" }
                        };
                        $('#select-distrito-editar').select2(opts);
                        $('#select-unidad-editar').select2(opts);
                        $('#select-empleados-editar').select2(opts);

                    } else {
                        toastr.error('Información no encontrada');
                    }
                })
                .catch(() => { closeLoading(); toastr.error('Error al obtener información'); });
        }

        function buscarUnidadEdicion(){
            const id = document.getElementById('select-distrito-editar').value;
            if(id == '0') return;
            openLoading();
            axios.post(url+'/empleados/buscarunidad', { id })
                .then(({ data }) => {
                    closeLoading();
                    if(data.success === 1){
                        ['#select-unidad-editar','#select-empleados-editar'].forEach(s => {
                            if($(s).hasClass('select2-hidden-accessible')) $(s).select2('destroy');
                        });
                        document.getElementById("select-unidad-editar").options.length = 0;
                        document.getElementById("select-empleados-editar").options.length = 0;

                        // Opción vacía para forzar selección
                        $('#select-unidad-editar').append('<option value="" selected disabled>— Seleccionar unidad —</option>');
                        $('#select-empleados-editar').append('<option value="" selected disabled>— Seleccionar empleado —</option>');

                        data.arrayUnidad.forEach(v => {
                            $('#select-unidad-editar').append(`<option value="${v.id}">${v.nombre}</option>`);
                        });

                        const opts = { theme:"bootstrap-5", dropdownParent:$('#modalEditar'), language:{ noResults:()=>"Búsqueda no encontrada" } };
                        $('#select-unidad-editar').select2(opts);
                        $('#select-empleados-editar').select2(opts);
                    } else { toastr.error('Información no encontrada'); }
                })
                .catch(() => { closeLoading(); toastr.error('Error al buscar unidad'); });
        }

        function buscarEmpleadosEdicion(){
            const id = document.getElementById('select-unidad-editar').value;
            openLoading();
            axios.post(url+'/empleados/buscarunidad-empleado', { id })
                .then(({ data }) => {
                    closeLoading();
                    if(data.success === 1){
                        if($('#select-empleados-editar').hasClass('select2-hidden-accessible')) $('#select-empleados-editar').select2('destroy');
                        document.getElementById("select-empleados-editar").options.length = 0;
                        $('#select-empleados-editar').append('<option value="0" selected disabled>Seleccionar opción</option>');
                        data.arrayEmpleados.forEach(v => $('#select-empleados-editar').append(`<option value="${v.id}">${v.nombre}</option>`));

                        $('#select-empleados-editar').select2({ theme:"bootstrap-5", dropdownParent:$('#modalEditar'), language:{ noResults:()=>"Búsqueda no encontrada" } });
                    } else { toastr.error('Información no encontrada'); }
                })
                .catch(() => { closeLoading(); toastr.error('Error al buscar empleados'); });
        }

        function editar(){
            const id            = document.getElementById('id-editar').value;
            const fecha         = document.getElementById('fecha-editar').value;
            const descripcion   = document.getElementById('descripcion-editar').value;
            const empleado      = document.getElementById('select-empleados-editar').value;
            const materialLinea = document.getElementById('linea-editar').value;

            if(!fecha)              { toastr.error('La fecha es requerida');           return; }
            if(!empleado || empleado == '' || empleado == '0') {
                toastr.error('Debe seleccionar un empleado válido');
                return;
            }

            openLoading();
            const formData = new FormData();
            formData.append('id', id);
            formData.append('fecha', fecha);
            formData.append('descripcion', descripcion);
            formData.append('empleado', empleado);
            formData.append('linea', materialLinea);

            axios.post(url+'/historial/salidas/editar', formData)
                .then(({ data }) => {
                    closeLoading();
                    if(data.success === 1){
                        toastr.success('Actualizado correctamente');
                        $('#modalEditar').modal('hide');
                        recargar();
                    } else { toastr.error('Error al actualizar'); }
                })
                .catch(() => { closeLoading(); toastr.error('Error al actualizar'); });
        }

        function reportePDF(id){
            window.open("{{ URL::to('admin/salidas/pdfcompleto') }}/" + id);
        }
    </script>

@endsection
