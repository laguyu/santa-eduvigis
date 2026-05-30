@extends('layouts.admin')

@section('title', 'Secciones Home')

@section('content')
    <section class="admin-panel">
        <div class="admin-panel-title">
            <h1>Secciones editables del Home</h1>
            <a href="{{ route('admin.contents.create') }}" class="btn btn-primary">Nueva seccion</a>
        </div>

        <p class="help-text">
            Como editar el home: haz clic en Editar en la seccion que quieras, cambia Titulo/Subtitulo/Contenido y pulsa Guardar.
            Si activas "Mostrar contenido completo en otra pagina", en el home se vera un resumen con el boton "Leer informacion completa".
            Las secciones base del home no se eliminan; si deseas ocultarlas, desmarca "Seccion activa".
        </p>

        <div class="table-wrap">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Orden</th>
                        <th>Clave</th>
                        <th>Titulo</th>
                        <th>Activa</th>
                        <th>Fotos</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($contents as $item)
                        <tr>
                            <td>{{ $item->display_order }}</td>
                            <td>{{ $item->key }}</td>
                            <td>{{ $item->title }}</td>
                            <td>{{ $item->is_active ? 'Si' : 'No' }}</td>
                            <td>{{ $item->images_count }}</td>
                            <td class="actions">
                                <a href="{{ route('admin.contents.edit', $item->id) }}" class="btn btn-soft">Editar</a>
                                @if ($item->isProtected())
                                    <span class="help-text">Seccion base</span>
                                @else
                                    <form method="POST" action="{{ route('admin.contents.destroy', $item->id) }}" onsubmit="return confirm('Deseas eliminar esta seccion?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger">Eliminar</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6">No hay secciones registradas.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $contents->links() }}
    </section>
@endsection
