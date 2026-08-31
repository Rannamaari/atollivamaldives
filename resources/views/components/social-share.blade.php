@props([
    'share' => [],
    'style' => 'default',
])

@php
    $share = is_array($share) ? $share : [];
    $title = $share['title'] ?? 'Atolliva Maldives';
    $styleClass = $style === 'compact' ? 'social-share social-share--compact' : 'social-share';
    $icon = static fn (string $name): string => match ($name) {
        'share' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M15 8l-6 4 6 4M17 5a3 3 0 1 0 0 6 3 3 0 0 0 0-6ZM7 9a3 3 0 1 0 0 6 3 3 0 0 0 0-6Zm10 4a3 3 0 1 0 0 6 3 3 0 0 0 0-6Z" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"/></svg>',
        'whatsapp' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M20 11.5A8.5 8.5 0 0 1 7.3 18.9L4 20l1.2-3.2A8.5 8.5 0 1 1 20 11.5Z" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7"/><path d="M9.5 9.5c.3-.7.6-.7.9-.7h.3c.2 0 .5.1.6.4l.7 1.6c.1.3.1.5-.1.7l-.5.6c-.1.1-.1.3 0 .4.5.8 1.2 1.5 2 2 .1.1.3.1.4 0l.6-.5c.2-.2.5-.2.7-.1l1.6.7c.3.1.4.4.4.6v.3c0 .3 0 .6-.7.9-.5.2-1 .3-1.5.2-1.2-.2-2.4-.9-3.6-2-1.1-1.1-1.8-2.3-2-3.6-.1-.5 0-1 .2-1.5Z" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.4"/></svg>',
        'facebook' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M14 8h2V5h-2c-2.2 0-4 1.8-4 4v2H8v3h2v5h3v-5h2.3l.7-3H13V9c0-.6.4-1 1-1Z" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"/></svg>',
        'x' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="m5 5 14 14M19 5 5 19" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"/></svg>',
        'link' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M10 13a5 5 0 0 0 7.1 0l2.1-2.1a5 5 0 0 0-7.1-7.1L10.9 5M14 11a5 5 0 0 0-7.1 0L4.8 13.1a5 5 0 0 0 7.1 7.1L13.1 19" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"/></svg>',
        'caption' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M7 6h10M7 10h10M7 14h6M6 3h12a2 2 0 0 1 2 2v14l-4-2-4 2-4-2-4 2V5a2 2 0 0 1 2-2Z" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7"/></svg>',
        'more' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 5v14M5 12h14" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"/></svg>',
        default => '',
    };
@endphp

@if($share !== [])
    <div
        class="{{ $styleClass }}"
        data-social-share
        data-social-title="{{ e($share['title'] ?? '') }}"
        data-social-description="{{ e($share['description'] ?? '') }}"
        data-social-caption="{{ e($share['caption'] ?? '') }}"
        data-social-url="{{ e($share['native_share_url'] ?? $share['url'] ?? '') }}"
        data-social-content-type="{{ e($share['content_type'] ?? '') }}"
        data-social-content-id="{{ e((string) ($share['content_id'] ?? '')) }}"
        data-social-track-endpoint="{{ route('social-share.track') }}"
        data-social-native-enabled="{{ config('app.env') !== 'testing' && \App\Models\SiteSetting::current()->enable_native_share ? 'true' : 'false' }}"
    >
        <button type="button" class="social-share__toggle" data-social-share-toggle aria-expanded="false" aria-label="Share {{ $title }}">
            {!! $icon('share') !!}
            <span>Share</span>
        </button>
        <div class="social-share__menu" data-social-share-menu hidden style="display:none;" aria-hidden="true">
            <div class="social-share__actions">
                <a href="{{ $share['whatsapp_url'] ?? '#' }}" target="_blank" rel="noopener" data-social-platform="whatsapp">{!! $icon('whatsapp') !!}<span>WhatsApp</span></a>
                <a href="{{ $share['facebook_url'] ?? '#' }}" target="_blank" rel="noopener" data-social-platform="facebook">{!! $icon('facebook') !!}<span>Facebook</span></a>
                <a href="{{ $share['x_url'] ?? '#' }}" target="_blank" rel="noopener" data-social-platform="x">{!! $icon('x') !!}<span>X</span></a>
                <button type="button" data-social-copy-link>{!! $icon('link') !!}<span>Copy Link</span></button>
                <button type="button" data-social-copy-caption>{!! $icon('caption') !!}<span>Copy Caption</span></button>
                <button type="button" data-social-native>{!! $icon('more') !!}<span>More</span></button>
            </div>
            <p class="social-share__feedback" data-social-feedback aria-live="polite"></p>
        </div>
    </div>
@endif
