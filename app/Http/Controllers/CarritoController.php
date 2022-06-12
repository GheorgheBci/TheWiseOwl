<?php

namespace App\Http\Controllers;

use App\Models\Ejemplar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


class CarritoController extends Controller
{
    public function addToCarrito(Request $request, Ejemplar $ejemplar)
    {
        \Cart::session(Auth::user()->codUsu);

        $ejemplares = \Cart::getContent();

        if (count($ejemplares) === 0) {

            \Cart::session(Auth::user()->codUsu)->add(array(
                'id' => $ejemplar->isbn,
                'name' => $ejemplar->nomEjemplar,
                'price' => $ejemplar->precio,
                'quantity' => 1,
                'attributes' => array(
                    'portada' => $ejemplar->image_book
                ),
                'associatedModel' => 'Ejemplar'
            ));

            Auth::user()->addEjemplarWishList()->detach($ejemplar->isbn);

            return back()->with(['exito' => 'Ejemplar añadido al carrito']);
        }

        foreach ($ejemplares as $libro) {

            if ($ejemplar->isbn !== $libro->id) {
                \Cart::session(Auth::user()->codUsu)->add(array(
                    'id' => $ejemplar->isbn,
                    'name' => $ejemplar->nomEjemplar,
                    'price' => $ejemplar->precio,
                    'quantity' => 1,
                    'attributes' => array(
                        'portada' => $ejemplar->image_book
                    ),
                    'associatedModel' => 'Ejemplar'
                ));

                Auth::user()->addEjemplarWishList()->detach($ejemplar->isbn);

                return back()->with(['exito' => 'Ejemplar añadido al carrito']);
            }
        }

        return back()->with(['exito' => 'Este ejemplar ya esta en el carrito']);
    }

    public function showCarrito()
    {
        \Cart::session(Auth::user()->codUsu);

        $ejemplar = \Cart::getContent();

        $total = \Cart::getTotal();

        $cantidad = \Cart::getTotalQuantity();

        return view('auth.carrito', ['ejemplar' => $ejemplar, 'total' => $total, 'cantidad' => $cantidad]);
    }

    public function removeFromCarrito(Request $request, $id)
    {
        \Cart::session(Auth::user()->codUsu)->remove($id);

        return redirect()->route('show');
    }

    public function alquilarCarrito(Request $request)
    {
        \Cart::session(Auth::user()->codUsu);

        $ejemplares =  \Cart::getContent();


        $array = array();

        foreach (Auth::user()->ejemplar as $key) {
            array_push($array, $key->pivot->isbn);
        }

        foreach ($ejemplares as $ejemplar) {

            if (!in_array($ejemplar->id, $array)) {

                DB::table('detalle_alquiler')->insert([
                    'codUsu' => Auth::user()->codUsu,
                    'isbn' => $ejemplar->id,
                    'fecAlquiler' => date('Y-m-d'),
                    'fecDevolucion' => date('Y-m-d', strtotime('+30 day', strtotime(date('Y-m-d')))),
                    'precioAlquiler' => $ejemplar->price,
                ]);
            }

            \Cart::session(Auth::user()->codUsu)->remove($ejemplar->id);
        }

        return redirect()->route('show');
    }
}
