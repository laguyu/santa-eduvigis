@extends('layouts.site')

@section('title', 'Recuperar Contrasena')

@section('content')
    <main class="login-wrap">
        <section class="login-card">
            <h1>Recuperar contrasena</h1>
            <p>Ingresa tu correo de acceso al panel para enviarte un enlace de recuperacion.</p>

            @if (session('status'))
                <small>{{ session('status') }}</small>
            @endif

            <form method="POST" action="{{ route('admin.password.email') }}" class="admin-form">
                @csrf

                <label for="email">Correo</label>
                <input id="email" name="email" type="email" value="{{ old('email') }}" required autocomplete="username">
                @error('email') <small class="error">{{ $message }}</small> @enderror

                <button type="submit" class="btn btn-primary">Enviar enlace</button>
                <a href="{{ route('admin.login') }}" class="btn btn-soft">Volver al ingreso</a>
            </form>
        </section>
    </main>
@endsection
