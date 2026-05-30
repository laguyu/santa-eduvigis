@extends('layouts.site')

@section('title', 'Nueva Contrasena')

@section('content')
    <main class="login-wrap">
        <section class="login-card">
            <h1>Nueva contrasena</h1>
            <p>Define una nueva contrasena para tu acceso al panel.</p>

            <form method="POST" action="{{ route('admin.password.update') }}" class="admin-form">
                @csrf

                <input type="hidden" name="token" value="{{ $token }}">

                <label for="email">Correo</label>
                <input id="email" name="email" type="email" value="{{ old('email', $email) }}" required autocomplete="username">
                @error('email') <small class="error">{{ $message }}</small> @enderror

                <label for="password">Contrasena nueva</label>
                <input id="password" name="password" type="password" required autocomplete="new-password">
                @error('password') <small class="error">{{ $message }}</small> @enderror

                <label for="password_confirmation">Confirmar contrasena</label>
                <input id="password_confirmation" name="password_confirmation" type="password" required autocomplete="new-password">

                <button type="submit" class="btn btn-primary">Actualizar contrasena</button>
                <a href="{{ route('admin.login') }}" class="btn btn-soft">Volver al ingreso</a>
            </form>
        </section>
    </main>
@endsection
