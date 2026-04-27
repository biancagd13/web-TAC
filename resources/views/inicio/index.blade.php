@extends('layouts.app')

@section('content')
<div class="container-fluid py-4" style="background-color: #f8f9fa;">
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="fw-bold">Bienvenido al Sistema TAC</h1>
            <p class="text-muted">Resumen general del estado de los talleres</p>
        </div>
    </div>

    <div class="row g-4 mb-4 text-center">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm p-4" style="border-radius: 15px;">
                <p class="text-muted mb-1 small uppercase fw-bold">Total talleres</p>
                <h1 class="fw-bold mb-0 text-success">{{ $totalTalleres }}</h1>
                <span class="badge bg-success-subtle text-success mt-2">Activos</span>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow-sm p-4" style="border-radius: 15px;">
                <p class="text-muted mb-1 small uppercase fw-bold">Estudiantes</p>
                <h1 class="fw-bold mb-0 text-primary">{{ $totalEstudiantes }}</h1>
                <span class="badge bg-primary-subtle text-primary mt-2">Inscritos</span>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow-sm p-4" style="border-radius: 15px;">
                <p class="text-muted mb-1 small uppercase fw-bold">Instructores</p>
                <h1 class="fw-bold mb-0 text-dark">{{ $totalInstructores }}</h1>
                <span class="badge bg-dark-subtle text-dark mt-2">Personal activo</span>
            </div>
        </div>
    </div>

    <div class="row mt-5">
        <div class="col-12 text-center">
            <p class="text-muted">Selecciona una opción del menú lateral para comenzar a trabajar.</p>
        </div>
    </div>
</div>
@endsection