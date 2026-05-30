@extends('layouts.admin')

@section('title', 'Configuracion Parroquial')

@section('content')
    <section class="admin-panel">
        <h1>Configuracion del sitio parroquial</h1>

        <form method="POST" action="{{ route('admin.settings.update') }}" class="admin-form" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <label for="parish_name">Nombre de la parroquia</label>
            <input id="parish_name" name="parish_name" type="text" value="{{ old('parish_name', $branding['name']) }}" required>
            @error('parish_name') <small class="error">{{ $message }}</small> @enderror

            <label for="parish_logo">Logo parroquial</label>
            <input id="parish_logo" name="parish_logo" type="file" accept="image/png,image/jpeg,image/webp,image/svg+xml">
            <small class="help-text">Sube el logo institucional para mostrarlo en el home.</small>
            @error('parish_logo') <small class="error">{{ $message }}</small> @enderror

            @if (!empty($branding['logo_url']))
                <div class="logo-preview">
                    <img src="{{ $branding['logo_url'] }}" alt="Logo parroquial">
                </div>
            @endif

            <div class="actions">
                <button type="submit" class="btn btn-primary">Guardar configuracion</button>
            </div>
        </form>
    </section>
@endsection
