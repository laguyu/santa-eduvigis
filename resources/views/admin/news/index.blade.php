@extends('layouts.admin')

@section('title', 'Noticias Parroquiales')

@section('content')
    <section class="admin-panel">
        <div class="admin-panel-title">
            <h1>Noticias parroquiales</h1>
            <a href="{{ route('admin.news.create') }}" class="btn btn-primary">Nueva noticia</a>
        </div>

        <div class="table-wrap">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Titulo</th>
                        <th>Publicado</th>
                        <th>Fecha</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($posts as $post)
                        <tr>
                            <td>{{ $post->title }}</td>
                            <td>{{ $post->is_published ? 'Si' : 'No' }}</td>
                            <td>{{ $post->published_at?->format('Y-m-d H:i') ?? '-' }}</td>
                            <td class="actions">
                                <a href="{{ route('admin.news.edit', $post->id) }}" class="btn btn-soft">Editar</a>
                                <form method="POST" action="{{ route('admin.news.destroy', $post->id) }}" onsubmit="return confirm('Deseas eliminar esta noticia?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger">Eliminar</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4">No hay noticias registradas.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $posts->links() }}
    </section>
@endsection
