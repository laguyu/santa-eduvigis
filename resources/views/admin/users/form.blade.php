<form method="POST" action="{{ $action }}" class="admin-form">
    @csrf
    @if ($method !== 'POST')
        @method($method)
    @endif

    <label for="name">Nombre</label>
    <input id="name" name="name" type="text" value="{{ old('name', $adminUser?->name) }}" required>
    @error('name') <small class="error">{{ $message }}</small> @enderror

    <label for="email">Correo</label>
    <input id="email" name="email" type="email" value="{{ old('email', $adminUser?->email) }}" required>
    @error('email') <small class="error">{{ $message }}</small> @enderror

    <label for="password">{{ $adminUser ? 'Nueva contrasena (opcional)' : 'Contrasena' }}</label>
    <input id="password" name="password" type="password" {{ $adminUser ? '' : 'required' }}>
    @error('password') <small class="error">{{ $message }}</small> @enderror

    <label for="password_confirmation">Confirmar contrasena</label>
    <input id="password_confirmation" name="password_confirmation" type="password" {{ $adminUser ? '' : 'required' }}>

    <label class="checkbox-wrap">
        <input name="is_admin" type="checkbox" value="1" {{ old('is_admin', $adminUser?->is_admin ?? true) ? 'checked' : '' }}>
        Acceso admin al panel
    </label>
    @error('is_admin') <small class="error">{{ $message }}</small> @enderror

    <div class="actions">
        <button type="submit" class="btn btn-primary">{{ $adminUser ? 'Guardar cambios' : 'Crear usuario' }}</button>
        <a href="{{ route('admin.users.index') }}" class="btn btn-soft">Cancelar</a>
    </div>
</form>
