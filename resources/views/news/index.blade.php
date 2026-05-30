@extends('layouts.site')

@section('title', 'Noticias | '.$branding['name'])

@section('content')
    <div class="decor-bg"></div>

    <main class="detail-page">
        <a href="{{ route('home') }}" class="btn btn-soft">Volver al inicio</a>

        <section class="modern-section">
            <div class="detail-brand">
                @if (!empty($branding['logo_url']))
                    <img src="{{ $branding['logo_url'] }}" alt="Logo parroquial">
                @endif
                <div>
                    <p class="section-kicker">{{ $branding['name'] }}</p>
                    <h1>Noticias parroquiales</h1>
                </div>
            </div>

            <div class="news-grid">
                @forelse($posts as $post)
                    <article class="news-card">
                        @if ($post->cover_image_path)
                            <img src="{{ Storage::url($post->cover_image_path) }}" alt="{{ $post->title }}">
                        @endif
                        <h3>{{ $post->title }}</h3>
                        <p class="section-sub">{{ $post->published_at?->translatedFormat('d M Y') ?? 'Sin fecha' }}</p>
                        <p>{{ $post->excerpt ?: \Illuminate\Support\Str::limit(strip_tags($post->body), 180) }}</p>
                        <a href="{{ route('news.show', $post->slug) }}" class="btn btn-soft">Leer noticia</a>
                    </article>
                @empty
                    <p>No hay noticias publicadas por ahora.</p>
                @endforelse
            </div>

            {{ $posts->links() }}
        </section>
    </main>
@endsection
