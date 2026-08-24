<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
    <title>@yield('title','Micro Travel')</title>
    <meta name="description" content="@yield('description','Maldives stays and experiences, thoughtfully planned by local experts.')">
    <link rel="icon" type="image/png" href="{{ asset('logo/favicon.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,500;1,400&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    @php($cssVersion = fn (string $path) => asset($path).'?v='.filemtime(public_path($path)))
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
document.querySelector('[data-menu]')?.addEventListener('click',()=>document.querySelector('[data-nav]')?.classList.toggle('open'));
document.querySelectorAll('[data-tab]').forEach(b=>b.addEventListener('click',()=>{document.querySelectorAll('[data-tab]').forEach(x=>x.classList.remove('active'));b.classList.add('active');document.querySelector('[name=type]').value=b.dataset.tab.toLowerCase()}));
</script>
</body></html>
