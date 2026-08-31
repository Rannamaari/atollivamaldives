<!doctype html>
<html lang="en">
<head>
    @php
        $settings = $siteSettings ?? \App\Models\SiteSetting::current();
        $analyticsId = $settings->google_analytics_id ?: config('services.analytics.google_analytics_id');
        $tagManagerId = $settings->google_tag_manager_id ?: config('services.analytics.google_tag_manager_id');
        $seoDefaults = $seo ?? app(\App\Support\Seo\SeoManager::class)->defaults()->toArray();
        $decodeMeta = static fn (?string $value): string => html_entity_decode(trim((string) $value), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $pageTitle = $decodeMeta($__env->yieldContent('title', $seoDefaults['title']));
        $pageDescription = $decodeMeta($__env->yieldContent('description', $seoDefaults['description']));
        $shareImage = trim($__env->yieldContent('share_image', $seoDefaults['og_image']));
        $canonicalUrl = trim($__env->yieldContent('canonical', $seoDefaults['canonical']));
        $robots = trim($__env->yieldContent('robots', $seoDefaults['robots']));
        $brandTitle = $seoDefaults['site_name'];
        $schemaBlocks = $seoDefaults['schema'] ?? [];
        $searchConsoleVerification = $seoDefaults['search_console_verification'] ?? null;
    @endphp
    @if($tagManagerId)
        <script>
            (function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
            new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
            j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
            'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
            })(window,document,'script','dataLayer',@json($tagManagerId));
        </script>
    @elseif($analyticsId)
        <script async src="https://www.googletagmanager.com/gtag/js?id={{ $analyticsId }}"></script>
        <script>
            window.dataLayer = window.dataLayer || [];
            function gtag(){dataLayer.push(arguments);}
            gtag('js', new Date());
            gtag('config', @json($analyticsId));
        </script>
    @endif
    <meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <x-seo.social-meta :seo="$seoDefaults" :share="$socialShare ?? null" />
    <meta name="robots" content="{{ $robots }}">
    @if($searchConsoleVerification)
        <meta name="google-site-verification" content="{{ $searchConsoleVerification }}">
    @endif
    <link rel="icon" type="image/png" href="{{ asset('logo/optimized/favicon.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('logo/optimized/favicon.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,500;1,400&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    @php($cssVersion = fn (string $path) => asset($path).'?v='.filemtime(public_path($path)))
    @php($hasViteBuild = file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
    @php($recaptchaEnabled = config('services.recaptcha.enabled') && filled(config('services.recaptcha.site_key')))
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
    <link rel="stylesheet" href="{{ $cssVersion('css/about.css') }}">
    <link rel="stylesheet" href="{{ $cssVersion('css/forms.css') }}">
    <link rel="stylesheet" href="{{ $cssVersion('css/social-share.css') }}">
    @if($recaptchaEnabled)
        <script async src="https://www.google.com/recaptcha/api.js?render={{ config('services.recaptcha.site_key') }}"></script>
    @endif
    @yield('head')
    @foreach($schemaBlocks as $schema)
        @if(is_array($schema) && $schema !== [])
            <script type="application/ld+json">{!! json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
        @endif
    @endforeach
</head>
<body>
@if($tagManagerId)
    <noscript><iframe src="https://www.googletagmanager.com/ns.html?id={{ $tagManagerId }}"
    height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
@endif
@yield('content')
<script>
const recaptchaSiteKey = @json($recaptchaEnabled ? config('services.recaptcha.site_key') : null);

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

        guestToggle.setAttribute('aria-expanded', 'false');
        guestPopover.hidden = true;
        guestSection.classList.remove('is-open');

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

(() => {
    const today = new Date();
    today.setHours(0, 0, 0, 0);
    const todayString = today.toISOString().split('T')[0];

    const parseDateInputValue = (value) => {
        if (!value) {
            return null;
        }

        const parsed = new Date(`${value}T00:00:00`);

        return Number.isNaN(parsed.getTime()) ? null : parsed;
    };

    const enforceDateRules = (form) => {
        const dateInputs = form
            ? Array.from(form.querySelectorAll('input[type="date"]'))
            : Array.from(document.querySelectorAll('input[type="date"]'));

        dateInputs.forEach((input) => {
            if (!input.min || input.min < todayString) {
                input.min = todayString;
            }

            const inputDate = parseDateInputValue(input.value);

            if (inputDate && inputDate < today) {
                input.value = '';
            }
        });

        const arrivalInput = form?.querySelector('input[type="date"][name="arrival_date"], input[type="date"][name="check_in"]');
        const departureInput = form?.querySelector('input[type="date"][name="departure_date"], input[type="date"][name="check_out"]');

        if (!arrivalInput || !departureInput) {
            return;
        }

        const arrivalValue = parseDateInputValue(arrivalInput.value);
        const departureMin = arrivalValue && arrivalValue > today ? arrivalInput.value : todayString;

        departureInput.min = departureMin;

        const departureValue = parseDateInputValue(departureInput.value);

        if (departureValue && departureValue < parseDateInputValue(departureMin)) {
            departureInput.value = '';
        }
    };

    const forms = new Set(
        Array.from(document.querySelectorAll('input[type="date"]'))
            .map((input) => input.form)
            .filter(Boolean),
    );

    document.querySelectorAll('input[type="date"]').forEach((input) => {
        const form = input.form;

        if (!form) {
            if (!input.min || input.min < todayString) {
                input.min = todayString;
            }

            return;
        }

        input.addEventListener('input', () => enforceDateRules(form));
        input.addEventListener('change', () => enforceDateRules(form));
    });

    forms.forEach((form) => enforceDateRules(form));
})();

document.querySelectorAll('form[data-recaptcha-form]').forEach((form) => {
    if (!recaptchaSiteKey) {
        return;
    }

    let submitting = false;
    const runWhenRecaptchaReady = (callback, attempts = 30) => {
        if (typeof window.grecaptcha !== 'undefined') {
            window.grecaptcha.ready(callback);
            return;
        }

        if (attempts <= 0) {
            callback();
            return;
        }

        window.setTimeout(() => runWhenRecaptchaReady(callback, attempts - 1), 150);
    };

    form.addEventListener('submit', (event) => {
        if (submitting) {
            return;
        }

        event.preventDefault();

        const actionInput = form.querySelector('[data-recaptcha-action]');
        const tokenInput = form.querySelector('[data-recaptcha-token]');
        const action = actionInput?.value || 'inquiry_submit';

        runWhenRecaptchaReady(() => {
            if (typeof window.grecaptcha === 'undefined') {
                form.submit();
                return;
            }

            window.grecaptcha.execute(recaptchaSiteKey, { action }).then((token) => {
                if (!tokenInput) {
                    form.submit();
                    return;
                }

                tokenInput.value = token;
                submitting = true;
                form.submit();
            });
        });
    });
});

document.querySelectorAll('[data-social-share]').forEach((shareRoot) => {
    const toggle = shareRoot.querySelector('[data-social-share-toggle]');
    const menu = shareRoot.querySelector('[data-social-share-menu]');
    const feedback = shareRoot.querySelector('[data-social-feedback]');
    const title = shareRoot.dataset.socialTitle || '';
    const description = shareRoot.dataset.socialDescription || '';
    const caption = shareRoot.dataset.socialCaption || '';
    const url = shareRoot.dataset.socialUrl || '';
    const contentType = shareRoot.dataset.socialContentType || '';
    const contentId = shareRoot.dataset.socialContentId || '';
    const trackingEndpoint = shareRoot.dataset.socialTrackEndpoint || '';
    const nativeEnabled = shareRoot.dataset.socialNativeEnabled === 'true';
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

    const setFeedback = (message) => {
        if (feedback) {
            feedback.textContent = message;
        }
    };

    const track = (platform, trackedUrl = url) => {
        if (!trackingEndpoint || !contentType || !contentId || !csrfToken) {
            return;
        }

        window.fetch(trackingEndpoint, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
            },
            body: JSON.stringify({
                content_type: contentType,
                content_id: Number(contentId),
                platform,
                url: trackedUrl,
            }),
            keepalive: true,
        }).catch(() => {});
    };

    const closeMenu = () => {
        menu.hidden = true;
        menu.style.display = 'none';
        menu.setAttribute('aria-hidden', 'true');
        toggle?.setAttribute('aria-expanded', 'false');
    };

    toggle?.addEventListener('click', async () => {
        if (nativeEnabled && navigator.share && window.matchMedia('(max-width: 768px)').matches) {
            try {
                await navigator.share({ title, text: caption || description, url });
                track('native', url);
            } catch {
                // Let the fallback menu open if the share sheet is cancelled or unavailable.
            }
        }

        const isOpen = toggle.getAttribute('aria-expanded') === 'true';
        toggle.setAttribute('aria-expanded', String(!isOpen));
        menu.hidden = isOpen;
        menu.style.display = isOpen ? 'none' : 'grid';
        menu.setAttribute('aria-hidden', isOpen ? 'true' : 'false');
    });

    shareRoot.querySelectorAll('[data-social-platform]').forEach((link) => {
        link.addEventListener('click', () => track(link.dataset.socialPlatform, link.href));
    });

    shareRoot.querySelector('[data-social-copy-link]')?.addEventListener('click', async () => {
        await navigator.clipboard.writeText(url);
        track('copy_link', url);
        setFeedback('Link copied');
    });

    shareRoot.querySelector('[data-social-copy-caption]')?.addEventListener('click', async () => {
        await navigator.clipboard.writeText(caption);
        track('copy_caption', url);
        setFeedback('Caption copied');
    });

    shareRoot.querySelector('[data-social-native]')?.addEventListener('click', async () => {
        if (nativeEnabled && navigator.share) {
            try {
                await navigator.share({ title, text: caption || description, url });
                track('native', url);
                closeMenu();
                return;
            } catch {
                setFeedback('Sharing is not available right now');
                return;
            }
        }

        await navigator.clipboard.writeText(url);
        track('copy_link', url);
        setFeedback('Link copied');
    });

    document.addEventListener('click', (event) => {
        if (!shareRoot.contains(event.target)) {
            closeMenu();
        }
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') {
            closeMenu();
        }
    });
});
</script>
<script type="text/javascript">
var Tawk_API = Tawk_API || {}, Tawk_LoadStart = new Date();
(function() {
    var loaded = false;
    var loadTawk = function() {
        if (loaded) {
            return;
        }

        loaded = true;

        var s1 = document.createElement("script"), s0 = document.getElementsByTagName("script")[0];
        s1.async = true;
        s1.src = 'https://embed.tawk.to/6a8ebf1410a50a3444253078/1k0upkqdv';
        s1.charset = 'UTF-8';
        s1.setAttribute('crossorigin', '*');
        s0.parentNode.insertBefore(s1, s0);
    };

    ['pointerdown', 'scroll', 'keydown'].forEach(function(eventName) {
        window.addEventListener(eventName, loadTawk, { once: true, passive: true });
    });

    if ('requestIdleCallback' in window) {
        window.requestIdleCallback(loadTawk, { timeout: 5000 });
    } else {
        window.setTimeout(loadTawk, 4000);
    }
})();
</script>
</body></html>
