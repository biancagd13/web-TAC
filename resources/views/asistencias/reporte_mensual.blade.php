<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: 'Helvetica', Arial, sans-serif; font-size: 10px; color: #333; }
        .header { text-align: center; border-bottom: 3px solid #1e6d2a; padding: 10px; margin-bottom: 20px; }
        .title { font-size: 16px; font-weight: bold; color: #1e6d2a; text-transform: uppercase; }
        table { width: 100%; border-collapse: collapse; table-layout: fixed; }
        th, td { border: 1px solid #dee2e6; padding: 5px; text-align: center; }
        th { background-color: #1e6d2a; color: white; font-size: 8px; }
        .col-alumno { width: 22%; text-align: left; padding-left: 10px; background-color: #f8f9fa; }
        .presente { color: #1e6d2a; font-weight: bold; }
        .footer { position: fixed; bottom: 10px; width: 100%; text-align: center; font-size: 8px; color: #888; }
    </style>
</head>
<body>
    <div class="header">
        <div class="title">Concentrado de Asistencia - {{ $tipo == 'mensual' ? 'Mensual' : 'Cuatrimestral' }}</div>
        <div style="margin-top: 5px;">
            <strong>Taller:</strong> {{ $imparte->taller->nombre }} | 
            <strong>{{ $tituloFecha }}</strong> | 
            <strong>Instructor:</strong> {{ $imparte->usuario->nombre }}
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th class="col-alumno">Nombre del Alumno</th>
                @foreach($columnas as $col)
                    <th>{{ strtoupper($col['label']) }}</th>
                @endforeach
                <th style="background-color: #064006; width: 50px;">TOTAL</th>
            </tr>
        </thead>
        <tbody>
            @php $granTotal = 0; @endphp
            @foreach($alumnos as $al)
                <tr>
                    <td class="col-alumno">{{ $al->usuario->nombre }}</td>
                    @php $filaTotal = 0; @endphp
                    
                    @foreach($columnas as $col)
                        @php
                            if($tipo == 'mensual') {
                                // Buscamos si asistió a la sesión específica
                                $asistio = \App\Models\DetalleAsistencia::where('ID_asistencia', $col['id'])
                                            ->where('ID_usuario', $al->ID_usuario)->exists();
                                $val = $asistio ? 'X' : '-';
                                if($asistio) $filaTotal++;
                            } else {
                                // Contamos asistencias en ese mes específico del cuatrimestre
                                $val = \App\Models\DetalleAsistencia::whereHas('asistencia', function($q) use ($col, $imparte) {
                                            $q->where('ID_imparte', $imparte->ID_imparte)
                                              ->whereMonth('fecha_creacion', $col['id']);
                                        })->where('ID_usuario', $al->ID_usuario)->count();
                                $filaTotal += $val;
                            }
                        @endphp
                        <td class="{{ ($tipo == 'mensual' && $val == 'X') ? 'presente' : '' }}">
                            {{ $val }}
                        </td>
                    @endforeach
                    
                    <td style="font-weight: bold; background-color: #f1f8f1;">{{ $filaTotal }}</td>
                    @php $granTotal += $filaTotal; @endphp
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr style="background-color: #f8f9fa; font-weight: bold;">
                <td class="col-alumno" style="text-align: right;">GRAN TOTAL:</td>
                @foreach($columnas as $col) <td></td> @endforeach
                <td style="background-color: #d4edda; color: #155724;">{{ $granTotal }}</td>
            </tr>
        </tfoot>
    </table>

    <div class="footer">Sistema TAC - Universidad Tecnológica del Valle de Toluca - Generado el {{ date('d/m/Y H:i') }}</div>
</body>
</html>