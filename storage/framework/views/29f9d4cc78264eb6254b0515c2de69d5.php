<?php
    $wa = 'https://wa.me/'.env('MICRO_TRAVEL_WHATSAPP', '9609996210').'?text='.urlencode($whatsAppText ?? 'Hello Micro Travel, I would like help planning a Maldives holiday.');
    $isHome = request()->routeIs('home');
    $isLiveaboards = request()->routeIs('liveaboards.*');
    $isBlog = request()->routeIs('blog.*');
    $isStays = request()->routeIs('accommodations.*');
    $homeSection = fn (string $hash) => $isHome ? $hash : route('home').$hash;
?>

<header class="<?php echo e($class ?? ''); ?>">
    <?php echo $__env->make('partials.logo', ['dark' => $dark ?? false], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <nav data-nav>
        <a href="<?php echo e($homeSection('#stays')); ?>" class="<?php echo \Illuminate\Support\Arr::toCssClasses(['is-active' => $isStays]); ?>">Stays</a>
        <a href="<?php echo e(route('liveaboards.index')); ?>" class="<?php echo \Illuminate\Support\Arr::toCssClasses(['is-active' => $isLiveaboards]); ?>">Liveaboards</a>
        <a href="<?php echo e($homeSection('#experiences')); ?>">Experiences</a>
        <a href="<?php echo e(route('blog.index')); ?>" class="<?php echo \Illuminate\Support\Arr::toCssClasses(['is-active' => $isBlog]); ?>">Blog</a>
        <a href="<?php echo e($homeSection('#about')); ?>">About us</a>
    </nav>

    <a class="nav-cta" href="<?php echo e($wa); ?>" target="_blank">Plan my trip ↗</a>
    <button class="menu" data-menu aria-label="Toggle menu"><span></span><span></span></button>
</header>
<?php /**PATH /Users/munad/Documents/Micro Travel/Micro-Travel-Laravel/resources/views/partials/site-nav.blade.php ENDPATH**/ ?>