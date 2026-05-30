@extends('layouts.site')

@section('title', 'Ingreso Admin')

@section('content')
    <main class="login-wrap">
        <section class="login-card">
            <h1>Panel Admin Parroquial</h1>
            <p>Ingresa para editar el contenido del sitio principal.</p>

            @if (session('status'))
                <small>{{ session('status') }}</small>
            @endif

            <form method="POST" action="{{ route('admin.login.submit') }}" class="admin-form">
                @csrf

                <label for="email">Correo</label>
                <input id="email" name="email" type="email" value="{{ old('email') }}" required autocomplete="username">
                @error('email') <small class="error">{{ $message }}</small> @enderror

                <label for="password">Contrasena</label>
                <input id="password" name="password" type="password" required autocomplete="current-password">

                <a href="{{ route('admin.password.request') }}" class="help-text">Olvide mi contrasena</a>

                <button type="submit" class="btn btn-primary">Ingresar</button>
            </form>
        </section>
    </main>
@endsection
