@extends('layouts.admin')

@section('title', 'Nueva Noticia')

@section('content')
    <section class="admin-panel">
        <h1>Nueva noticia</h1>
        @include('admin.news.form', [
            'action' => route('admin.news.store'),
            'method' => 'POST',
            'post' => null,
        ])
    </section>
@endsection
