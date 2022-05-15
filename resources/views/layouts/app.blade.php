<!doctype html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link href="https://fonts.googleapis.com/css2?family=Material+Icons" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Icons+Outlined" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Icons+Round" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Icons+Sharp" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Icons+Two+Tone" rel="stylesheet">
    <link rel="stylesheet" href="//use.fontawesome.com/releases/v5.0.7/css/all.css">
    <link href="{{ asset('css/main.css') }}" rel="stylesheet">
    @yield('javascript')
    <script src="{{ asset('anime.min.js') }}" async></script>
    <title>@yield('titulo')</title>
</head>

<body>
    <div class="logo">
        <img src="../buho.svg" alt="buho" class="logo__imagen--width">
    </div>

    <header class="header header--height">
        <nav>
            <span id="menu-barra" class="header__menu-barra">
                <i class="fas fa-bars header__i--margin"></i>Menu
            </span>
            <ul id="c" class="header__ul header__ul--none">

                <li><a href="{{ route('inicio') }}" class="header__a">Inicio</a></li>
                <li><a href="#" class="header__a">Ejemplares</a></li>
                <li><a href="{{ route('conocenos') }}" class="header__a">Conocenos</a></li>
                <li><a href="{{ route('contacto') }}" class="header__a">Contacto</a></li>
                {{-- @auth
                    @if (Auth::user()->idRol == 3)
                        <li><a href="{{ route('admin') }}">Administrador</a></li>
                    @endif
                @endauth --}}
                <li><a href="#" class="header__a"><i class="fas fa-heart header__i--margin"></i>WishList</a></li>
                <li><a href="#" class="header__a"><i class="fas fa-cart-plus header__i--margin"></i>Carrito</a></li>
                <li>
                    @if (Auth::user())
                        <a href="{{ route('usuario.userHome') }}" class="header__a"><i class="fas fa-user header__i--margin"></i>Mi Cuenta</a>
                    @else
                        <a href="{{ route('login') }}" class="header__a"><i class="fas fa-user header__i--margin"></i>Mi Cuenta</a>
                    @endif
                </li>
            </ul>
        </nav>
    </header>

    <div class="contenedor">

        <main>
            @yield('content')
        </main>

    </div>

    <footer class="footer"></footer>
</body>

</html>
