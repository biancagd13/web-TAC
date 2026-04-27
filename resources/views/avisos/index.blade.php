@extends('layouts.app')

@section('content')
<div class="container-fluid py-4" style="background-color: #ffffff;">

    @if(session('exito'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert" style="border-radius: 15px;">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('exito') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4" role="alert" style="border-radius: 15px;">
            <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- BARRA DE FILTROS INTELIGENTES MEJORADA --}}
    <div class="card border-0 shadow-sm mb-4 p-3" style="border-radius: 20px; background-color: #f8f9fa;">
        <form action="{{ route('avisos.index') }}" method="GET" class="row g-3 align-items-center">
            <div class="col-md-3">
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0" style="border-radius: 15px 0 0 15px;"><i class="bi bi-search"></i></span>
                    <input type="text" name="buscar" class="form-control border-start-0" placeholder="Buscar texto..." value="{{ $buscar ?? '' }}" style="border-radius: 0 15px 15px 0;">
                </div>
            </div>

            {{-- NUEVA IMPLEMENTACIÓN: FILTRO POR TALLER --}}
            <div class="col-md-3">
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0" style="border-radius: 15px 0 0 15px;"><i class="bi bi-tag"></i></span>
                    <select name="taller_id" class="form-select border-start-0" style="border-radius: 0 15px 15px 0;">
                        <option value="">Todos</option>
                        <option value="general" {{ request('taller_id') == 'general' ? 'selected' : '' }}> Generales</option>
                        @foreach(App\Models\Taller::where('activo', 1)->get() as $t)
                            <option value="{{ $t->ID_taller }}" {{ request('taller_id') == $t->ID_taller ? 'selected' : '' }}>
                                {{ $t->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="col-md-3">
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0" style="border-radius: 15px 0 0 15px;"><i class="bi bi-calendar-event"></i></span>
                    <input type="date" name="fecha" class="form-control border-start-0" value="{{ $fecha ?? '' }}" style="border-radius: 0 15px 15px 0;">
                </div>
            </div>

            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-success w-100 fw-bold" style="border-radius: 15px; background-color: #1e6d2a;">Filtrar</button>
                <a href="{{ route('avisos.index') }}" class="btn btn-outline-secondary w-100 fw-bold" style="border-radius: 15px;">Limpiar</a>
            </div>
        </form>
    </div>

    @if(Auth::user()->ID_rol == 1)
        {{-- VISTA ESTUDIANTES (Mantenida) --}}
        <div class="mb-4 d-flex align-items-center">
            <i class="bi bi-bell-fill text-warning fs-1 me-3"></i>
            <div>
                <h1 class="fw-bold mb-0" style="font-size: 2.5rem;">Avisos y Notificaciones</h1>
                <p class="text-muted fs-5">Información oficial de la administración y tus profesores</p>
            </div>
        </div>

        <div class="row">
            <div class="col-md-8">
                <div class="card border-0 shadow-sm p-4 mb-4" style="border-radius: 25px; background-color: #f1f3f4;">
                    <h4 class="fw-bold mb-4"><i class="bi bi-megaphone-fill me-2 text-dark"></i> Avisos Generales</h4>
                    @forelse($avisosGenerales as $aviso)
                    <div class="card border-0 mb-3 p-3 shadow-sm" style="border-radius: 20px; background-color: #e9f5e9;">
                        <div class="d-flex justify-content-between">
                            <div class="d-flex">
                                <i class="bi bi-pin-angle-fill text-danger fs-4 me-3"></i>
                                <div>
                                    <h5 class="fw-bold mb-1">{{ $aviso->titulo }}</h5>
                                    <p class="text-secondary small mb-2">{{ $aviso->contenido }}</p>
                                    <div class="d-flex align-items-center gap-3">
                                        <small class="text-muted"><i class="bi bi-shield-lock me-1"></i> Administración</small>
                                        <small class="text-muted"><i class="bi bi-calendar3 me-1"></i> {{ $aviso->fecha_publicacion }}</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-4 text-muted small">No se encontraron comunicados generales.</div>
                    @endforelse
                </div>

                <div class="card border-0 shadow-sm p-4" style="border-radius: 25px; background-color: #f1f3f4;">
                    <h4 class="fw-bold mb-4"><i class="bi bi-person-video3 me-2 text-dark"></i> Avisos de tus Instructores</h4>
                    @forelse($avisosPorTaller as $aviso)
                    <div class="card border-0 mb-3 p-3 shadow-sm" style="border-radius: 20px; background-color: #e0f2f1;">
                        <div class="d-flex justify-content-between">
                            <div class="d-flex">
                                <i class="bi bi-chat-left-text-fill text-primary fs-4 me-3"></i>
                                <div>
                                    <h5 class="fw-bold mb-1">{{ $aviso->titulo }}</h5>
                                    <p class="text-secondary small mb-2">{{ $aviso->contenido }}</p>
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="badge rounded-pill px-3 py-1" style="background-color: #00897b; color: #fff;">
                                            {{ $aviso->taller->nombre ?? 'General' }}
                                        </span>
                                        <small class="text-muted ms-2"><i class="bi bi-person me-1"></i> {{ $aviso->usuario->nombre }}</small>
                                        <small class="text-muted ms-2"><i class="bi bi-calendar3 me-1"></i> {{ $aviso->fecha_publicacion }}</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-4 text-muted small">No se encontraron avisos de instructores.</div>
                    @endforelse
                </div>
            </div>

            <div class="col-md-4">
                <div class="card border-0 shadow-sm p-4 mb-4" style="border-radius: 20px; background-color: #dcedc8;">
                    <h6 class="fw-bold mb-3 text-center"><i class="bi bi-patch-check me-1"></i> Mis Talleres Activos</h6>
                    @forelse($misTalleres as $item)
                        <div class="d-flex justify-content-between align-items-center bg-white p-2 rounded-pill mb-2 px-3 shadow-sm">
                            <span class="small fw-bold">{{ $item->taller->nombre }}</span>
                            <i class="bi bi-check-lg text-success"></i>
                        </div>
                    @empty
                        <p class="text-center text-muted small">No estás inscrito.</p>
                    @endforelse
                </div>
            </div>
        </div>

    @else
        {{-- VISTA ADMIN / INSTRUCTOR MEJORADA --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="fw-bold mb-0">Tablero de Avisos - UTVT</h1>
                <p class="text-muted small">Control de avisos del sistema</p>
            </div>
            <a href="{{ route('avisos.create') }}" class="btn btn-success fw-bold px-4 rounded-pill shadow-sm" style="background-color: #1e6d2a;">
                <i class="bi bi-plus-lg me-1"></i> Nuevo Aviso
            </a>
        </div>

        <div class="table-responsive shadow-sm rounded-4 overflow-hidden">
            <table class="table table-hover align-middle bg-white mb-0">
                <thead class="bg-light">
                    <tr class="text-success">
                        <th class="border-0 px-4">ID</th>
                        <th class="border-0">Título</th>
                        <th class="border-0">Taller</th> {{-- NUEVA COLUMNA --}}
                        <th class="border-0">Publicado por</th>
                        <th class="border-0">Rol</th>
                        <th class="border-0">Fecha</th>
                        <th class="border-0 text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($avisos as $aviso)
                    <tr class="border-bottom">
                        <td class="px-4 text-muted">{{ $aviso->ID_aviso }}</td>
                        <td class="fw-bold">{{ $aviso->titulo }}</td>
                        
                        {{-- MOSTRAR EL TALLER O GENERAL --}}
                        <td>
                            @if($aviso->ID_taller)
                                <span class="badge bg-outline-success text-success border border-success px-3">
                                    {{ $aviso->taller->nombre }}
                                </span>
                            @else
                                <span class="badge bg-light text-muted border px-3">General</span>
                            @endif
                        </td>

                        <td><i class="bi bi-person-circle me-1"></i> {{ $aviso->usuario->nombre }}</td>
                        <td>
                            <span class="badge rounded-pill shadow-sm" 
                                  style="background-color: {{ $aviso->usuario->ID_rol == 3 ? '#b0b0b0' : '#f6c98e' }} !important; color: white; padding: 8px 15px; font-weight: 600;">
                                {{ $aviso->usuario->rol->nombre ?? ($aviso->usuario->ID_rol == 3 ? 'Admin' : 'Instructor') }}
                            </span>
                        </td>
                        <td><i class="bi bi-calendar3 me-1"></i> {{ $aviso->fecha_publicacion }}</td>
                        <td class="text-center">
                            <div class="d-flex justify-content-center gap-2">
                                @php
                                    $tallerTxt = $aviso->ID_taller ? "*Taller:* {$aviso->taller->nombre}\n" : "";
                                    $textoWA = urlencode("*TAC - NUEVO AVISO*\n\n{$tallerTxt}*Asunto:* {$aviso->titulo}\n\n{$aviso->contenido}");
                                @endphp
                                <a href="https://wa.me/?text={{ $textoWA }}" target="_blank" class="btn btn-sm btn-success border shadow-sm" title="Compartir en WhatsApp">
                                    <i class="bi bi-whatsapp"></i>
                                </a>

                                @if(Auth::user()->ID_usuario == $aviso->ID_usuario)
                                <a href="{{ route('avisos.edit', $aviso->ID_aviso) }}" class="btn btn-sm btn-light border shadow-sm">
                                    <i class="bi bi-pencil-square text-secondary"></i>
                                </a>
                                @endif

                                @if(Auth::user()->ID_usuario == $aviso->ID_usuario || Auth::user()->ID_rol == 3)
                                <form action="{{ route('avisos.destroy', $aviso->ID_aviso) }}" method="POST" style="display:inline;">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-light border shadow-sm" onclick="return confirm('¿Eliminar este aviso?')">
                                        <i class="bi bi-trash3 text-danger"></i>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted">No se encontraron avisos con los filtros aplicados.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection