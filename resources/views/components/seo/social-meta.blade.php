@props([
    'seo' => [],
    'share' => null,
])

@php
    $meta = is_array($share) ? $share : [];
    $seo = is_array($seo) ? $seo : [];
    $title = $meta['title'] ?? $seo['title'] ?? 'Atolliva Maldives';
    $description = $meta['description'] ?? $seo['description'] ?? '';
    $canonical = $meta['canonical_url'] ?? $seo['canonical'] ?? url()->current();
    $image = $meta['image'] ?? $seo['og_image'] ?? asset('logo/optimized/atolliva-share.png');
    $siteName = $meta['site_name'] ?? $seo['site_name'] ?? 'Atolliva Maldives';
    $ogType = $meta['og_type'] ?? 'website';
    $twitterCard = $meta['twitter_card'] ?? $seo['twitter_card'] ?? 'summary_large_image';
@endphp

<title>{{ $title }}</title>
<meta name="description" content="{{ $description }}">
<link rel="canonical" href="{{ $canonical }}">
<meta property="og:type" content="{{ $ogType }}">
<meta property="og:site_name" content="{{ $siteName }}">
<meta property="og:title" content="{{ $meta['title'] ?? $seo['og_title'] ?? $title }}">
<meta property="og:description" content="{{ $meta['description'] ?? $seo['og_description'] ?? $description }}">
<meta property="og:url" content="{{ $meta['url'] ?? $canonical }}">
<meta property="og:image" content="{{ $image }}">
<meta property="og:image:secure_url" content="{{ $image }}">
<meta property="og:image:width" content="1200">
<meta property="og:image:height" content="630">
<meta name="twitter:card" content="{{ $twitterCard }}">
<meta name="twitter:title" content="{{ $meta['title'] ?? $seo['og_title'] ?? $title }}">
<meta name="twitter:description" content="{{ $meta['description'] ?? $seo['og_description'] ?? $description }}">
<meta name="twitter:image" content="{{ $image }}">
