@extends('layouts.site')

@section('title', 'Parroquia Santa Eduviges')

@section('content')
    <div class="decor-bg"></div>

    <header class="modern-hero" id="{{ $hero['anchor'] }}">
        <nav class="modern-nav">
            <div class="brand-block">
                @if (!empty($branding['logo_url']))
                    <img src="{{ $branding['logo_url'] }}" alt="Logo parroquial" class="brand-logo">
                @else
                    <span class="brand-dot"></span>
                @endif
                <span class="brand-mark" aria-hidden="true"></span>
                <strong>{{ $branding['name'] ?? 'Santa Eduviges' }}</strong>
            </div>

            <div class="modern-nav-links">
                @foreach ($navItems as $navItem)
                    <a href="{{ $navItem['href'] }}" @class(['admin-link' => $navItem['is_admin'] ?? false])>{{ $navItem['label'] }}</a>
                @endforeach
                <button type="button" class="contrast-toggle" data-contrast-toggle aria-pressed="false">Alto contraste</button>
            </div>
        </nav>

        <div class="hero-grid">
            <div class="hero-content">
                <p class="eyebrow">{{ $hero['kicker'] }}</p>
                <h1>{{ $hero['title'] }}</h1>
                <h2>{{ $hero['subtitle'] }}</h2>
                <p>{!! $hero['body_html'] !!}</p>

                <div class="hero-actions">
                    @if ($hero['cta_text'] !== '')
                        <a class="btn btn-primary {{ str_starts_with($hero['cta_url'], '#') ? 'is-anchor' : '' }}" href="{{ $hero['cta_url'] }}">{{ $hero['cta_text'] }}</a>
                    @endif
                    @if ($massSchedule)
                        <a class="btn btn-soft is-anchor" href="#{{ $massSchedule['anchor'] }}">Ver horarios</a>
                    @endif
                </div>

                @if ($hero['images']->isNotEmpty())
                    <div class="image-carousel hero-gallery" data-carousel>
                        <div class="carousel-loading" data-carousel-loader aria-hidden="true"></div>
                        <div class="carousel-viewport">
                            <div class="carousel-track" data-carousel-track>
                                @foreach ($hero['images'] as $image)
                                    <figure class="carousel-slide">
                                        <img src="{{ Storage::url($image->path) }}" alt="Imagen principal de la parroquia">
                                    </figure>
                                @endforeach
                            </div>
                        </div>

                        @if ($hero['images']->count() > 1)
                            <button type="button" class="carousel-btn prev" data-carousel-prev aria-label="Imagen anterior">&#10094;</button>
                            <button type="button" class="carousel-btn next" data-carousel-next aria-label="Imagen siguiente">&#10095;</button>
                            <div class="carousel-dots" data-carousel-dots></div>
                        @endif
                    </div>
                @endif
            </div>

            @if (! empty($heroPanelSections))
                <aside class="hero-panel">
                    <p class="panel-title">Hoy en la parroquia</p>
                    <div class="panel-item panel-live" data-live-panel>
                        <span>Hora local</span>
                        <strong data-live-time>--:--</strong>
                        <small data-live-date>--</small>
                    </div>
                    @foreach ($heroPanelSections as $panelSection)
                        <div class="panel-item">
                            <span>{{ $panelSection['title'] }}</span>
                            <strong>{{ $panelSection['summary'] }}</strong>
                        </div>
                    @endforeach
                </aside>
            @endif
        </div>
    </header>

    <main class="modern-main">
        @if ($massSchedule)
            <section class="modern-section section-schedule section-variant-schedule" id="{{ $massSchedule['anchor'] }}">
                <div class="section-head">
                    <span class="section-icon icon-schedule" aria-hidden="true"></span>
                    <p class="section-kicker">{{ $massSchedule['kicker'] }}</p>
                    <h3>{{ $massSchedule['title'] }}</h3>
                    <p class="section-sub">{{ $massSchedule['subtitle'] }}</p>
                </div>

                <div class="schedule-content">
                    @if ($massSchedule['use_detail_page'])
                        <div class="section-rich">{{ $massSchedule['body_summary'] }}</div>
                        <a class="btn btn-soft" href="{{ $massSchedule['detail_url'] }}">Leer informacion completa</a>
                    @else
                        <div class="section-rich">{!! $massSchedule['body_html'] !!}</div>
                    @endif

                    @if ($massSchedule['images']->isNotEmpty())
                        <div class="image-carousel section-gallery" data-carousel>
                            <div class="carousel-loading" data-carousel-loader aria-hidden="true"></div>
                            <div class="carousel-viewport">
                                <div class="carousel-track" data-carousel-track>
                                    @foreach ($massSchedule['images'] as $image)
                                        <figure class="carousel-slide">
                                            <img src="{{ Storage::url($image->path) }}" alt="Imagen de {{ $massSchedule['title'] }}">
                                        </figure>
                                    @endforeach
                                </div>
                            </div>

                            @if ($massSchedule['images']->count() > 1)
                                <button type="button" class="carousel-btn prev" data-carousel-prev aria-label="Imagen anterior">&#10094;</button>
                                <button type="button" class="carousel-btn next" data-carousel-next aria-label="Imagen siguiente">&#10095;</button>
                                <div class="carousel-dots" data-carousel-dots></div>
                            @endif
                        </div>
                    @endif

                    @if ($massSchedule['cta_text'] !== '')
                        <a class="btn btn-soft {{ str_starts_with($massSchedule['cta_url'], '#') ? 'is-anchor' : '' }}" href="{{ $massSchedule['cta_url'] }}">{{ $massSchedule['cta_text'] }}</a>
                    @endif
                </div>
            </section>
        @endif

        @if ($sacraments || $news)
            <section class="modern-grid">
                @if ($sacraments)
                    <article class="modern-section section-variant-sacraments" id="{{ $sacraments['anchor'] }}">
                        <div class="section-shell">
                            <div class="section-copy">
                                <span class="section-icon icon-sacraments" aria-hidden="true"></span>
                                <p class="section-kicker">{{ $sacraments['kicker'] }}</p>
                                <h3>{{ $sacraments['title'] }}</h3>
                                <p class="section-sub">{{ $sacraments['subtitle'] }}</p>

                                @if ($sacraments['use_detail_page'])
                                    <div class="section-rich">{{ $sacraments['body_summary'] }}</div>
                                    <a class="btn btn-soft" href="{{ $sacraments['detail_url'] }}">Leer informacion completa</a>
                                @else
                                    <div class="section-rich">{!! $sacraments['body_html'] !!}</div>
                                @endif

                                @if ($sacraments['cta_text'] !== '')
                                    <a class="btn btn-soft {{ str_starts_with($sacraments['cta_url'], '#') ? 'is-anchor' : '' }}" href="{{ $sacraments['cta_url'] }}">{{ $sacraments['cta_text'] }}</a>
                                @endif
                            </div>

                            @if ($sacraments['images']->isNotEmpty())
                                <div class="section-media">
                                    <div class="image-carousel section-gallery" data-carousel>
                                        <div class="carousel-loading" data-carousel-loader aria-hidden="true"></div>
                                        <div class="carousel-viewport">
                                            <div class="carousel-track" data-carousel-track>
                                                @foreach ($sacraments['images'] as $image)
                                                    <figure class="carousel-slide">
                                                        <img src="{{ Storage::url($image->path) }}" alt="Imagen de {{ $sacraments['title'] }}">
                                                    </figure>
                                                @endforeach
                                            </div>
                                        </div>

                                        @if ($sacraments['images']->count() > 1)
                                            <button type="button" class="carousel-btn prev" data-carousel-prev aria-label="Imagen anterior">&#10094;</button>
                                            <button type="button" class="carousel-btn next" data-carousel-next aria-label="Imagen siguiente">&#10095;</button>
                                            <div class="carousel-dots" data-carousel-dots></div>
                                        @endif
                                    </div>
                                </div>
                            @endif
                        </div>
                    </article>
                @endif

                @if ($news)
                    <article class="modern-section is-reverse section-variant-news" id="{{ $news['anchor'] }}">
                        <div class="section-shell">
                            <div class="section-copy">
                                <span class="section-icon icon-news" aria-hidden="true"></span>
                                <p class="section-kicker">{{ $news['kicker'] }}</p>
                                <h3>{{ $news['title'] }}</h3>
                                <p class="section-sub">{{ $news['subtitle'] }}</p>

                                @if ($news['use_detail_page'])
                                    <div class="section-rich">{{ $news['body_summary'] }}</div>
                                    <a class="btn btn-soft" href="{{ $news['detail_url'] }}">Leer informacion completa</a>
                                @else
                                    <div class="section-rich">{!! $news['body_html'] !!}</div>
                                @endif

                                <a class="btn btn-primary" href="{{ $newsIndexUrl }}">Ver noticias publicadas</a>

                                @if ($news['cta_text'] !== '')
                                    <a class="btn btn-soft {{ str_starts_with($news['cta_url'], '#') ? 'is-anchor' : '' }}" href="{{ $news['cta_url'] }}">{{ $news['cta_text'] }}</a>
                                @endif
                            </div>

                            @if ($news['images']->isNotEmpty())
                                <div class="section-media">
                                    <div class="image-carousel section-gallery" data-carousel>
                                        <div class="carousel-loading" data-carousel-loader aria-hidden="true"></div>
                                        <div class="carousel-viewport">
                                            <div class="carousel-track" data-carousel-track>
                                                @foreach ($news['images'] as $image)
                                                    <figure class="carousel-slide">
                                                        <img src="{{ Storage::url($image->path) }}" alt="Imagen de {{ $news['title'] }}">
                                                    </figure>
                                                @endforeach
                                            </div>
                                        </div>

                                        @if ($news['images']->count() > 1)
                                            <button type="button" class="carousel-btn prev" data-carousel-prev aria-label="Imagen anterior">&#10094;</button>
                                            <button type="button" class="carousel-btn next" data-carousel-next aria-label="Imagen siguiente">&#10095;</button>
                                            <div class="carousel-dots" data-carousel-dots></div>
                                        @endif
                                    </div>
                                </div>
                            @endif
                        </div>
                    </article>
                @endif
            </section>
        @endif

        @if ($community)
            <section class="modern-section section-variant-community" id="{{ $community['anchor'] }}">
                <div class="section-shell">
                    <div class="section-copy">
                        <span class="section-icon icon-community" aria-hidden="true"></span>
                        <p class="section-kicker">{{ $community['kicker'] }}</p>
                        <h3>{{ $community['title'] }}</h3>
                        <p class="section-sub">{{ $community['subtitle'] }}</p>

                        @if ($community['use_detail_page'])
                            <div class="section-rich">{{ $community['body_summary'] }}</div>
                            <a class="btn btn-soft" href="{{ $community['detail_url'] }}">Leer informacion completa</a>
                        @else
                            <div class="section-rich">{!! $community['body_html'] !!}</div>
                        @endif

                        @if (! empty($community['highlights']))
                            <div class="tag-list">
                                @foreach ($community['highlights'] as $highlight)
                                    <span>{{ $highlight }}</span>
                                @endforeach
                            </div>
                        @endif

                        @if ($community['cta_text'] !== '')
                            <a class="btn btn-soft {{ str_starts_with($community['cta_url'], '#') ? 'is-anchor' : '' }}" href="{{ $community['cta_url'] }}">{{ $community['cta_text'] }}</a>
                        @endif
                    </div>

                    @if ($community['images']->isNotEmpty())
                        <div class="section-media">
                            <div class="image-carousel section-gallery" data-carousel>
                                <div class="carousel-loading" data-carousel-loader aria-hidden="true"></div>
                                <div class="carousel-viewport">
                                    <div class="carousel-track" data-carousel-track>
                                        @foreach ($community['images'] as $image)
                                            <figure class="carousel-slide">
                                                <img src="{{ Storage::url($image->path) }}" alt="Imagen de {{ $community['title'] }}">
                                            </figure>
                                        @endforeach
                                    </div>
                                </div>

                                @if ($community['images']->count() > 1)
                                    <button type="button" class="carousel-btn prev" data-carousel-prev aria-label="Imagen anterior">&#10094;</button>
                                    <button type="button" class="carousel-btn next" data-carousel-next aria-label="Imagen siguiente">&#10095;</button>
                                    <div class="carousel-dots" data-carousel-dots></div>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>
            </section>
        @endif

        @foreach ($extraSections as $extraSection)
            <section class="modern-section section-variant-extra {{ $loop->even ? 'is-reverse' : '' }}" id="{{ $extraSection['anchor'] }}">
                <div class="section-shell">
                    <div class="section-copy">
                        <span class="section-icon icon-extra" aria-hidden="true"></span>
                        <p class="section-kicker">{{ $extraSection['kicker'] }}</p>
                        <h3>{{ $extraSection['title'] }}</h3>
                        <p class="section-sub">{{ $extraSection['subtitle'] }}</p>

                        @if ($extraSection['use_detail_page'])
                            <div class="section-rich">{{ $extraSection['body_summary'] }}</div>
                            <a class="btn btn-soft" href="{{ $extraSection['detail_url'] }}">Leer informacion completa</a>
                        @else
                            <div class="section-rich">{!! $extraSection['body_html'] !!}</div>
                        @endif

                        @if ($extraSection['cta_text'] !== '')
                            <a class="btn btn-soft {{ str_starts_with($extraSection['cta_url'], '#') ? 'is-anchor' : '' }}" href="{{ $extraSection['cta_url'] }}">{{ $extraSection['cta_text'] }}</a>
                        @endif
                    </div>

                    @if ($extraSection['images']->isNotEmpty())
                        <div class="section-media">
                            <div class="image-carousel section-gallery" data-carousel>
                                <div class="carousel-loading" data-carousel-loader aria-hidden="true"></div>
                                <div class="carousel-viewport">
                                    <div class="carousel-track" data-carousel-track>
                                        @foreach ($extraSection['images'] as $image)
                                            <figure class="carousel-slide">
                                                <img src="{{ Storage::url($image->path) }}" alt="Imagen de {{ $extraSection['title'] }}">
                                            </figure>
                                        @endforeach
                                    </div>
                                </div>

                                @if ($extraSection['images']->count() > 1)
                                    <button type="button" class="carousel-btn prev" data-carousel-prev aria-label="Imagen anterior">&#10094;</button>
                                    <button type="button" class="carousel-btn next" data-carousel-next aria-label="Imagen siguiente">&#10095;</button>
                                    <div class="carousel-dots" data-carousel-dots></div>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>
            </section>
        @endforeach

        @if ($contact)
            <section class="modern-section section-contact section-variant-contact" id="{{ $contact['anchor'] }}">
                <div class="section-shell">
                    <div class="section-copy">
                        <span class="section-icon icon-contact" aria-hidden="true"></span>
                        <p class="section-kicker">{{ $contact['kicker'] }}</p>
                        <h3>{{ $contact['title'] }}</h3>
                        <p class="section-sub">{{ $contact['subtitle'] }}</p>

                        @if ($contact['use_detail_page'])
                            <div class="section-rich">{{ $contact['body_summary'] }}</div>
                            <a class="btn btn-soft" href="{{ $contact['detail_url'] }}">Leer informacion completa</a>
                        @else
                            <div class="section-rich">{!! $contact['body_html'] !!}</div>
                        @endif

                        @if ($contact['cta_text'] !== '')
                            <a class="btn btn-primary {{ str_starts_with($contact['cta_url'], '#') ? 'is-anchor' : '' }}" href="{{ $contact['cta_url'] }}">{{ $contact['cta_text'] }}</a>
                        @endif
                    </div>

                    @if ($contact['images']->isNotEmpty())
                        <div class="section-media">
                            <div class="image-carousel section-gallery" data-carousel>
                                <div class="carousel-loading" data-carousel-loader aria-hidden="true"></div>
                                <div class="carousel-viewport">
                                    <div class="carousel-track" data-carousel-track>
                                        @foreach ($contact['images'] as $image)
                                            <figure class="carousel-slide">
                                                <img src="{{ Storage::url($image->path) }}" alt="Imagen de {{ $contact['title'] }}">
                                            </figure>
                                        @endforeach
                                    </div>
                                </div>

                                @if ($contact['images']->count() > 1)
                                    <button type="button" class="carousel-btn prev" data-carousel-prev aria-label="Imagen anterior">&#10094;</button>
                                    <button type="button" class="carousel-btn next" data-carousel-next aria-label="Imagen siguiente">&#10095;</button>
                                    <div class="carousel-dots" data-carousel-dots></div>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>
            </section>
        @endif
    </main>
@endsection
