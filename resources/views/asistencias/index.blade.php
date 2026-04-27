@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="fw-bold mb-0">Asistencias TAC</h1>
            <p class="text-muted">Gestión de sesiones de pase de lista</p>
        </div>
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-outline-dark fw-bold rounded-pill" data-bs-toggle="modal" data-bs-target="#modalReporte">
                <i class="bi bi-file-earmark-spreadsheet"></i> Concentrado Reportes
            </button>
            <a href="{{ route('asistencias.create') }}" class="btn text-white fw-bold px-4 rounded-pill" style="background: #1e6d2a;">
                <i class="bi bi-plus-lg"></i> Nueva Sesión
            </a>
        </div>
    </div>

    @if(session('exito'))
        <div class="alert alert-success border-0 shadow-sm mb-4 rounded-4">
            {{ session('exito') }}
        </div>
    @endif

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <table class="table table-hover align-middle mb-0">
            <thead class="bg-light">
                <tr style="color: #1e6d2a;">
                    <th class="px-4">ID</th>
                    <th>Taller / Fecha</th>
                    <th>Instructor</th>
                    <th class="text-center">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($asistencias as $a)
                <tr class="border-bottom">
                    <td class="px-4 text-muted">#{{ $a->ID_asistencia }}</td>
                    <td>
                        <strong>{{ $a->imparteTaller->taller->nombre }}</strong><br>
                        <small class="text-muted"><i class="bi bi-clock-history me-1"></i>{{ date('d/m/Y H:i', strtotime($a->fecha_creacion)) }}</small>
                    </td>
                    <td>{{ $a->imparteTaller->usuario->nombre }}</td>
                    <td class="text-center">
                        <div class="btn-group shadow-sm">
                            <a href="{{ route('asistencias.show', $a->ID_asistencia) }}" class="btn btn-light border-end" title="Mostrar QR">
                                <i class="bi bi-qr-code text-success"></i>
                            </a>
                            <a href="{{ route('asistencias.lista_manual', $a->ID_asistencia) }}" class="btn btn-light border-end" title="Pase de Lista Manual">
                                <i class="bi bi-person-check-fill text-primary"></i>
                            </a>
                            <form action="{{ route('asistencias.destroy', $a->ID_asistencia) }}" method="POST" class="d-inline">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-light" onclick="return confirm('¿Eliminar sesión definitiva?')">
                                    <i class="bi bi-trash text-danger"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

{{-- Modal Reporte --}}
<div class="modal fade" id="modalReporte" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-0">
                <h5 class="fw-bold">Generar Concentrado</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="formReporte" action="" method="GET">
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Seleccionar Taller</label>
                        <select name="ID_imparte" class="form-select" required>
                            @php
                                $usuario = Auth::user();
                                $imparticiones_query = \App\Models\ImparteTaller::with('taller');
                                if($usuario->ID_rol != 3){ $imparticiones_query->where('ID_usuario', $usuario->ID_usuario); }
                                $imparticiones = $imparticiones_query->get();
                            @endphp
                            @foreach($imparticiones as $im)
                                <option value="{{ $im->ID_imparte }}">{{ $im->taller->nombre }} @if($usuario->ID_rol == 3) ({{ $im->usuario->nombre }}) @endif</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">Tipo de Reporte</label>
                        <select name="tipo_reporte" id="tipo_reporte" class="form-select" onchange="toggleFiltro()">
                            <option value="mensual">Mensual</option>
                            <option value="periodo">Por Periodo (Cuatrimestre)</option>
                        </select>
                    </div>

                    <div id="div_mes" class="mb-3">
                        <label class="form-label small fw-bold">Mes</label>
                        <select name="mes" class="form-select">
                            @for($i=1; $i<=12; $i++)
                                <option value="{{ $i }}" {{ date('m') == $i ? 'selected' : '' }}>
                                    {{ \Carbon\Carbon::create()->month($i)->translatedFormat('F') }}
                                </option>
                            @endfor
                        </select>
                    </div>

                    <div id="div_periodo" class="mb-3 d-none">
                        <label class="form-label small fw-bold">Periodo Cuatrimestral</label>
                        <select name="periodo" id="periodo_select" class="form-select" onchange="updatePeriodoNombre()">
                            <option value="01,02,03,04" data-nombre="Enero - Abril">Enero - Abril</option>
                            <option value="05,06,07,08" data-nombre="Mayo - Agosto">Mayo - Agosto</option>
                            <option value="09,10,11,12" data-nombre="Septiembre - Diciembre">Septiembre - Diciembre</option>
                        </select>
                        <input type="hidden" name="periodo_nombre" id="periodo_nombre" value="Enero - Abril">
                    </div>

                    <div class="row g-2 mt-2">
                        <div class="col-6">
                            <button type="button" onclick="generarReporte('pdf')" class="btn btn-outline-danger w-100 fw-bold rounded-pill">
                                <i class="bi bi-file-earmark-pdf"></i> PDF
                            </button>
                        </div>
                        <div class="col-6">
                            <button type="button" onclick="generarReporte('excel')" class="btn btn-success w-100 fw-bold rounded-pill" style="background: #1e6d2a; border: none;">
                                <i class="bi bi-file-earmark-spreadsheet"></i> Excel
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    function toggleFiltro() {
        const tipo = document.getElementById('tipo_reporte').value;
        document.getElementById('div_mes').classList.toggle('d-none', tipo !== 'mensual');
        document.getElementById('div_periodo').classList.toggle('d-none', tipo !== 'periodo');
    }

    function updatePeriodoNombre() {
        const select = document.getElementById('periodo_select');
        const nombre = select.options[select.selectedIndex].getAttribute('data-nombre');
        document.getElementById('periodo_nombre').value = nombre;
    }

    function generarReporte(tipo) {
        const form = document.getElementById('formReporte');
        form.action = (tipo === 'pdf') ? "{{ route('asistencias.reporte') }}" : "{{ route('asistencias.excel') }}";
        form.submit();
    }
</script>
@endsection