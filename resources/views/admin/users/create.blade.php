@extends('layouts.admin')

@section('title', 'Nuevo Usuario')

@section('content')
    <section class="admin-panel">
        <h1>Nuevo usuario</h1>
        @include('admin.users.form', [
            'action' => route('admin.users.store'),
            'method' => 'POST',
            'adminUser' => null,
        ])
    </section>
@endsection
