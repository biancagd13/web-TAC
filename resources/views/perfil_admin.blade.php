@extends('layouts.app')

@section('content')
<div class="container-fluid" style="background-color: #f8f9fa;">
    <div class="row">
        <div class="col-lg-8">
            <h2 class="fw-bold mb-1">Mi perfil</h2>
            <p class="text-muted mb-4">Panel de Administración</p>

            <div class="card border-0 shadow-sm p-4 mb-4" style="border-radius: 20px;">
                <h5 class="fw-bold mb-4">Información personal</h5>
                <div class="d-flex align-items-center mb-4">
                    <div class="position-relative">
                        <form action="{{ route('usuarios.updateFoto') }}" method="POST" enctype="multipart/form-data" id="formFotoAdmin">
                            @csrf
                            <label for="fotoInputAdmin" style="cursor: pointer;" title="Cambiar foto">
                                @if($user->foto_perfil)
                                    {{-- CORRECCIÓN: Se usa la URL directa de Cloudinary --}}
                                    <img src="{{ $user->foto_perfil }}" class="rounded-circle shadow-sm" style="width: 100px; height: 100px; object-fit: cover; border: 3px solid #0a6e0a;">
                                @else
                                    <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center fw-bold shadow-sm" style="width: 100px; height: 100px; font-size: 2.5rem; background: #0a6e0a !important;">
                                        {{ strtoupper(substr($user->nombre, 0, 1)) }}
                                    </div>
                                @endif
                                <div class="position-absolute bottom-0 end-0 bg-white rounded-circle p-1 shadow-sm border" style="width: 30px; height: 30px; display: flex; align-items: center; justify-content: center;">
                                    <i class="bi bi-camera-fill text-success"></i>
                                </div>
                            </label>
                            <input type="file" name="foto" id="fotoInputAdmin" class="d-none" onchange="document.getElementById('formFotoAdmin').submit()">
                        </form>
                    </div>
                    
                    <div class="ms-4">
                        <h4 class="fw-bold mb-0">{{ $user->nombre }}</h4>
                        <p class="text-muted mb-1">{{ $nombreRol }}</p>
                        <small class="text-muted d-block"><i class="bi bi-envelope me-2"></i>{{ $user->correo }}</small>
                        <small class="text-muted d-block"><i class="bi bi-whatsapp me-2"></i>{{ $user->telefono ?? 'Sin teléfono' }}</small>
                    </div>
                </div>
                
                <div class="row border-top pt-4">
                    <div class="col-md-6 border-end">
                        <p class="mb-0 text-muted small fw-bold text-uppercase">Jerarquía</p>
                        <div class="mt-3">
                            <small class="text-muted d-block small">Rol Asignado</small>
                            <span class="fw-bold">{{ $nombreRol }}</span>
                        </div>
                    </div>
                    <div class="col-md-6 ps-4">
                        <p class="mb-0 text-muted small fw-bold text-uppercase">&nbsp;</p>
                        <div class="mt-3">
                            <small class="text-muted d-block small">ID de Administrador</small>
                            <span class="fw-bold text-success">ADM-2026-{{ $user->ID_usuario }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm p-4" style="border-radius: 20px;">
                <h5 class="fw-bold mb-4">Estado del Sistema</h5>
                <div class="list-group list-group-flush">
                    <div class="list-group-item px-0 border-0 d-flex gap-3 mb-3">
                        <div class="bg-light p-2 rounded text-primary"><i class="bi bi-folder-check fs-4"></i></div>
                        <div>
                            <p class="mb-0 fw-bold small">Infraestructura de Talleres</p>
                            <small class="text-muted">Se están gestionando {{ $talleresGestionados }} talleres activos.</small>
                        </div>
                    </div>
                    <div class="list-group-item px-0 border-0 d-flex gap-3">
                        <div class="bg-light p-2 rounded text-success"><i class="bi bi-people-fill fs-4"></i></div>
                        <div>
                            <p class="mb-0 fw-bold small">Población Estudiantil y Docente</p>
                            <small class="text-muted">{{ $totalEstudiantes }} alumnos y {{ $instructoresActivos }} maestros operando.</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm p-4 mb-4" style="border-radius: 20px;">
                <h6 class="fw-bold mb-4 text-dark"><i class="bi bi-bar-chart-line-fill text-primary me-2"></i>Estadísticas Globales</h6>
                <div class="mb-3 border-bottom pb-2">
                    <small class="text-muted d-block">Talleres Totales</small>
                    <span class="fw-bold fs-5">{{ $talleresGestionados }}</span>
                </div>
                <div class="mb-3 border-bottom pb-2">
                    <small class="text-muted d-block">Alumnos Inscritos</small>
                    <span class="fw-bold fs-5">{{ $totalEstudiantes }}</span>
                </div>
                <div>
                    <small class="text-muted d-block">Maestros Activos</small>
                    <span class="fw-bold fs-5">{{ $instructoresActivos }}</span>
                </div>
            </div>

            <div class="card border-0 shadow p-4 mb-4 text-white" style="border-radius: 20px; background: #0a6e0a;">
                <h6 class="fw-bold mb-4 text-uppercase small"><i class="bi bi-calendar-check me-2"></i>Métricas del Mes</h6>
                <div class="d-flex justify-content-between mb-2">
                    <small>Nuevos talleres</small>
                    <span class="fw-bold">+{{ $nuevosTalleresMes }}</span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <small>Inscripciones nuevas</small>
                    <span class="fw-bold">+{{ $inscripcionesMes }}</span>
                </div>
                <div class="d-flex justify-content-between align-items-center mt-3 pt-2 border-top border-white border-opacity-10">
                    <small>Asistencia General</small>
                    <span class="fw-bold fs-4">{{ $tasaAsistencia }}%</span>
                </div>
            </div>

            <div class="card border-0 shadow-sm p-4" style="border-radius: 20px;">
                <h6 class="fw-bold mb-3 text-dark">Accesos Rápidos</h6>
                <div class="d-grid gap-2">
                    <a href="{{ route('talleres.create') }}" class="btn btn-outline-dark text-start border-0 py-2"><i class="bi bi-plus-circle me-2 text-success"></i> Crear taller</a>
                    <a href="{{ route('usuarios.index') }}" class="btn btn-outline-dark text-start border-0 py-2"><i class="bi bi-people me-2 text-primary"></i> Gestionar usuarios</a>
                    <a href="{{ route('avisos.index') }}" class="btn btn-outline-dark text-start border-0 py-2"><i class="bi bi-megaphone me-2 text-warning"></i> Gestionar avisos</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection