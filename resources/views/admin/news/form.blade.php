<form method="POST" action="{{ $action }}" class="admin-form" enctype="multipart/form-data">
    @csrf
    @if ($method !== 'POST')
        @method($method)
    @endif

    <label for="title">Titulo</label>
    <input id="title" name="title" type="text" value="{{ old('title', $post?->title) }}" required>
    @error('title') <small class="error">{{ $message }}</small> @enderror

    <label for="slug">Slug (opcional)</label>
    <input id="slug" name="slug" type="text" value="{{ old('slug', $post?->slug) }}" placeholder="se-genera-automaticamente">
    @error('slug') <small class="error">{{ $message }}</small> @enderror

    <label for="excerpt">Resumen corto</label>
    <textarea id="excerpt" name="excerpt" rows="3">{{ old('excerpt', $post?->excerpt) }}</textarea>
    @error('excerpt') <small class="error">{{ $message }}</small> @enderror

    <label for="body">Contenido completo</label>
    <textarea id="body" name="body" rows="10" required>{{ old('body', $post?->body) }}</textarea>
    @error('body') <small class="error">{{ $message }}</small> @enderror

    <label for="cover_image">Imagen portada</label>
    <input id="cover_image" name="cover_image" type="file" accept="image/png,image/jpeg,image/webp">
    @error('cover_image') <small class="error">{{ $message }}</small> @enderror

    @if (!empty($post?->cover_image_path))
        <div class="logo-preview">
            <img src="{{ Storage::url($post->cover_image_path) }}" alt="Portada noticia">
        </div>
    @endif

    <div class="grid-two">
        <div>
            <label for="published_at">Fecha de publicacion</label>
            <input id="published_at" name="published_at" type="datetime-local" value="{{ old('published_at', $post?->published_at?->format('Y-m-d\\TH:i')) }}">
            @error('published_at') <small class="error">{{ $message }}</small> @enderror
        </div>
        <div>
            <label for="display_order">Orden</label>
            <input id="display_order" name="display_order" type="number" min="0" value="{{ old('display_order', $post?->display_order ?? 0) }}" required>
            @error('display_order') <small class="error">{{ $message }}</small> @enderror
        </div>
    </div>

    <label class="checkbox-wrap">
        <input name="is_published" type="checkbox" value="1" {{ old('is_published', $post?->is_published ?? true) ? 'checked' : '' }}>
        Publicar noticia
    </label>

    <div class="actions">
        <button type="submit" class="btn btn-primary">Guardar noticia</button>
        <a href="{{ route('admin.news.index') }}" class="btn btn-soft">Cancelar</a>
    </div>
</form>
