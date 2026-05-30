@extends('layouts.admin')

@section('title', 'Editar Noticia')

@section('content')
    <section class="admin-panel">
        <h1>Editar noticia</h1>
        @include('admin.news.form', [
            'action' => route('admin.news.update', $post->id),
            'method' => 'PUT',
            'post' => $post,
        ])
    </section>
@endsection
