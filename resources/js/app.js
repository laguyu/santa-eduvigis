document.querySelectorAll('[data-rich-editor]').forEach((editorRoot) => {
	const content = editorRoot.querySelector('[data-rich-content]');
	const textarea = editorRoot.querySelector('textarea[name="body"]');
	const toolbarButtons = editorRoot.querySelectorAll('[data-cmd]');
	const colorPicker = editorRoot.querySelector('[data-color-picker]');

	if (!content || !textarea) {
		return;
	}

	const syncToTextarea = () => {
		textarea.value = content.innerHTML.trim();
	};

	toolbarButtons.forEach((button) => {
		button.addEventListener('click', () => {
			const cmd = button.getAttribute('data-cmd');

			if (!cmd) {
				return;
			}

			if (cmd === 'createLink') {
				const url = window.prompt('Ingresa la URL del enlace:');
				if (url) {
					document.execCommand('createLink', false, url);
				}
			} else {
				document.execCommand(cmd, false, null);
			}

			content.focus();
			syncToTextarea();
		});
	});

	if (colorPicker) {
		colorPicker.addEventListener('input', (event) => {
			document.execCommand('foreColor', false, event.target.value);
			content.focus();
			syncToTextarea();
		});
	}

	content.addEventListener('input', syncToTextarea);

	const form = editorRoot.closest('form');
	if (form) {
		form.addEventListener('submit', syncToTextarea);
	}

	syncToTextarea();
});

document.querySelectorAll('form').forEach((form) => {
	const targetSelect = form.querySelector('[data-cta-target]');
	const customInput = form.querySelector('[data-cta-custom]');
	const hiddenInput = form.querySelector('[data-cta-hidden]');

	if (!targetSelect || !hiddenInput) {
		return;
	}

	const syncCtaValue = () => {
		const selected = targetSelect.value;

		if (selected === 'custom') {
			if (customInput) {
				customInput.hidden = false;
				hiddenInput.value = customInput.value.trim();
			}

			return;
		}

		if (customInput) {
			customInput.hidden = true;
		}

		hiddenInput.value = selected;
	};

	targetSelect.addEventListener('change', syncCtaValue);
	customInput?.addEventListener('input', syncCtaValue);
	form.addEventListener('submit', syncCtaValue);
	syncCtaValue();
});

document.querySelectorAll('[data-carousel]').forEach((carousel) => {
	const track = carousel.querySelector('[data-carousel-track]');
	const prev = carousel.querySelector('[data-carousel-prev]');
	const next = carousel.querySelector('[data-carousel-next]');
	const dotsContainer = carousel.querySelector('[data-carousel-dots]');
	const loader = carousel.querySelector('[data-carousel-loader]');

	if (!track) {
		return;
	}

	const slides = Array.from(track.children);

	// Performance: eager-load hero images, lazy-load the rest.
	carousel.querySelectorAll('img').forEach((img) => {
		const isHeroCarousel = Boolean(carousel.closest('.hero-gallery'));
		if (!img.hasAttribute('loading')) {
			img.loading = isHeroCarousel ? 'eager' : 'lazy';
		}
		img.decoding = 'async';
		if (!isHeroCarousel && !img.hasAttribute('fetchpriority')) {
			img.setAttribute('fetchpriority', 'low');
		}
	});

	if (slides.length <= 1) {
		if (loader) {
			loader.remove();
		}
		return;
	}

	const firstImage = slides[0].querySelector('img');
	if (loader && firstImage) {
		if (firstImage.complete) {
			loader.classList.add('is-hidden');
		} else {
			firstImage.addEventListener('load', () => loader.classList.add('is-hidden'), { once: true });
			firstImage.addEventListener('error', () => loader.classList.add('is-hidden'), { once: true });
		}
	}

	let currentIndex = 0;
	let autoplayId = null;
	let isInViewport = true;
	let isInteracting = false;

	const renderDots = () => {
		if (!dotsContainer) {
			return;
		}

		dotsContainer.innerHTML = '';
		slides.forEach((_, index) => {
			const dot = document.createElement('button');
			dot.type = 'button';
			dot.className = `carousel-dot${index === currentIndex ? ' active' : ''}`;
			dot.setAttribute('aria-label', `Ir a imagen ${index + 1}`);
			dot.addEventListener('click', () => {
				currentIndex = index;
				update();
			});
			dotsContainer.appendChild(dot);
		});
	};

	const update = () => {
		track.style.transform = `translateX(-${currentIndex * 100}%)`;
		renderDots();
	};

	const goNext = () => {
		currentIndex = (currentIndex + 1) % slides.length;
		update();
	};

	const goPrev = () => {
		currentIndex = (currentIndex - 1 + slides.length) % slides.length;
		update();
	};

	prev?.addEventListener('click', goPrev);
	next?.addEventListener('click', goNext);

	const startAutoplay = () => {
		if (autoplayId || !isInViewport || isInteracting) {
			return;
		}

		autoplayId = window.setInterval(goNext, 6000);
	};

	const stopAutoplay = () => {
		if (autoplayId) {
			window.clearInterval(autoplayId);
			autoplayId = null;
		}
	};

	const setInteracting = (value) => {
		isInteracting = value;
		if (isInteracting) {
			stopAutoplay();
			return;
		}

		startAutoplay();
	};

	carousel.addEventListener('mouseenter', () => setInteracting(true));
	carousel.addEventListener('mouseleave', () => setInteracting(false));
	carousel.addEventListener('focusin', () => setInteracting(true));
	carousel.addEventListener('focusout', () => setInteracting(false));

	if ('IntersectionObserver' in window) {
		const observer = new IntersectionObserver(
			(entries) => {
				const entry = entries[0];
				isInViewport = entry.isIntersecting && entry.intersectionRatio >= 0.35;

				if (isInViewport) {
					startAutoplay();
					return;
				}

				stopAutoplay();
			},
			{ threshold: [0, 0.35, 0.75] }
		);

		observer.observe(carousel);
	}

	document.addEventListener('visibilitychange', () => {
		if (document.hidden) {
			stopAutoplay();
			return;
		}

		startAutoplay();
	});

	update();
	startAutoplay();
});

const contrastToggle = document.querySelector('[data-contrast-toggle]');
if (contrastToggle) {
	const storageKey = 'parroquia.contrastMode';
	const body = document.body;

	const applyContrast = (enabled) => {
		body.classList.toggle('high-contrast', enabled);
		contrastToggle.setAttribute('aria-pressed', enabled ? 'true' : 'false');
		contrastToggle.textContent = enabled ? 'Contraste normal' : 'Alto contraste';
	};

	const saved = window.localStorage.getItem(storageKey);
	applyContrast(saved === 'high');

	contrastToggle.addEventListener('click', () => {
		const enabled = !body.classList.contains('high-contrast');
		applyContrast(enabled);
		window.localStorage.setItem(storageKey, enabled ? 'high' : 'normal');
	});
}

const livePanel = document.querySelector('[data-live-panel]');
if (livePanel) {
	const timeNode = livePanel.querySelector('[data-live-time]');
	const dateNode = livePanel.querySelector('[data-live-date]');

	const updateClock = () => {
		const now = new Date();
		const timeFormatter = new Intl.DateTimeFormat('es-CO', {
			hour: '2-digit',
			minute: '2-digit',
			hour12: true,
		});
		const dateFormatter = new Intl.DateTimeFormat('es-CO', {
			weekday: 'long',
			day: '2-digit',
			month: 'long',
		});

		if (timeNode) {
			timeNode.textContent = timeFormatter.format(now);
		}

		if (dateNode) {
			const raw = dateFormatter.format(now);
			dateNode.textContent = raw.charAt(0).toUpperCase() + raw.slice(1);
		}
	};

	updateClock();
	window.setInterval(updateClock, 30000);
}
