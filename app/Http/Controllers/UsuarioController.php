<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use App\Models\Rol;
use App\Models\Taller; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash; 
use Illuminate\Support\Facades\Auth; 
use Illuminate\Support\Facades\Storage;
// IMPORTANTE: Fachada de Cloudinary
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Cloudinary\Configuration\Configuration;

class UsuarioController extends Controller
{
    public function index(Request $request)
    {
        $buscar = $request->get('buscar');

        $usuarios = Usuario::with('rol')
            ->when($buscar, function ($query, $buscar) {
                return $query->where('nombre', 'LIKE', '%' . $buscar . '%')
                             ->orWhere('correo', 'LIKE', '%' . $buscar . '%');
            })->get();

        $totalTalleres = Taller::count();
        $totalUsuarios = $usuarios->count();
        $totalEstudiantes = Usuario::where('ID_rol', 1)->count();
        $totalInstructores = Usuario::where('ID_rol', 2)->count();

        return view('usuarios.index', compact(
            'usuarios', 
            'buscar', 
            'totalTalleres', 
            'totalUsuarios', 
            'totalEstudiantes', 
            'totalInstructores'
        ));
    }

    public function misTalleres()
    {
        $rol = Auth::user()->ID_rol;
        if ($rol == 1) {
            return redirect()->route('talleres.index');
        }
        return redirect()->route('inicio');
    }

    public function create()
    {
        $roles = Rol::all();
        return view('usuarios.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre'      => 'required|string|max:100',
            'correo'      => 'required|email|max:100|unique:usuarios,correo',
            'password'    => 'required|string|min:8', 
            'ID_rol'      => 'required|exists:rol,ID_rol',
            'activo'      => 'required|in:0,1',
            'telefono'    => 'nullable|string|max:20', 
            'foto_perfil' => 'nullable|string'         
        ]);

        $datos = $request->only(['nombre', 'correo', 'ID_rol', 'activo', 'telefono', 'foto_perfil']);
        $datos['password'] = Hash::make($request->password);
        $datos['activo'] = (int) $request->activo;

        Usuario::create($datos);

        return redirect()->route('usuarios.index')->with('exito', 'Usuario registrado con éxito.');
    }

    public function edit(Usuario $usuario)
    {
        $roles = Rol::all();
        return view('usuarios.edit', compact('usuario', 'roles'));
    }

    public function update(Request $request, Usuario $usuario)
    {
        $request->validate([
            'nombre'      => 'required|string|max:100',
            'correo'      => 'required|email|max:100|unique:usuarios,correo,' . $usuario->ID_usuario . ',ID_usuario',
            'password'    => 'nullable|string|min:8', 
            'ID_rol'      => 'required|exists:rol,ID_rol',
            'activo'      => 'required|in:0,1',
            'telefono'    => 'nullable|string|max:20', 
            'foto_perfil' => 'nullable|string'         
        ]);

        $datos = $request->only(['nombre', 'correo', 'ID_rol', 'activo', 'telefono', 'foto_perfil']);
        $datos['activo'] = (int) $request->activo;
        
        if ($request->filled('password')) {
            $datos['password'] = Hash::make($request->password);
        }

        $usuario->update($datos);

        return redirect()->route('usuarios.index')->with('exito', 'Usuario actualizado.');
    }

    public function destroy(Usuario $usuario)
    {
        try {
            $usuario->delete();
            return redirect()->route('usuarios.index')->with('exito', 'Usuario eliminado.');
        } catch (\Exception $e) {
            return redirect()->route('usuarios.index')->with('error', 'No se puede eliminar este usuario.');
        }
    }

    /**
     * ACTUALIZACIÓN FINAL: Subida a Cloudinary 
     */
    public function updateFoto(Request $request)
{
    $request->validate([
        'foto' => 'required|file|max:10240',
    ]);

    try {
        $usuario = Auth::user();

        if ($request->hasFile('foto')) {
            // CREDENCIALES DIRECTAS (Sin usar el .env ni config)
            $cloudName = 'dy9nfdieh';
            $apiKey    = '348696452454798';
            $apiSecret = 'E2EQ8_puv2O8lIvwOW5-YBJ_oNo';

            $file = $request->file('foto');

            // USAMOS LA CLASE BASE QUE YA ESTÁ EN TU VENDOR
            $cloudinary = new \Cloudinary\Cloudinary([
                'cloud' => [
                    'cloud_name' => $cloudName,
                    'api_key'    => $apiKey,
                    'api_secret' => $apiSecret,
                ],
            ]);

            // SUBIDA MANUAL
            $upload = $cloudinary->uploadApi()->upload($file->getRealPath(), [
                'folder' => 'perfiles_tac'
            ]);

            $urlNube = $upload['secure_url'];

            // GUARDAR EN BD
            $usuario->foto_perfil = $urlNube;
            $usuario->save();

            if ($request->wantsJson()) {
                return response()->json([
                    'status' => 'success',
                    'foto'   => $urlNube
                ]);
            }

            return back()->with('exito', '¡Foto actualizada!');
        }
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error', 
            'message' => 'Error: ' . $e->getMessage()
        ], 500);
    }
}
}