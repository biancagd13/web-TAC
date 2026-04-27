@extends('layouts.app')

@section('content')
<div class="container-fluid py-4" style="background-color: #ffffff; min-height: 100vh;">
    <div class="mb-5 ps-4">
        <h1 class="fw-bold" style="font-size: 3rem; color: #000;">Nuevo Taller</h1>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger mx-4 shadow-sm" style="border-radius: 15px;">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="row justify-content-center">
        <div class="col-md-8">
            {{-- SE AGREGA EL ID PARA EL SCRIPT --}}
            <form id="formTaller" action="{{ route('talleres.store') }}" method="POST" class="px-4">
                @csrf 

                <div class="row mb-4 align-items-center">
                    <label class="col-md-4 fw-bold fs-5 text-end pe-5">Nombre del taller</label>
                    <div class="col-md-8">
                        <input type="text" name="nombre" value="{{ old('nombre') }}" class="form-control rounded-pill py-2 px-4 shadow-sm" required>
                    </div>
                </div>

                <div class="row mb-4 align-items-center">
                    <label class="col-md-4 fw-bold fs-5 text-end pe-5">Descripción</label>
                    <div class="col-md-8">
                        <textarea name="detalle" class="form-control shadow-sm" style="border-radius: 20px; min-height: 100px;" required>{{ old('detalle') }}</textarea>
                    </div>
                </div>

                <div class="row mb-4 align-items-center">
                    <label class="col-md-4 fw-bold fs-5 text-end pe-5">Activo</label>
                    <div class="col-md-8">
                        <select name="activo" class="form-select rounded-pill shadow-sm" required>
                            <option value="1">Activo</option>
                            <option value="0">Inactivo</option>
                        </select>
                    </div>
                </div>

                <div class="row mb-4">
                    <label class="col-md-4 fw-bold fs-5 text-end pe-5 pt-2">Horario</label>
                    <div class="col-md-8">
                        <div class="d-flex flex-wrap gap-2 mb-3">
                            @foreach(['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes'] as $dia)
                                <div class="form-check">
                                    <input class="form-check-input check-dia" type="checkbox" value="{{ $dia }}" id="dia{{ $dia }}">
                                    <label class="form-check-label small fw-bold" for="dia{{ $dia }}">{{ $dia }}</label>
                                </div>
                            @endforeach
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <select id="h_inicio" class="form-select form-select-sm rounded-pill w-auto shadow-sm">
                                @for($i=7; $i<=12; $i++) <option value="{{ sprintf('%02d', $i) }}">{{ sprintf('%02d', $i) }}</option> @endfor
                            </select>
                            <select id="m_inicio" class="form-select form-select-sm rounded-pill w-auto shadow-sm">
                                @for($i=0; $i<=55; $i+=5) <option value="{{ sprintf('%02d', $i) }}">{{ sprintf('%02d', $i) }}</option> @endfor
                            </select>
                            <span class="small text-muted mx-1">am. </span>
                            <span class="small text-muted mx-1">a</span>
                            <select id="h_fin" class="form-select form-select-sm rounded-pill w-auto shadow-sm">
                                @for($i=12; $i<=20; $i++) <option value="{{ sprintf('%02d', $i) }}">{{ sprintf('%02d', $i) }}</option> @endfor
                            </select>
                            <select id="m_fin" class="form-select form-select-sm rounded-pill w-auto shadow-sm">
                                @for($i=0; $i<=55; $i+=5) <option value="{{ sprintf('%02d', $i) }}">{{ sprintf('%02d', $i) }}</option> @endfor
                            </select>
                            <span class="small text-muted mx-1">pm.</span>
                        </div>
                        {{-- ESTE ES EL CAMPO QUE RECIBE EL VALOR PARA LA BD --}}
                        <input type="hidden" name="horario" id="input_horario" value="Pendiente">
                    </div>
                </div>

                <div class="row mb-5 align-items-center">
                    <label class="col-md-4 fw-bold fs-5 text-end pe-5">Cupos</label>
                    <div class="col-md-8">
                        <input type="number" name="cupo" value="{{ old('cupo', 20) }}" min="1" oninput="if(this.value < 1) this.value = 1;" class="form-control rounded-pill shadow-sm py-2 px-4" required>
                    </div>
                </div>

                <div class="row justify-content-center mt-5">
                    <div class="col-md-6 text-center">
                        {{-- CAMBIAMOS A TYPE="BUTTON" PARA PROCESAR EL JS PRIMERO --}}
                        <button type="button" onclick="procesarEnvio()" class="btn btn-success w-100 fw-bold mb-3 shadow rounded-4 p-3" style="background-color: #0a6e0a; border: none;">
                            Guardar Taller
                        </button>
                        <a href="{{ route('talleres.index') }}" class="btn btn-secondary w-100 fw-bold shadow-sm rounded-4 p-3" style="background-color: #7bab7b; color: white; border: none; text-decoration: none; display: block;">
                            Regresar
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function procesarEnvio() {
        let dias = [];
        document.querySelectorAll('.check-dia:checked').forEach(cb => dias.push(cb.value));
        
        let hi = document.getElementById('h_inicio').value + ":" + document.getElementById('m_inicio').value;
        let hf = document.getElementById('h_fin').value + ":" + document.getElementById('m_fin').value;
        
        if(dias.length > 0) {
            document.getElementById('input_horario').value = dias.join(', ') + " de " + hi + " a " + hf;
        } else {
            document.getElementById('input_horario').value = "Sin horario definido";
        }

        // Enviamos el formulario manualmente tras actualizar el campo hidden
        document.getElementById('formTaller').submit();
    }
</script>
@endsection