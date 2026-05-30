<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Panel Admin')</title>
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
</head>
<body class="admin-body">
    <header class="admin-header">
        <div class="admin-header-inner">
            <a href="{{ route('admin.contents.index') }}" class="admin-brand">Parroquia Santa Eduviges</a>
            <div class="actions">
                <a href="{{ route('admin.contents.index') }}" class="btn btn-soft">Secciones</a>
                <a href="{{ route('admin.news.index') }}" class="btn btn-soft">Noticias</a>
                <a href="{{ route('admin.users.index') }}" class="btn btn-soft">Usuarios</a>
                <a href="{{ route('admin.settings.edit') }}" class="btn btn-soft">Logo y nombre</a>
                <form method="POST" action="{{ route('admin.logout') }}">
                    @csrf
                    <button type="submit" class="btn btn-outline">Cerrar sesion</button>
                </form>
            </div>
        </div>
    </header>

    <main class="admin-main">
        @if (session('status'))
            <div class="alert-success">{{ session('status') }}</div>
        @endif

        @yield('content')
    </main>
</body>
</html>
