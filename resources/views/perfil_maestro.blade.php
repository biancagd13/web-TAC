@extends('layouts.app')

@section('content')
<div class="container-fluid" style="background-color: #f8f9fa;">
    <div class="row">
        <div class="col-lg-8">
            <h2 class="fw-bold mb-1">Mi perfil</h2>
            <p class="text-muted mb-4">Instructor de talleres</p>

            <div class="card border-0 shadow-sm p-4 mb-4" style="border-radius: 20px;">
                <h5 class="fw-bold mb-4 text-dark">Información personal</h5>
                <div class="d-flex align-items-center mb-4">
                    <div class="position-relative">
                        <form action="{{ route('usuarios.updateFoto') }}" method="POST" enctype="multipart/form-data" id="formFotoInstructor">
                            @csrf
                            <label for="fotoInputInstructor" style="cursor: pointer;" title="Cambiar foto">
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
                            <input type="file" name="foto" id="fotoInputInstructor" class="d-none" onchange="document.getElementById('formFotoInstructor').submit()">
                        </form>
                    </div>

                    <div class="ms-4">
                        <h4 class="fw-bold mb-0">{{ $user->nombre }}</h4>
                        <p class="text-muted mb-1">Instructor</p>
                        <small class="text-muted d-block"><i class="bi bi-envelope me-2"></i>{{ $user->correo }}</small>
                        <small class="text-muted d-block"><i class="bi bi-whatsapp me-2"></i>{{ $user->telefono ?? 'Sin teléfono' }}</small>
                    </div>
                </div>
                
                <div class="row border-top pt-4">
                    <div class="col-md-6 border-end">
                        <p class="mb-0 text-muted small fw-bold text-uppercase">Informacion Profesional</p>
                        <div class="mt-3">
                            <small class="text-muted d-block small">Especialidad (Talleres)</small>
                            <span class="fw-bold text-wrap">{{ $especialidad }}</span>
                        </div>
                    </div>
                    <div class="col-md-6 ps-4">
                        <p class="mb-0 text-muted small fw-bold text-uppercase">&nbsp;</p>
                        <div class="mt-3">
                            <small class="text-muted d-block small">ID de Instructor</small>
                            <span class="fw-bold text-success">INS-2026-{{ $user->ID_usuario }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm p-4" style="border-radius: 20px;">
                <h5 class="fw-bold mb-4 text-dark">Talleres que imparto</h5>
                
                @forelse($talleresImpartidos as $ti)
                <div class="card border-0 shadow-sm mb-3 bg-light" style="border-radius: 15px;">
                    <div class="p-3 d-flex align-items-center">
                        <div class="bg-white rounded-3 d-flex align-items-center justify-content-center shadow-sm me-3" style="width: 60px; height: 60px;">
                            <span class="fw-bold text-success fs-4">{{ strtoupper(substr($ti->taller->nombre, 0, 1)) }}</span>
                        </div>
                        <div class="flex-grow-1">
                            <div class="d-flex justify-content-between">
                                <h6 class="fw-bold mb-1">{{ $ti->taller->nombre }}</h6>
                                <span class="badge" style="background: #efb88d; color: #854d0e;">Deportivo</span>
                            </div>
                            <div class="d-flex gap-4 mt-2">
                                <small class="text-muted"><i class="bi bi-clock me-1"></i>{{ $ti->taller->horario }}</small>
                                <small class="text-muted"><i class="bi bi-calendar-event me-1"></i>{{ $ti->periodo }}</small>
                            </div>
                        </div>
                    </div>
                </div>
                @empty
                <p class="text-muted text-center py-3">No hay talleres asignados en la base de datos.</p>
                @endforelse
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm p-4 mb-4" style="border-radius: 20px;">
                <h6 class="fw-bold mb-4 text-dark"><i class="bi bi-bar-chart-line-fill text-primary me-2"></i>Estadísticas</h6>
                <div class="mb-3 border-bottom pb-2">
                    <small class="text-muted d-block">Tus estudiantes (inscritos)</small>
                    <span class="fw-bold fs-5">{{ $totalMisEstudiantes }}</span>
                </div>
                <div class="mb-3 border-bottom pb-2">
                    <small class="text-muted d-block">Talleres activos sistema</small>
                    <span class="fw-bold fs-5">{{ $talleresActivosSistema }}</span>
                </div>
                <div>
                    <small class="text-muted d-block">Estudiantes globales</small>
                    <span class="fw-bold fs-5">{{ $totalEstudiantesSistema }}</span>
                </div>
            </div>

            <div class="card border-0 shadow p-4 mb-4 text-white" style="border-radius: 20px; background: #0a6e0a;">
                <h6 class="fw-bold mb-4 text-uppercase small" style="letter-spacing: 1px;"><i class="bi bi-calendar-check me-2"></i>Este mes</h6>
                <div class="d-flex justify-content-between mb-3 border-bottom border-white border-opacity-10 pb-2">
                    <span>Sesiones impartidas</span>
                    <span class="fw-bold fs-5">{{ $sesionesImpartidas }}</span>
                </div>
                <div class="d-flex justify-content-between align-items-center">
                    <span>Asistencia promedio</span>
                    <span class="fw-bold fs-3">{{ $promedioAsistenciaMaestro }}%</span>
                </div>
                <p class="small mt-3 mb-0 text-white-50">* Datos basados en tus listas de asistencia mensuales</p>
            </div>

            <div class="card border-0 shadow-sm p-4 mb-4" style="border-radius: 20px;">
                <h6 class="fw-bold mb-4 text-dark">Logros Recientes</h6>
                <div class="d-flex gap-3 mb-3">
                    <i class="bi bi-trophy-fill text-warning fs-4"></i>
                    <div>
                        <p class="mb-0 fw-bold small">Instructor Activo</p>
                        <small class="text-muted">Asignación verificada</small>
                    </div>
                </div>
                @if($totalMisEstudiantes > 20)
                <div class="d-flex gap-3">
                    <i class="bi bi-people-fill text-primary fs-4"></i>
                    <div>
                        <p class="mb-0 fw-bold small">Grupo Numeroso</p>
                        <small class="text-muted">+20 alumnos a cargo</small>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection