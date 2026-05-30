@extends('layouts.admin')

@section('title', 'Editar Usuario')

@section('content')
    <section class="admin-panel">
        <h1>Editar usuario</h1>
        @include('admin.users.form', [
            'action' => route('admin.users.update', $adminUser->id),
            'method' => 'PUT',
            'adminUser' => $adminUser,
        ])
    </section>
@endsection
