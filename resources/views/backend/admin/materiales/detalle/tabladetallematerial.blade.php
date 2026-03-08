{{-- SIN @extends ni @section — se carga via $.load() --}}

@if($listado->isEmpty())
    <div class="empty-state">
        <i class="fas fa-box-open"></i>
        <p>No hay lotes con stock disponible para este material</p>
    </div>
@else
    <table id="tabla-detalle" class="table table-bordered table-hover table-sm">
        <thead class="thead-dark">
        <tr>
            <th>Lote</th>
            <th>Fecha Entrada</th>
            <th>Descripción</th>
            <th>Cant. Inicial</th>
            <th>Disponible</th>
            <th>Entregado</th>
            <th>Stock</th>
        </tr>
        </thead>
        <tbody>
        @foreach($listado as $fila)
            <tr>
                <td>
                    <span class="lote-badge">{{ $fila->lote }}</span>
                </td>
                <td>
                    <span class="fecha-chip">
                        <i class="fas fa-calendar-alt"></i> {{ $fila->fechaFormat }}
                    </span>
                </td>
                <td>{{ $fila->descripcion }}</td>
                <td>{{ $fila->cantidad_inicial }}</td>
                <td data-disponible="{{ $fila->cantidadDisponible }}">
                    <strong>{{ $fila->cantidadDisponible }}</strong>
                </td>
                <td data-entregado="{{ $fila->cantidad_entregada }}">
                    {{ $fila->cantidad_entregada }}
                </td>
                <td>
                    @if($fila->cantidadDisponible > 10)
                        <span class="stock-badge ok"><span class="dot"></span> OK</span>
                    @elseif($fila->cantidadDisponible > 0)
                        <span class="stock-badge warn"><span class="dot"></span> Bajo</span>
                    @endif
                </td>

            </tr>
        @endforeach
        </tbody>
    </table>
@endif
