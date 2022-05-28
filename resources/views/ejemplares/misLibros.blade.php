@extends('layouts.app')

@section('titulo', 'Mis libros')

@section('content')

    <div class="buscador">
        <div class="buscador__div">
            <form action="{{ route('ejemplar.buscar') }}" method="post">
                @csrf
                <span class="buscador__icono"><i class="fa fa-search"></i></span>
                <input type="search" class="buscador__input" name="ejemplar" placeholder="Busca un ejemplar..." />
            </form>
        </div>
    </div>

    <div class="filtro">

        <p class="filtro__p">{{ $numero }} ejemplares</p>

        <div id="mostrar-ordenar" class="ordenar">
            <p class="ordenar__p">Ordenar por...</p>
            <ul id="opciones-ordenar" class="ordenar__ul">
                <li class="ordenar__li"><a href="{{ route('ejemplar.ordenar', 1) }}" class="ordenar__a">Nombre
                        [a-z]</a>
                </li>
                <li class="ordenar__li"><a href="{{ route('ejemplar.ordenar', 2) }}" class="ordenar__a">Nombre
                        [z-a]</a>
                </li>
                <li class="ordenar__li"><a href="{{ route('ejemplar.ordenar', 3) }}" class="ordenar__a">Publicación
                        más
                        antigua</a></li>
                <li class="ordenar__li"><a href="{{ route('ejemplar.ordenar', 4) }}" class="ordenar__a">Publicación
                        más
                        reciente</a></li>
                <li class="ordenar__li"><a href="{{ route('ejemplar.ordenar', 5) }}" class="ordenar__a">Mejor
                        valorado</a>
                </li>
            </ul>
        </div>

    </div>

    <div class="ejemplares">
        @foreach ($misLibros as $item)
            <div class="libro">
                <ul class="libro__paginas">
                    <li class="libro__portada libro__li">
                        <img src="{{ asset('book/' . $item->image_book) }}" alt="portada" class="libro__img">
                    </li>
                    <li class="libro__contratapa libro__li"></li>
                    <li class="libro__pagina libro__li">
                    </li>
                    <li class="libro__pagina libro__li">
                    </li>
                    <li class="libro__pagina libro__li">
                    </li>
                    <li class="libro__pagina libro__enlace libro__li">
                        <a href="{{ route('usuario.libro', $item) }}" class="libro__a">Leer</a>
                    </li>
                    <li class="libro__pagina libro__li">
                    </li>
                    <li class="libro__contraportada"></li>
                </ul>
            </div>
        @endforeach
    </div>
@endsection
