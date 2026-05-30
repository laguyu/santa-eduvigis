@extends('layouts.site')

@section('title', $post->title.' | '.$branding['name'])

@section('content')
    <div class="decor-bg"></div>

    <main class="detail-page">
        <a href="{{ route('news.index') }}" class="btn btn-soft">Volver a noticias</a>

        <article class="modern-section">
            <div class="detail-brand">
                @if (!empty($branding['logo_url']))
                    <img src="{{ $branding['logo_url'] }}" alt="Logo parroquial">
                @endif
                <div>
                    <p class="section-kicker">{{ $branding['name'] }}</p>
                    <h1>{{ $post->title }}</h1>
                </div>
            </div>

            <p class="section-sub">{{ $post->published_at?->translatedFormat('d M Y H:i') ?? 'Sin fecha de publicacion' }}</p>

            @if ($post->cover_image_path)
                <img src="{{ Storage::url($post->cover_image_path) }}" alt="{{ $post->title }}" class="detail-cover">
            @endif

            <div class="section-rich">{!! preg_match('/<[^>]+>/', (string) $post->body) ? $post->body : nl2br(e((string) $post->body)) !!}</div>
        </article>
    </main>
@endsection
