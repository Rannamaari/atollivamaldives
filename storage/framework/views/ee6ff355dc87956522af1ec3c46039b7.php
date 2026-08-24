<?php $__env->startSection('title','Maldives FAQ | Atolliva Maldives Travel Questions'); ?>
<?php $__env->startSection('description','Find answers to common Maldives travel questions about transfers, weather, visas, resorts, liveaboards, budgets, honeymoons, and planning with Atolliva Maldives.'); ?>
<?php $__env->startSection('head'); ?>
<script type="application/ld+json">
<?php echo json_encode([
    '<?php $__contextArgs = [];
if (context()->has($__contextArgs[0])) :
if (isset($value)) { $__contextPrevious[] = $value; }
$value = context()->get($__contextArgs[0]); ?>' => 'https://schema.org',
    '@type' => 'FAQPage',
    'mainEntity' => $faqSchema,
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT); ?>

</script>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
<?php echo $__env->make('partials.site-nav', ['whatsAppText' => 'Hello Atolliva Maldives, I would like help planning my Maldives holiday.'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<main class="faq-page">
    <section class="faq-hero">
        <div class="faq-hero__backdrop"></div>
        <div class="faq-hero__inner">
            <p class="faq-hero__kicker">MALDIVES FAQ</p>
            <h1>Frequently asked<br><em>questions.</em></h1>
            <p class="faq-hero__intro">Everything travellers ask most often about the Maldives, from airports and transfers to resorts, weather, liveaboards, and planning your holiday with Atolliva Maldives.</p>
        </div>
    </section>

    <section class="faq-summary">
        <div class="faq-summary__inner">
            <div>
                <p class="kicker">QUICK GUIDE</p>
                <h2>Helpful answers,<br><em>all in one place.</em></h2>
            </div>
            <p>Use this page to explore the essentials before you book. It is designed to help both first-time visitors and returning travellers understand how Maldives holidays work and what Atolliva Maldives can arrange for you.</p>
        </div>
    </section>

    <section class="faq-content">
        <div class="faq-content__inner">
            <aside class="faq-index" aria-label="FAQ sections">
                <p class="faq-index__title">On this page</p>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $sections; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $section): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <a href="#<?php echo e(\Illuminate\Support\Str::slug($section['title'])); ?>"><?php echo e($section['title']); ?></a>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </aside>

            <div class="faq-groups">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $sections; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $section): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <section class="faq-group" id="<?php echo e(\Illuminate\Support\Str::slug($section['title'])); ?>">
                        <div class="faq-group__head">
                            <p class="kicker"><?php echo e(strtoupper($loop->iteration === 1 ? 'Plan your trip' : 'Travel with Atolliva')); ?></p>
                            <h2><?php echo e($section['title']); ?></h2>
                            <p><?php echo e($section['intro']); ?></p>
                        </div>

                        <div class="faq-items">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $section['items']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <details class="faq-item" <?php if($loop->first && $loop->parent->first): ?> open <?php endif; ?>>
                                    <summary><?php echo e($item['question']); ?></summary>
                                    <div class="faq-item__answer">
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $item['answer']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $paragraph): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <p><?php echo e($paragraph); ?></p>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </div>
                                </details>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                    </section>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>
    </section>

    <section class="faq-cta">
        <div class="faq-cta__inner">
            <p class="faq-cta__kicker">NEED PERSONALISED ADVICE?</p>
            <h2>Tell us what kind of<br><em>Maldives holiday</em> you want.</h2>
            <p>Whether you are comparing resorts, planning a honeymoon, arranging transfers, or choosing between a guesthouse and liveaboard, Atolliva Maldives can help you narrow it down.</p>
            <a href="https://wa.me/<?php echo e(env('MICRO_TRAVEL_WHATSAPP','9609996210')); ?>?text=<?php echo e(urlencode('Hello Atolliva Maldives, I would like help planning my Maldives holiday.')); ?>" target="_blank">ASK ON WHATSAPP <span>↗</span></a>
        </div>
    </section>
</main>

<?php echo $__env->make('partials.site-footer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/munad/Documents/Micro Travel/Micro-Travel-Laravel/resources/views/faq.blade.php ENDPATH**/ ?>