@extends('layouts.admin')

@section('title', 'Usuarios del Panel')

@section('content')
    <section class="admin-panel">
        <div class="admin-panel-title">
            <h1>Usuarios del panel</h1>
            <a href="{{ route('admin.users.create') }}" class="btn btn-primary">Nuevo usuario</a>
        </div>

        <p class="help-text">
            Gestiona los accesos al panel autoadministrable. Si un usuario no tiene acceso admin, podra existir en el sistema pero no podra ingresar al panel.
        </p>

        <div class="table-wrap">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Correo</th>
                        <th>Acceso panel</th>
                        <th>Creado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $adminUser)
                        <tr>
                            <td>{{ $adminUser->name }}</td>
                            <td>{{ $adminUser->email }}</td>
                            <td>
                                <span class="badge {{ $adminUser->is_admin ? 'badge-ok' : 'badge-muted' }}">
                                    {{ $adminUser->is_admin ? 'Admin activo' : 'Sin acceso' }}
                                </span>
                            </td>
                            <td>{{ $adminUser->created_at?->format('Y-m-d') }}</td>
                            <td class="actions">
                                <a href="{{ route('admin.users.edit', $adminUser->id) }}" class="btn btn-soft">Editar</a>
                                <form method="POST" action="{{ route('admin.users.toggle-access', $adminUser->id) }}">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-outline">
                                        {{ $adminUser->is_admin ? 'Desactivar acceso' : 'Activar acceso' }}
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5">No hay usuarios registrados.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $users->links() }}
    </section>
@endsection
