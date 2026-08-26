<!doctype html>
<html lang="en">
<head>
    @php
        $brandTitle = 'Atolliva Maldives';
        $brandTagline = 'YOUR MALDIVES, THOUGHTFULLY PLANNED';
        $pageTitle = trim($__env->yieldContent('title', $brandTitle));
        $pageDescription = trim($__env->yieldContent('description', $brandTagline));
        $shareImage = $__env->yieldContent('share_image', asset('logo/Atolliva Maldives Logo Transparent.png'));
    @endphp
    <meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
    <title>{{ $pageTitle }}</title>
    <meta name="description" content="{{ $pageDescription }}">
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="{{ $brandTitle }}">
    <meta property="og:title" content="{{ $pageTitle }}">
    <meta property="og:description" content="{{ $pageDescription }}">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:image" content="{{ $shareImage }}">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $pageTitle }}">
    <meta name="twitter:description" content="{{ $pageDescription }}">
    <meta name="twitter:image" content="{{ $shareImage }}">
    <link rel="icon" type="image/png" href="{{ asset('logo/favicon.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,500;1,400&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    @php($cssVersion = fn (string $path) => asset($path).'?v='.filemtime(public_path($path)))
    @php($hasViteBuild = file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
    @if($hasViteBuild)
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
    <link rel="stylesheet" href="{{ $cssVersion('css/microtravel.css') }}">
    <link rel="stylesheet" href="{{ $cssVersion('css/nav-refresh.css') }}">
    <link rel="stylesheet" href="{{ $cssVersion('css/accommodation-gallery.css') }}">
    <link rel="stylesheet" href="{{ $cssVersion('css/why-book.css') }}">
    <link rel="stylesheet" href="{{ $cssVersion('css/liveaboards.css') }}">
    <link rel="stylesheet" href="{{ $cssVersion('css/blog-offer.css') }}">
    <link rel="stylesheet" href="{{ $cssVersion('css/faq.css') }}">
    <link rel="stylesheet" href="{{ $cssVersion('css/forms.css') }}">
    @yield('head')
</head>
<body>
@yield('content')
<script>
const navToggle = document.querySelector('[data-menu]');
const navMenu = document.querySelector('[data-nav]');

navToggle?.addEventListener('click', () => {
    const isOpen = navMenu?.classList.toggle('open');
    navToggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
    document.body.classList.toggle('nav-open', Boolean(isOpen));
});

document.querySelectorAll('[data-nav] a').forEach((link) => {
    link.addEventListener('click', () => {
        navMenu?.classList.remove('open');
        navToggle?.setAttribute('aria-expanded', 'false');
        document.body.classList.remove('nav-open');
    });
});
document.querySelectorAll('.finder--premium').forEach((finder) => {
    const typeInput = finder.querySelector('[name="type"]');
    const tabButtons = finder.querySelectorAll('[data-tab]');
    const guestSection = finder.querySelector('[data-guests]');
    const guestToggle = finder.querySelector('[data-guests-toggle]');
    const guestPopover = finder.querySelector('[data-guests-popover]');
    const guestSummary = finder.querySelector('[data-guests-summary]');
    const guestInputs = ['adults', 'children', 'rooms'].reduce((carry, key) => {
        carry[key] = finder.querySelector(`[data-guests-input="${key}"]`);
        return carry;
    }, {});

    const updateSummary = () => {
        if (!guestSummary) return;
        const adults = Number(guestInputs.adults?.value || 2);
        const children = Number(guestInputs.children?.value || 0);
        guestSummary.textContent = `${adults} Adult${adults === 1 ? '' : 's'} · ${children} Child${children === 1 ? '' : 'ren'}`;
    };

    const syncStepperValue = (key) => {
        finder.querySelectorAll(`[data-stepper-value="${key}"]`).forEach((node) => {
            node.textContent = guestInputs[key]?.value ?? '';
        });
    };

    tabButtons.forEach((button) => {
        button.addEventListener('click', () => {
            tabButtons.forEach((tab) => tab.classList.remove('active'));
            button.classList.add('active');
            if (typeInput) {
                typeInput.value = button.dataset.tab.toLowerCase();
            }
        });
    });

    if (guestSection && guestToggle && guestPopover) {
        const limits = { adults: 1, children: 0, rooms: 1 };

        guestToggle.addEventListener('click', () => {
            const isOpen = guestToggle.getAttribute('aria-expanded') === 'true';
            guestToggle.setAttribute('aria-expanded', String(!isOpen));
            guestPopover.hidden = isOpen;
            guestSection.classList.toggle('is-open', !isOpen);
        });

        guestPopover.querySelectorAll('[data-stepper-target]').forEach((button) => {
            button.addEventListener('click', () => {
                const key = button.dataset.stepperTarget;
                const direction = Number(button.dataset.stepperDirection || 0);
                const input = guestInputs[key];

                if (!input) {
                    return;
                }

                const currentValue = Number(input.value || 0);
                const nextValue = Math.max(limits[key] ?? 0, currentValue + direction);
                input.value = String(nextValue);
                syncStepperValue(key);
                updateSummary();
            });
        });

        document.addEventListener('click', (event) => {
            if (!guestSection.contains(event.target)) {
                guestToggle.setAttribute('aria-expanded', 'false');
                guestPopover.hidden = true;
                guestSection.classList.remove('is-open');
            }
        });

        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape') {
                guestToggle.setAttribute('aria-expanded', 'false');
                guestPopover.hidden = true;
                guestSection.classList.remove('is-open');
            }
        });
    }

    Object.keys(guestInputs).forEach(syncStepperValue);
    updateSummary();
});
</script>
</body></html>
