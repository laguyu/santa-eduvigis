<form method="POST" action="{{ $action }}" class="admin-form" enctype="multipart/form-data">
    @csrf
    @if ($method !== 'POST')
        @method($method)
    @endif

    @php
        $isProtectedSection = !empty($content) && $content->isProtected();
    @endphp

    <label for="key">Clave (slug)</label>
    @if ($isProtectedSection)
        <input id="key" name="key_display" type="text" value="{{ old('key', $content?->key) }}" disabled required>
        <input name="key" type="hidden" value="{{ old('key', $content?->key) }}">
        <small class="help-text">La clave de esta seccion base esta protegida y no se puede cambiar.</small>
    @else
        <input id="key" name="key" type="text" value="{{ old('key', $content?->key) }}" required>
    @endif
    @error('key') <small class="error">{{ $message }}</small> @enderror

    <label for="title">Titulo</label>
    <input id="title" name="title" type="text" value="{{ old('title', $content?->title) }}" required>
    @error('title') <small class="error">{{ $message }}</small> @enderror

    <label for="subtitle">Subtitulo</label>
    <input id="subtitle" name="subtitle" type="text" value="{{ old('subtitle', $content?->subtitle) }}">
    <small class="help-text">En Misas, Sacramentos y Comunidad, este subtitulo tambien se muestra en "Hoy en la parroquia".</small>
    @error('subtitle') <small class="error">{{ $message }}</small> @enderror

    <label for="body">Contenido (editor enriquecido)</label>
    <div class="rich-editor" data-rich-editor>
        <div class="rich-toolbar" role="toolbar" aria-label="Editor de texto enriquecido">
            <button type="button" class="btn btn-soft" data-cmd="bold" title="Negrita"><strong>B</strong></button>
            <button type="button" class="btn btn-soft" data-cmd="italic" title="Cursiva"><em>I</em></button>
            <button type="button" class="btn btn-soft" data-cmd="underline" title="Subrayado"><u>U</u></button>
            <button type="button" class="btn btn-soft" data-cmd="insertUnorderedList" title="Lista">Lista</button>
            <button type="button" class="btn btn-soft" data-cmd="insertOrderedList" title="Numeracion">1,2,3</button>
            <button type="button" class="btn btn-soft" data-cmd="createLink" title="Enlace">Link</button>
            <label class="color-pick" title="Color de texto">
                Color
                <input type="color" data-color-picker value="#8f1e00">
            </label>
            <button type="button" class="btn btn-soft" data-cmd="removeFormat" title="Limpiar formato">Limpiar</button>
        </div>

        <div class="rich-content" contenteditable="true" data-rich-content>{!! old('body', $content?->body) !!}</div>
        <textarea id="body" name="body" rows="6" hidden>{{ old('body', $content?->body) }}</textarea>
    </div>
    @error('body') <small class="error">{{ $message }}</small> @enderror

    <label for="highlights">Etiquetas o items destacados</label>
    <textarea id="highlights" name="highlights" rows="4" placeholder="Una linea por item">{{ old('highlights', $content?->highlights) }}</textarea>
    <small class="help-text">Para Comunidad, cada linea se muestra como una etiqueta en el home.</small>
    @error('highlights') <small class="error">{{ $message }}</small> @enderror

    <div class="grid-two">
        <div>
            <label for="cta_text">Texto boton</label>
            <input id="cta_text" name="cta_text" type="text" value="{{ old('cta_text', $content?->cta_text) }}">
            @error('cta_text') <small class="error">{{ $message }}</small> @enderror
        </div>
        <div>
            @php
                $savedUrl = old('cta_url', $content?->cta_url);
                $presetTargets = ['#inicio', '#horarios', '#sacramentos', '#noticias', '#comunidad', '#contacto'];
                $selectedTarget = in_array($savedUrl, $presetTargets, true) || empty($savedUrl) ? $savedUrl : 'custom';
            @endphp

            <label for="cta_target">Destino del boton</label>
            <select id="cta_target" name="cta_target" data-cta-target>
                <option value="">Sin enlace</option>
                <option value="#inicio" {{ $selectedTarget === '#inicio' ? 'selected' : '' }}>Ir a Inicio</option>
                <option value="#horarios" {{ $selectedTarget === '#horarios' ? 'selected' : '' }}>Ir a Horarios de Misa</option>
                <option value="#sacramentos" {{ $selectedTarget === '#sacramentos' ? 'selected' : '' }}>Ir a Sacramentos</option>
                <option value="#noticias" {{ $selectedTarget === '#noticias' ? 'selected' : '' }}>Ir a Noticias</option>
                <option value="#comunidad" {{ $selectedTarget === '#comunidad' ? 'selected' : '' }}>Ir a Comunidad</option>
                <option value="#contacto" {{ $selectedTarget === '#contacto' ? 'selected' : '' }}>Ir a Contacto</option>
                <option value="custom" {{ $selectedTarget === 'custom' ? 'selected' : '' }}>URL personalizada</option>
            </select>

            <input
                id="cta_url_custom"
                type="text"
                data-cta-custom
                placeholder="https://... o mailto:..."
                value="{{ $selectedTarget === 'custom' ? $savedUrl : '' }}"
                {{ $selectedTarget === 'custom' ? '' : 'hidden' }}
            >

            <input id="cta_url" name="cta_url" type="hidden" value="{{ $savedUrl }}" data-cta-hidden>
            <small class="help-text">Tip: Elige una opcion de la lista para evitar escribir URLs manualmente.</small>
            @error('cta_url') <small class="error">{{ $message }}</small> @enderror
        </div>
    </div>

    <div class="grid-two">
        <div>
            <label for="display_order">Orden</label>
            <input id="display_order" name="display_order" type="number" min="0" value="{{ old('display_order', $content?->display_order ?? 0) }}" required>
            @error('display_order') <small class="error">{{ $message }}</small> @enderror
        </div>
        <label class="checkbox-wrap">
            <input name="is_active" type="checkbox" value="1" {{ old('is_active', $content?->is_active ?? true) ? 'checked' : '' }}>
            Seccion activa
        </label>
    </div>

    <label class="checkbox-wrap">
        <input name="use_detail_page" type="checkbox" value="1" {{ old('use_detail_page', $content?->use_detail_page ?? false) ? 'checked' : '' }}>
        Mostrar contenido completo en otra pagina (ideal para textos extensos)
    </label>

    <div class="actions">
        <button type="submit" class="btn btn-primary">Guardar</button>
        <a href="{{ route('admin.contents.index') }}" class="btn btn-soft">Cancelar</a>
    </div>

    <hr>

    <label for="images">Album de imagenes (puedes seleccionar varias)</label>
    <input id="images" name="images[]" type="file" accept="image/png,image/jpeg,image/webp" multiple>
    <small class="help-text">Formatos permitidos: JPG, PNG, WEBP. Maximo 4MB por imagen.</small>
    @error('images') <small class="error">{{ $message }}</small> @enderror
    @error('images.*') <small class="error">{{ $message }}</small> @enderror

    @if (!empty($content) && $content->images->isNotEmpty())
        <div class="album-grid">
            @foreach($content->images as $image)
                <label class="album-card">
                    <img src="{{ Storage::url($image->path) }}" alt="Imagen {{ $loop->iteration }}">
                    <span>Eliminar</span>
                    <input type="checkbox" name="remove_image_ids[]" value="{{ $image->id }}">
                </label>
            @endforeach
        </div>
    @endif
</form>
