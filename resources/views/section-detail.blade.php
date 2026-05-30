@extends('layouts.site')

@section('title', $section->title.' | '.$branding['name'])

@section('content')
    @php
        $detailBody = preg_match('/<[^>]+>/', (string) $section->body)
            ? $section->body
            : nl2br(e((string) $section->body));
    @endphp

    <div class="decor-bg"></div>

    <main class="detail-page">
        <a href="{{ route('home') }}" class="btn btn-soft">Volver al inicio</a>

        <article class="modern-section">
            <div class="detail-brand">
                @if (!empty($branding['logo_url']))
                    <img src="{{ $branding['logo_url'] }}" alt="Logo parroquial">
                @endif
                <div>
                    <p class="section-kicker">{{ $branding['name'] }}</p>
                    <h1>{{ $section->title }}</h1>
                </div>
            </div>

            @if (!empty($section->subtitle))
                <p class="section-sub">{{ $section->subtitle }}</p>
            @endif

            <div class="section-rich">{!! $detailBody !!}</div>

            @if ($section->images->isNotEmpty())
                <div class="image-carousel section-gallery" data-carousel>
                    <div class="carousel-viewport">
                        <div class="carousel-track" data-carousel-track>
                            @foreach ($section->images as $image)
                                <figure class="carousel-slide">
                                    <img src="{{ Storage::url($image->path) }}" alt="Imagen de {{ $section->title }}">
                                </figure>
                            @endforeach
                        </div>
                    </div>

                    @if ($section->images->count() > 1)
                        <button type="button" class="carousel-btn prev" data-carousel-prev aria-label="Imagen anterior">&#10094;</button>
                        <button type="button" class="carousel-btn next" data-carousel-next aria-label="Imagen siguiente">&#10095;</button>
                        <div class="carousel-dots" data-carousel-dots></div>
                    @endif
                </div>
            @endif
        </article>
    </main>
@endsection
