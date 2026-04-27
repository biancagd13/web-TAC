<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Login TAC - Sistema TAC</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { margin: 0; height: 100vh; font-family: 'Segoe UI', sans-serif; background: #ffffff; }
        .container-login { display: flex; width: 100%; height: 100vh; }

        /* PANEL IZQUIERDO - Usando el verde exacto de tu imagen */
        .left {
            width: 50%;
            background-color: #1e6d2a; 
            color: white;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            padding: 60px;
        }
        
        /* LOGO CIRCULAR EN LOGIN */
        .left img { 
            width: 180px; 
            height: 180px; /* Alto y ancho iguales para círculo perfecto */
            object-fit: cover; /* Evita que la imagen se estire */
            border-radius: 50%; /* Hace la imagen circular */
            border: 4px solid rgba(255, 255, 255, 0.2); /* Borde elegante */
            margin-bottom: 25px; 
            filter: drop-shadow(0 5px 15px rgba(0,0,0,0.2));
        }
        
        .info-box {
            margin-top: 35px; padding: 25px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 20px; width: 340px;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        .info-item { display: flex; align-items: center; margin: 15px 0; font-size: 1rem; }
        .info-item i { margin-right: 15px; font-size: 1.3rem; color: #ffffff; opacity: 0.9; } 

        /* PANEL DERECHO */
        .right { width: 50%; display: flex; justify-content: center; align-items: center; background-color: #f8f9fa; }
        .login-card {
            width: 440px; background: white; padding: 50px;
            border-radius: 25px; box-shadow: 0 20px 40px rgba(0, 0, 0, 0.05);
        }
        
        /* Inputs con acento en el verde de tu imagen al hacer focus */
        .form-control { border-radius: 12px; padding: 12px; border: 1px solid #dee2e6; transition: 0.3s; }
        .form-control:focus { 
            border-color: #1e6d2a; 
            box-shadow: 0 0 0 0.25rem rgba(30, 109, 42, 0.15); 
        }

        /* Botón principal usando el verde exacto de tu imagen */
        .btn-green {
            background-color: #1e6d2a; color: white; border: none; padding: 14px;
            border-radius: 12px; font-weight: 700; transition: all 0.3s ease;
            text-transform: uppercase; letter-spacing: 1px;
        }
        .btn-green:hover { 
            background-color: #16531f; 
            transform: translateY(-2px); 
            box-shadow: 0 5px 15px rgba(30, 109, 42, 0.3);
        }

        /* Título en el mismo verde */
        .text-green-tac { color: #1e6d2a; }

        .alert-fixed {
            position: fixed; top: 20px; left: 50%; transform: translateX(-50%);
            z-index: 9999; width: 90%; max-width: 400px; border-radius: 15px;
        }
    </style>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
</head>
<body>

    @if ($errors->any())
    <div class="alert alert-danger alert-fixed alert-dismissible fade show shadow border-0" role="alert" style="background-color: #f8d7da; color: #842029;">
        <ul class="mb-0 small">
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <div class="container-login">
        <div class="left d-none d-lg-flex">
            <img src="{{ asset('img/tac.png') }}" alt="Logo TAC">
            
            <h2 class="fw-bold text-uppercase" style="letter-spacing: 2px;">SISTEMA TAC</h2>
            <p class="opacity-75">Taller de Actualizaciones Cuervos</p>

            <div class="info-box text-start">
                <div class="info-item"><i class="bi bi-trophy-fill"></i><span>Talleres deportivos</span></div>
                <div class="info-item"><i class="bi bi-palette-fill"></i><span>Talleres creativos</span></div>
                <div class="info-item"><i class="bi bi-clock-history"></i><span>Control de Horarios</span></div>
                <div class="info-item"><i class="bi bi-qr-code-scan"></i><span>Asistencia por QR</span></div>
            </div>
        </div>

        <div class="right">
            <div class="login-card">
                <h3 class="mb-1 fw-bold text-green-tac">¡Bienvenido!</h3>
                <p class="mb-4 text-muted small">Ingresa tus credenciales institucionales para continuar</p>

                <form action="{{ url('/login') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-secondary text-uppercase" style="font-size: 0.7rem;">Correo Institucional</label>
                        <input type="email" name="correo" class="form-control" 
                               value="{{ old('correo') }}" placeholder="ejemplo@utvt.edu.mx" required>
                    </div>

                    <div class="mb-4">
                        <label class="form-label small fw-bold text-secondary text-uppercase" style="font-size: 0.7rem;">Contraseña</label>
                        <input type="password" name="password" class="form-control" 
                               placeholder="••••••••" required>
                    </div>

                    <button type="submit" class="btn btn-green w-100 shadow-sm py-3 mt-2">
                        Iniciar Sesión
                    </button>
                </form>

                <div class="text-center mt-5 text-muted small border-top pt-4">
                    UTVT - Sistema TAC © 2026
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>