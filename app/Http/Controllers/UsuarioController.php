<?php

namespace App\Http\Controllers;

use App\Models\Ejemplar;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class UsuarioController extends Controller
{
    public function homeUser()
    {
        $alquileres = DB::table('detalle_alquiler')->where('codUsu', Auth::user()->codUsu)->get();


        return view('auth.userAccount', ['alquileres' => $alquileres]);
    }

    public function wishlist()
    {
        $array = [];

        $ejemplares = DB::table('wishlist')->where('codUsu', Auth::user()->codUsu)->get('isbn');

        foreach ($ejemplares as $item) {
            array_push($array, $item->isbn);
        }

        return view('auth.wishlist', ['milista' => Ejemplar::whereIn('isbn', $array)->get()]);
    }

    public function addToWishList(Request $request, Ejemplar $ejemplar)
    {
        $existe = DB::table('wishlist')->where('codUsu', Auth::user()->codUsu)->where('isbn', $ejemplar->isbn)->first();

        if (empty($existe)) {
            Auth::user()->addEjemplarWishList()->attach($ejemplar->isbn);
        }

        return redirect()->route('ejemplar.ejemplar', ['ejemplar' => $ejemplar]);
    }

    public function removeFromWishList(Request $request, Ejemplar $ejemplar)
    {
        Auth::user()->addEjemplarWishList()->detach($ejemplar->isbn);

        return redirect()->route('usuario.wishlist')->with(['success' => 'Ejemplar eliminado de tu WishList']);
    }

    public function cargarImagenUsuario(Request $request)
    {
        if ($request->hasFile('imagen')) {

            $request->validate([
                'imagen' => 'image'
            ]);

            $destination = 'public/img';
            $imagen = $request->file('imagen');
            $imagenName = $imagen->getClientOriginalName();
            $path = $request->file('imagen')->storeAs($destination, $imagenName);

            Usuario::where('codUsu', Auth::user()->codUsu)->update([
                'imagen_usuario' => $imagenName
            ]);
        }

        return redirect()->route('usuario.userHome')->with('success-imagen', "¡Imagen actualizada!");
    }

    public function actualizarDatosPersonales(Request $request, Usuario $usuario)
    {
        $request->validate([
            'nombre' => 'alpha|max:20',
            'ape1' => 'alpha|max:20',
            'ape2' => 'alpha|max:35|nullable',
            'fechaNac' => 'date',
            'email' => 'email|max:255|unique:usuario,email,' . $usuario->codUsu . ',codUsu'
        ]);

        Usuario::where('codUsu', Auth::user()->codUsu)->update([
            'nombre' => $request->nombre,
            'apellido1' => $request->ape1,
            'apellido2' => $request->ape2,
            'email' => $request->email,
            'fecNacimiento' => $request->fechaNac
        ]);

        return redirect()->route('usuario.userHome')->with(['success-datos-personales' => 'Datos personales actualizados']);
    }

    public function cambiarContraseña(Request $request)
    {
        if (Hash::check($request->password, Auth::user()->password)) {

            $request->validate([
                'password' => 'required|alpha_dash|min:8',
                'newPassword' => 'required|regex:/^.*(?=.{3,})(?=.*[a-zA-Z])(?=.*[0-9])(?=.*[\d\x]).*$/|min:8',
                'password-confirm' => 'required|min:8|same:newPassword',
            ]);

            Usuario::where('codUsu', Auth::user()->codUsu)->update([
                'password' => Hash::make($request->newPassword),
            ]);

            return redirect()->route('usuario.userHome')->with('success-contraseña', "¡Contraseña actualizada!");
        }

        return redirect()->route('usuario.userHome')->with('error', "¡La contraseña no existe!");
    }

    public function socio(Request $request)
    {
        date_default_timezone_set('Europe/Madrid');

        if (Auth::user()->idRol !== 2) {
            switch ($request->tipo) {
                case "1":
                    $date_future = date("Y-m-d", strtotime('+30 day', strtotime(date("Y-m-d"))));
                    break;
                case "6":
                    $date_future = date("Y-m-d", strtotime('+180 day', strtotime(date("Y-m-d"))));
                    break;
                case "12":
                    $date_future = date("Y-m-d", strtotime('+365 day', strtotime(date("Y-m-d"))));
                    break;
            }

            Usuario::where('codUsu', Auth::user()->codUsu)->update([
                'idRol' => 2,
                'fec_ini_socio' => date("Y-m-d"),
                'fec_fin_socio' => $date_future,
            ]);

            return redirect()->route('membresia')->with('success', "¡Felicidades, ya eres socio!");
        }

        return redirect()->route('membresia')->with('error', "Ya tienes una membresia activa");
    }

    public function bajaSocio()
    {
        Usuario::where('codUsu', Auth::user()->codUsu)->update([
            'baja' => 1
        ]);

        return redirect()->route('usuario.userHome')->with('success', "¡Te has dado de baja correctamente!");
    }

    /*Parte Administrador*/

    public function cambiarRol(Request $request, Usuario $usuario)
    {
        $rol = null;

        switch ($request->rol) {
            case 'usuario':
                $rol = 1;
                break;
            case 'administrador':
                $rol = 3;
                break;
            default:
                return back()->with(['error' => 'Nombre de rol incorrecto [usuario, administrador]']);
                break;
        }

        if ($rol !== null) {
            Usuario::where('codUsu', $usuario->codUsu)->update([
                'idRol' => $rol
            ]);

            return back()->with(['success' => 'El rol del usuario ' . $usuario->nombre . ' ' .  $usuario->apellido1 . ' ' . $usuario->apellido2 . ' ha sido modificado correctamente']);
        }
    }

    public function usuarios(Request $request)
    {
        return view('admin.usuarios.index', ['usuarios' => Usuario::paginate(20)]);
    }

    public function eliminarCuenta(Request $request, Usuario $usuario)
    {
        Usuario::where('codUsu', $usuario->codUsu)->delete();

        return redirect()->route('usuario.usuarios')->with(['success' => 'Se ha eliminado el usuario ' . $usuario->nombre . ' ' .  $usuario->apellido1 . ' ' . $usuario->apellido2 . ' correctamente']);
    }

    public function buscarUsuario(Request $request)
    {
        $request->validate([
            'email' => 'email'
        ]);

        $usuario = Usuario::where('email', $request->email)->first();

        if ($usuario != null) {
            return view('admin.usuarios.usuario', ['usuario' => $usuario]);
        }

        return back()->with(['error' => 'No se ha encontrado el usuario con email ' . $request->email]);
    }

    public function showMisLibros()
    {
        $misLibros = Auth::user()->ejemplar()->paginate(9);
        $numero = $misLibros->count();

        return view('ejemplares.misLibros', ['misLibros' => $misLibros, "numero" => $numero]);
    }

    public function showLibro(Ejemplar $ejemplar)
    {
        return view('ejemplares.libro', ["ejemplar" => $ejemplar]);
    }
}
