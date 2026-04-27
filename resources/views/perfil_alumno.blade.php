@extends('layouts.app')

@section('content')
<div class="container-fluid" style="background-color: #f8f9fa;">
    <div class="row">
        <div class="col-lg-8">
            <h2 class="fw-bold mb-1">Mi perfil</h2>
            <p class="text-muted mb-4">Información personal y progreso</p>

            <div class="card border-0 shadow-sm p-4 mb-4" style="border-radius: 20px;">
                <div class="d-flex align-items-center mb-4">
                    <div class="position-relative">
                        <form action="{{ route('usuarios.updateFoto') }}" method="POST" enctype="multipart/form-data" id="formFotoPerfil">
                            @csrf
                            <label for="fotoInput" style="cursor: pointer;" title="Haga clic para cambiar su foto">
                                @if($user->foto_perfil)
                                    {{-- CORRECCIÓN: Se usa la URL directa de Cloudinary --}}
                                    <img src="{{ $user->foto_perfil }}" class="rounded-circle shadow" style="width: 100px; height: 100px; object-fit: cover; border: 3px solid #064006;">
                                @else
                                    <div class="bg-dark text-white rounded-circle d-flex align-items-center justify-content-center fw-bold shadow" style="width: 100px; height: 100px; font-size: 2.5rem; background: #064006 !important;">
                                        {{ strtoupper(substr($user->nombre, 0, 1)) }}
                                    </div>
                                @endif
                                <div class="position-absolute bottom-0 end-0 bg-white rounded-circle p-1 shadow-sm border" style="width: 30px; height: 30px; display: flex; align-items: center; justify-content: center;">
                                    <i class="bi bi-camera-fill text-success" style="font-size: 0.9rem;"></i>
                                </div>
                            </label>
                            <input type="file" name="foto" id="fotoInput" class="d-none" onchange="document.getElementById('formFotoPerfil').submit()">
                        </form>
                    </div>

                    <div class="ms-4">
                        <h3 class="fw-bold mb-0 text-dark">{{ $user->nombre }}</h3>
                        <p class="text-primary fw-bold mb-1">Estudiante</p>
                        <small class="text-muted d-block"><i class="bi bi-envelope me-2"></i>{{ $user->correo }}</small>
                        <small class="text-muted d-block"><i class="bi bi-mortarboard me-2"></i>{{ $nombreCarrera }}</small>
                        <small class="text-muted d-block"><i class="bi bi-whatsapp me-2"></i>{{ $user->telefono ?? 'Sin teléfono registrado' }}</small>
                    </div>
                </div>
                
                <div class="row border-top pt-3">
                    <div class="col-md-6 border-end">
                        <p class="mb-0 text-muted small fw-bold text-uppercase">Información Académica</p>
                        <div class="d-flex justify-content-between mt-2">
                            <span>Talleres activos:</span>
                            <span class="fw-bold text-success">{{ $inscripcionesActivas->count() }}</span>
                        </div>
                    </div>
                    <div class="col-md-6 ps-4">
                        <p class="mb-0 text-muted small fw-bold text-uppercase">&nbsp;</p>
                        <div class="d-flex justify-content-between">
                            <span>ID DE ESTUDIANTE:</span>
                            <span class="fw-bold text-success">EST-2026-{{ $user->ID_usuario }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm p-4" style="border-radius: 20px;">
                <h5 class="fw-bold mb-4">Mis Talleres</h5>
                @forelse($talleresData as $t)
                <div class="card border-0 shadow-sm mb-3" style="border-radius: 15px; background: #fdfdfd; border: 1px solid #eee !important;">
                    <div class="row g-0 align-items-center">
                        <div class="col-md-2 text-center py-3">
                            <div class="rounded-3 d-flex align-items-center justify-content-center fw-bold text-white shadow-sm mx-auto" style="width: 70px; height: 70px; font-size: 2rem; background: #0a6e0a;">
                                {{ $t['inicial'] }}
                            </div>
                        </div>
                        <div class="col-md-10 p-3">
                            <div class="d-flex justify-content-between">
                                <h5 class="fw-bold mb-1">{{ $t['nombre'] }}</h5>
                                <span class="badge bg-white text-dark border px-3 shadow-sm">Activo</span>
                            </div>
                            <p class="small text-muted mb-2"><i class="bi bi-clock me-2"></i>{{ $t['horario'] ?? 'Sin horario' }}</p>
                            
                            <div class="row align-items-center">
                                <div class="col-7">
                                    <div class="progress" style="height: 10px; border-radius: 10px; background: #e9ecef;">
                                        <div class="progress-bar" style="width: {{ $t['asistencia'] }}%; background: #0a6e0a;"></div>
                                    </div>
                                    <small class="text-muted mt-1 d-block">{{ $t['asistencia'] }}% de asistencia real</small>
                                </div>
                                <div class="col-5 d-flex justify-content-end gap-3 text-center">
                                    <div>
                                        <div class="rounded-circle border border-success border-4 d-flex align-items-center justify-content-center fw-bold" style="width: 45px; height: 45px; font-size: 0.7rem; color: #0a6e0a;">{{ $t['asistencia'] }}%</div>
                                        <small class="text-muted" style="font-size: 0.5rem;">Asistencias</small>
                                    </div>
                                    <div>
                                        <div class="rounded-circle border border-warning border-4 d-flex align-items-center justify-content-center fw-bold" style="width: 45px; height: 45px; font-size: 0.7rem; color: #f39c12;">{{ $t['progreso'] }}%</div>
                                        <small class="text-muted" style="font-size: 0.5rem;">Progreso</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @empty
                <p class="text-muted text-center py-4">No hay talleres inscritos en la base de datos.</p>
                @endforelse
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm p-4 mb-4" style="border-radius: 20px;">
                <h6 class="fw-bold mb-4 text-dark"><i class="bi bi-patch-check-fill me-2 text-success"></i>Logros</h6>
                @if($logros['primera_clase'])
                <div class="mb-3">
                    <p class="mb-0 fw-bold small"><i class="bi bi-bullseye text-danger me-2"></i>Primera clase completada</p>
                </div>
                @endif
                @if($logros['multitaller'])
                <div class="mb-1">
                    <p class="mb-0 fw-bold small"><i class="bi bi-fire text-warning me-2"></i>Alumno Destacado</p>
                    <small class="text-muted ps-4">Inscrito en varios talleres</small>
                </div>
                @endif
            </div>

            <div class="card border-0 shadow p-4 mb-4 text-white" style="border-radius: 20px; background: #0a6e0a;">
                <h6 class="fw-bold mb-4 text-uppercase small">Mi progreso</h6>
                <div class="d-flex justify-content-between mb-3 border-bottom border-white border-opacity-10 pb-2">
                    <span>Clases asistidas</span>
                    <span class="fw-bold fs-5">{{ $clasesTotales }}</span>
                </div>
                <div class="d-flex justify-content-between align-items-center">
                    <span>Asistencia Global</span>
                    <span class="fw-bold fs-3">{{ round($promedioAsistencia) }}%</span>
                </div>
                <p class="small mt-4 mb-0 text-white-50">* Necesitas 80% para constancia</p>
            </div>

            <div class="card border-0 shadow-sm p-4" style="border-radius: 20px; background: #e8f5e9;">
                <h6 class="fw-bold mb-3 text-dark">Próximas sesiones</h6>
                @foreach($talleresData->take(2) as $tp)
                    <div class="bg-white p-3 rounded-3 mb-2 shadow-sm border-start border-success border-4">
                        <p class="mb-0 small fw-bold text-dark">{{ $tp['nombre'] }}</p>
                        <small class="text-muted"><i class="bi bi-clock me-1"></i>{{ $tp['horario'] }}</small>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection