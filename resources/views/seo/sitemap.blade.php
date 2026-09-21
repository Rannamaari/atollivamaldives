<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
        xmlns:xhtml="http://www.w3.org/1999/xhtml"
        xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">
@foreach($urls as $url)
    <url>
        <loc>{{ $url['loc'] }}</loc>
@if(!empty($url['lastmod']))
        <lastmod>{{ $url['lastmod'] }}</lastmod>
@endif
@foreach($url['alternates'] ?? [] as $language => $alternateUrl)
        <xhtml:link rel="alternate" hreflang="{{ $language }}" href="{{ $alternateUrl }}" />
@endforeach
@if(!empty($url['image']))
        <image:image>
            <image:loc>{{ $url['image'] }}</image:loc>
@if(!empty($url['image_title']))
            <image:title>{{ $url['image_title'] }}</image:title>
@endif
        </image:image>
@endif
        <changefreq>{{ $url['changefreq'] }}</changefreq>
        <priority>{{ $url['priority'] }}</priority>
    </url>
@endforeach
</urlset>
