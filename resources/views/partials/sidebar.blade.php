<div class="sidebar d-flex flex-column shadow" style="width: 280px; height: 100vh; background: linear-gradient(180deg, #1e6d2a 0%, #064006 100%); position: fixed; color: white; z-index: 1000;">
    <div class="p-4 text-center border-bottom border-white border-opacity-10">
        <div class="bg-white rounded-circle d-inline-flex align-items-center justify-content-center shadow-sm mb-3" 
             style="width: 120px; height: 120px; overflow: hidden; border: 3px solid rgba(255,255,255,0.3);">
            <img src="{{ asset('img/tac.png') }}" alt="TAC" 
                 style="width: 100%; height: 100%; object-fit: cover;">
        </div>
        <h5 class="fw-bold mb-0 text-uppercase tracking-wider">
            @php
                $rol = Auth::user()->ID_rol;
                $rolName = ($rol == 3) ? 'Admin' : (($rol == 2) ? 'Instructor' : 'Estudiante');
            @endphp
            {{ $rolName }}
        </h5>
    </div>

    <div class="flex-grow-1 py-3 px-2">
        <style>
            .nav-link-custom { display: flex; align-items: center; gap: 15px; color: white; text-decoration: none; padding: 12px 20px; border-radius: 10px; transition: 0.2s; margin-bottom: 5px; opacity: 0.85; }
            .nav-link-custom:hover, .nav-link-custom.active { background: rgba(255, 255, 255, 0.15); opacity: 1; font-weight: 600; }
            .nav-link-custom i { font-size: 1.25rem; }
        </style>

        {{-- PERFIL (Para todos) --}}
        <a href="{{ route('perfil') }}" class="nav-link-custom {{ request()->routeIs('perfil') ? 'active' : '' }}">
            <i class="bi bi-person-circle"></i> Mi Perfil
        </a>

        {{-- VISTA ADMINISTRADOR (Rol 3) --}}
        @if($rol == 3)
            <a href="{{ route('talleres.index') }}" class="nav-link-custom {{ request()->routeIs('talleres.*') ? 'active' : '' }}">
                <i class="bi bi-journal-bookmark-fill"></i> Gestión Talleres
            </a>

            <a href="{{ route('imparte_taller.index') }}" class="nav-link-custom {{ request()->routeIs('imparte_taller.*') ? 'active' : '' }}">
                <i class="bi bi-person-badge-fill"></i> Asignar Instructores
            </a>

            {{-- NUEVA IMPLEMENTACIÓN: CONTROL DE INSCRIPCIONES --}}
            <a href="{{ route('inscripciones.index') }}" class="nav-link-custom {{ request()->routeIs('inscripciones.*') ? 'active' : '' }}">
                <i class="bi bi-clipboard-check-fill"></i> Inscripciones
            </a>

            {{-- NUEVA IMPLEMENTACIÓN: GESTIÓN GLOBAL DE CONSTANCIAS --}}
            <a href="{{ route('constancias.index') }}" class="nav-link-custom {{ request()->routeIs('constancias.*') ? 'active' : '' }}">
                <i class="bi bi-file-earmark-pdf-fill"></i> Control Constancias
            </a>

            <a href="{{ route('usuarios.index') }}" class="nav-link-custom {{ request()->routeIs('usuarios.*') ? 'active' : '' }}">
                <i class="bi bi-people-fill"></i> Usuarios
            </a>

            <a href="{{ route('carreras.index') }}" class="nav-link-custom {{ request()->routeIs('carreras.*') ? 'active' : '' }}">
                <i class="bi bi-mortarboard-fill"></i> Carreras UTVT
            </a>
            
            <a href="{{ route('avisos.index') }}" class="nav-link-custom {{ request()->routeIs('avisos.*') ? 'active' : '' }}">
                <i class="bi bi-megaphone-fill"></i> Avisos
            </a>

        {{-- VISTA INSTRUCTOR (Rol 2) --}}
        @elseif($rol == 2)
            <a href="{{ route('asistencias.index') }}" class="nav-link-custom {{ request()->routeIs('asistencias.*') ? 'active' : '' }}">
                <i class="bi bi-qr-code-scan"></i> Pasar Asistencia
            </a>
            <a href="{{ route('constancias.maestro') }}" class="nav-link-custom {{ request()->routeIs('constancias.maestro') ? 'active' : '' }}">
                <i class="bi bi-patch-check-fill"></i> Liberar Constancias
            </a>
            <a href="{{ route('avisos.index') }}" class="nav-link-custom {{ request()->routeIs('avisos.*') ? 'active' : '' }}">
                <i class="bi bi-bell-fill"></i> Avisos
            </a>

        {{-- VISTA ESTUDIANTE (Rol 1) --}}
        @elseif($rol == 1)
            <a href="{{ route('talleres.index') }}" class="nav-link-custom {{ request()->routeIs('talleres.index') && !request()->routeIs('estudiante.talleres') ? 'active' : '' }}">
                <i class="bi bi-compass-fill"></i> Explorar Talleres
            </a>
            
            <a href="{{ route('constancias.index') }}" class="nav-link-custom {{ request()->routeIs('constancias.*') ? 'active' : '' }}">
                <i class="bi bi-file-earmark-check-fill"></i> Mis Constancias
            </a>

            <a href="{{ route('avisos.index') }}" class="nav-link-custom {{ request()->routeIs('avisos.*') ? 'active' : '' }}">
                <i class="bi bi-bell-fill"></i> Avisos
            </a>
        @endif
    </div>

    <div class="p-3 border-top border-white border-opacity-10">
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="btn w-100 text-start text-white d-flex align-items-center gap-3 py-2 border-0" style="background: rgba(220, 53, 69, 0.1);">
                <i class="bi bi-box-arrow-left text-danger fs-4"></i>
                <span class="fw-bold">Cerrar sesión</span>
            </button>
        </form>
    </div>
</div>