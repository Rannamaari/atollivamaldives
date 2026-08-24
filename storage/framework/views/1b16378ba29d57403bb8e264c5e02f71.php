<footer>
    <?php echo $__env->make('partials.logo', ['dark' => true], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <p>Travel made personal, by people who know the islands.</p>
    <div>
        <a href="<?php echo e(request()->routeIs('home') ? '#stays' : route('home').'#stays'); ?>">Stays</a>
        <a href="<?php echo e(route('liveaboards.index')); ?>">Liveaboards</a>
        <a href="<?php echo e(route('blog.index')); ?>">Blog</a>
        <a href="<?php echo e(route('faq')); ?>">FAQ</a>
        <a href="<?php echo e(request()->routeIs('home') ? '#about' : route('home').'#about'); ?>">About</a>
    </div>
    <div class="footer-links">
        <p class="footer-links__title">Things to do in Maldives</p>
        <a href="<?php echo e(route('blog.show', 'seaplane-tours-in-maldives')); ?>">Seaplane Tours</a>
        <a href="<?php echo e(route('blog.show', 'island-hopping-in-maldives')); ?>">Island Hopping</a>
        <a href="<?php echo e(route('blog.show', 'water-sports-and-activities-in-maldives')); ?>">Water Sports &amp; Activities</a>
        <a href="<?php echo e(route('blog.show', 'diving-in-maldives')); ?>">Diving</a>
    </div>
    <small>© <?php echo e(date('Y')); ?> Micro Travel · Maldives</small>
</footer>
<?php /**PATH /Users/munad/Documents/Micro Travel/Micro-Travel-Laravel/resources/views/partials/site-footer.blade.php ENDPATH**/ ?>