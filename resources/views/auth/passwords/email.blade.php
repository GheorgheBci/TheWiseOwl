<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link href="{{ asset('css/main.css') }}" rel="stylesheet">
    <title>Recuperar contraseña</title>
</head>

<body>

    <div class="logo">
        <img src="{{ asset('img/buho.svg') }}" alt="buho" class="logo__imagen--width">
    </div>

    <div class="contenedor">

        <div class="email--padding">

            <form method="POST" action="{{ route('password.email') }}">
                @csrf

                <div class="email__div">
                    <label for="email" class="email__label">Correo Electrónico: </label>

                    <div class="email__div--mt">
                        <input id="email" class="email__input" type="email" @error('email') is-invalid @enderror
                            name="email" required autocomplete="email" autofocus placeholder="Indica tu email">
                    </div>

                    @error('email')
                        <span class="mensaje__error--red mensaje__error-reset-password-fs">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror

                    @if (session('status'))
                        <div class="mensaje__exito">
                            {{ session('status') }}
                        </div>
                    @endif
                    <div>
                        <button type="submit" class="email__button">Enviar el link</button>
                    </div>
                </div>


            </form>
        </div>

    </div>
</body>

</html>
