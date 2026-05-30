@extends('layouts.admin')

@section('title', 'Nueva Seccion')

@section('content')
    <section class="admin-panel">
        <h1>Nueva seccion</h1>
        @include('admin.contents.form', [
            'action' => route('admin.contents.store'),
            'method' => 'POST',
            'content' => null,
        ])
    </section>
@endsection
