<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <style>
        /* Ajuste de página para evitar cortes institucionales */
        @page { margin: 0; size: letter portrait; }
        
        body { 
            font-family: 'Helvetica', 'Arial', sans-serif; 
            margin: 0; padding: 0;
            background-color: #fff;
            color: #333;
        }

        /* Contenedor principal con el borde institucional verde */
        .border-institucional {
            border: 15px solid #004a2f;
            height: 96vh;
            margin: 2vh;
            padding: 30px;
            position: relative;
            box-sizing: border-box;
        }

        /* Encabezado con logotipos dinámicos del Controller */
        .header-logos {
            width: 100%;
            margin-bottom: 20px;
            border-collapse: collapse;
        }
        .logo-left { width: 50%; text-align: left; }
        .logo-right { width: 50%; text-align: right; }
        .img-header { height: 60px; object-fit: contain; }

        .header-text { text-align: center; margin-bottom: 30px; }
        .logo-text { font-size: 24px; font-weight: bold; color: #004a2f; text-transform: uppercase; }
        
        .content { text-align: center; margin-top: 30px; }
        .nombre-alumno { 
            font-size: 32px; font-weight: bold; 
            border-bottom: 2px solid #004a2f;
            display: inline-block; padding: 0 30px;
            margin: 15px 0;
            color: #000;
        }

        /* Pie de página de seguridad y validación */
        .footer-seguridad { 
            position: absolute;
            bottom: 30px;
            left: 40px;
            right: 40px;
            width: auto;
            border-top: 1px solid #ddd;
            padding-top: 15px;
        }

        .tabla-validacion { width: 100%; border-collapse: collapse; }
        .datos-col { width: 70%; text-align: left; vertical-align: top; }
        .qr-col { width: 30%; text-align: right; vertical-align: top; }

        .hash-cadena { 
            font-family: monospace; 
            font-size: 9px;
            color: #666; 
            word-break: break-all;
            background: #f9f9f9;
            display: block;
            margin-top: 5px;
            padding: 5px;
            border: 1px solid #eee;
        }

        .qr-img { width: 110px; height: 110px; border: 1px solid #eee; }
        .val-badge { 
            font-size: 9px; color: #004a2f; 
            margin-top: 5px; font-weight: bold; 
            text-align: center; display: block;
        }
    </style>
</head>
<body>
    <div class="border-institucional">
        
        <table class="header-logos">
            <tr>
                <td class="logo-left">
                    @if($logo_edomex)
                        <img src="{{ $logo_edomex }}" class="img-header">
                    @endif
                </td>
                <td class="logo-right">
                    @if($logo_utvt)
                        <img src="{{ $logo_utvt }}" class="img-header">
                    @endif
                </td>
            </tr>
        </table>

        <div class="header-text">
            <div class="logo-text">Universidad Tecnológica del Valle de Toluca</div>
            <p style="color: #555; margin-top: 5px; font-size: 14px;">Talleres de Actualizaciones Cuervo (TAC)</p>
        </div>

        <div class="content">
            <p style="font-size: 18px; font-style: italic; margin-bottom: 5px;">Otorga la presente</p>
            <h1 style="font-size: 48px; color: #004a2f; margin: 10px 0; font-weight: 900; letter-spacing: 2px;">CONSTANCIA</h1>
            <p style="font-size: 18px; font-style: italic; margin-top: 5px;">a:</p>
            
            <div class="nombre-alumno">{{ $alumno }}</div>

            <p style="font-size: 17px; margin: 25px 60px; line-height: 1.6; color: #444;">
                Por su destacada participación y acreditación del taller:<br>
                <strong style="color: #004a2f; font-size: 19px;">"{{ $taller }}"</strong><br>
                impartido en las instalaciones de la Universidad Tecnológica
del Valle de Toluca.
            </p>
            
            <p style="margin-top: 40px; font-weight: bold; font-size: 16px;">
                Lerma de Villada, Estado de México; a {{ $fecha }}
            </p>
        </div>

        <div class="footer-seguridad">
            <table class="tabla-validacion">
                <tr>
                    <td class="datos-col">
                        <div style="font-size: 11px; margin-bottom: 8px;">
                            <strong>CÓDIGO DE VALIDACIÓN:</strong> 
                            <span style="color: #004a2f; font-size: 13px; font-weight: bold;">{{ $codigo_validacion }}</span>
                        </div>
                        
                        <div style="font-size: 10px;">
                            <strong>FIRMA DIGITAL (HASH):</strong>
                            <span class="hash-cadena">{{ $firma_digital }}</span>
                        </div>
                        
                        <div style="font-size: 10px; margin-top: 12px; color: #555;">
                            <strong>AUTORIZADO POR:</strong> Administración TAC & Instructor ({{ $instructor }})
                        </div>
                    </td>
                    <td class="qr-col">
                        @if($qr_base64)
                            <img src="{{ $qr_base64 }}" class="qr-img">
                            <span class="val-badge">VALIDACIÓN OFICIAL</span>
                        @endif
                    </td>
                </tr>
            </table>
            <p style="font-size: 8.5px; color: #888; margin-top: 12px; font-style: italic; text-align: justify;">
                Este documento es una constancia oficial digital emitida por TAC al acreditar un cumplimiento mínimo del 80% de asistencia. La integridad y autenticidad del presente documento puede ser verificada mediante el escaneo del código QR.
            </p>
        </div>
    </div>
</body>
</html>