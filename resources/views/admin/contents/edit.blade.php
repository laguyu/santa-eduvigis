@extends('layouts.admin')

@section('title', 'Editar Seccion')

@section('content')
    <section class="admin-panel">
        <h1>Editar seccion</h1>
        @include('admin.contents.form', [
            'action' => route('admin.contents.update', $content->id),
            'method' => 'PUT',
            'content' => $content,
        ])
    </section>
@endsection
