<?php $__env->startSection('title','Maldives liveaboards — Micro Travel'); ?>
<?php $__env->startSection('description', strip_tags($page->intro ?: 'Discover liveaboard charters and voyages across the Maldives with Micro Travel.')); ?>
<?php $__env->startSection('content'); ?>
<div class="liveaboards-page">
    <?php echo $__env->make('partials.site-nav', ['whatsAppText' => 'Hello Micro Travel, I would like help planning a Maldives liveaboard journey.'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <section class="liveaboards-hero" style="background-image:url('<?php echo e($page->hero_image_url); ?>')">
        <div class="liveaboards-hero__shade"></div>
        <div class="liveaboards-hero__content">
            <p class="liveaboards-hero__eyebrow"><?php echo e($page->eyebrow); ?></p>
            <h1 class="liveaboards-hero__title"><?php echo e($page->title); ?></h1>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($page->intro): ?>
                <p class="liveaboards-hero__intro"><?php echo e($page->intro); ?></p>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </section>

    <section class="liveaboards-story">
        <div class="liveaboards-story__inner">
            <div>
                <p class="liveaboards-story__kicker">A different kind of Maldives escape</p>
                <h2>Live on the sea. Explore more. Travel together.</h2>
            </div>
            <div class="liveaboards-story__copy">
                <p><?php echo nl2br(e(strip_tags($page->body))); ?></p>
                <a href="#contact" class="liveaboards-story__link">Start planning your charter <span>→</span></a>
            </div>
        </div>
    </section>

    <section class="liveaboards-gallery" id="gallery">
        <div class="liveaboards-section-head">
            <p class="liveaboards-section-head__kicker">ONBOARD MOMENTS</p>
            <h2>See the liveaboard<br><em>experience.</em></h2>
        </div>
        <div class="liveaboards-gallery__grid">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $page->gallery_images ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $image): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <figure class="liveaboards-gallery__item">
                    <img src="<?php echo e(asset('storage/'.$image)); ?>" alt="Liveaboard Maldives gallery image">
                </figure>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <figure class="liveaboards-gallery__item liveaboards-gallery__item--wide">
                    <img src="<?php echo e($page->hero_image_url); ?>" alt="Liveaboard Maldives hero image">
                </figure>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </section>

    <section class="liveaboards-fleet" id="fleet">
        <div class="liveaboards-section-head">
            <p class="liveaboards-section-head__kicker">AVAILABLE LIVEABOARDS</p>
            <h2>Liveaboards we can<br><em>help you book.</em></h2>
        </div>
        <div class="liveaboards-fleet__grid">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $liveaboards; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $liveaboard): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <?php ($image = str_starts_with($liveaboard->cover_image,'http') ? $liveaboard->cover_image : asset('storage/'.$liveaboard->cover_image)); ?>
                <article class="liveaboards-fleet__card">
                    <a href="<?php echo e(route('accommodations.show',$liveaboard)); ?>">
                        <div class="liveaboards-fleet__photo">
                            <img src="<?php echo e($image); ?>" alt="<?php echo e($liveaboard->name); ?>">
                        </div>
                        <div class="liveaboards-fleet__meta">
                            <span><?php echo e($liveaboard->atoll); ?></span>
                            <span>FROM <?php echo e($liveaboard->currency); ?> <?php echo e(number_format($liveaboard->price_from)); ?></span>
                        </div>
                        <h3><?php echo e($liveaboard->name); ?></h3>
                        <p><?php echo e($liveaboard->summary); ?></p>
                    </a>
                </article>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <p class="liveaboards-empty">Add published boats from the `Travel Products → Liveaboards` area in the admin panel to show them here.</p>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </section>

    <section class="liveaboards-contact enquiry" id="contact">
        <div>
            <p class="kicker"><?php echo e(strtoupper($page->contact_heading)); ?></p>
            <h2>Tell us about your<br><em>ideal voyage.</em></h2>
            <p><?php echo e($page->contact_text); ?></p>
        </div>
        <form method="post" action="<?php echo e(route('inquiries.store')); ?>">
            <?php echo csrf_field(); ?>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('success')): ?>
                <p class="success"><?php echo e(session('success')); ?></p>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <input type="hidden" name="travel_type" value="liveaboard">
            <div class="form-grid">
                <label>Your name<input name="name" required value="<?php echo e(old('name')); ?>"></label>
                <label>Email address<input type="email" name="email" value="<?php echo e(old('email')); ?>"></label>
                <label>WhatsApp number<input name="phone" required value="<?php echo e(old('phone')); ?>"></label>
                <label>Preferred date<input type="date" name="travel_date" value="<?php echo e(old('travel_date')); ?>"></label>
                <label>Travellers<input type="number" name="travellers" min="1" value="<?php echo e(old('travellers', 2)); ?>"></label>
                <label>Approximate budget<input name="budget" placeholder="e.g. USD 8,000" value="<?php echo e(old('budget')); ?>"></label>
                <label class="wide">What kind of trip would you like?<textarea name="message" rows="5" placeholder="Private charter, diving route, family trip, celebration at sea..."><?php echo e(old('message')); ?></textarea></label>
            </div>
            <button>REQUEST A LIVEABOARD PLAN →</button>
        </form>
    </section>

    <?php echo $__env->make('partials.site-footer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/munad/Documents/Micro Travel/Micro-Travel-Laravel/resources/views/liveaboards/index.blade.php ENDPATH**/ ?>