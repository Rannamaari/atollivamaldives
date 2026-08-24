<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
    <title><?php echo $__env->yieldContent('title','Micro Travel'); ?></title>
    <meta name="description" content="<?php echo $__env->yieldContent('description','Maldives stays and experiences, thoughtfully planned by local experts.'); ?>">
    <link rel="icon" type="image/png" href="<?php echo e(asset('logo/favicon.png')); ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,500;1,400&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <?php ($cssVersion = fn (string $path) => asset($path).'?v='.filemtime(public_path($path))); ?>
    <link rel="stylesheet" href="<?php echo e($cssVersion('css/microtravel.css')); ?>">
    <link rel="stylesheet" href="<?php echo e($cssVersion('css/nav-refresh.css')); ?>">
    <link rel="stylesheet" href="<?php echo e($cssVersion('css/accommodation-gallery.css')); ?>">
    <link rel="stylesheet" href="<?php echo e($cssVersion('css/why-book.css')); ?>">
    <link rel="stylesheet" href="<?php echo e($cssVersion('css/liveaboards.css')); ?>">
    <link rel="stylesheet" href="<?php echo e($cssVersion('css/blog-offer.css')); ?>">
    <link rel="stylesheet" href="<?php echo e($cssVersion('css/faq.css')); ?>">
    <link rel="stylesheet" href="<?php echo e($cssVersion('css/forms.css')); ?>">
    <?php echo $__env->yieldContent('head'); ?>
</head>
<body>
<?php echo $__env->yieldContent('content'); ?>
<script>
document.querySelector('[data-menu]')?.addEventListener('click',()=>document.querySelector('[data-nav]')?.classList.toggle('open'));
document.querySelectorAll('[data-tab]').forEach(b=>b.addEventListener('click',()=>{document.querySelectorAll('[data-tab]').forEach(x=>x.classList.remove('active'));b.classList.add('active');document.querySelector('[name=type]').value=b.dataset.tab.toLowerCase()}));
</script>
</body></html>
<?php /**PATH /Users/munad/Documents/Micro Travel/Micro-Travel-Laravel/resources/views/layouts/app.blade.php ENDPATH**/ ?>