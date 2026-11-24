<?php
    use App\Support\Storefront;
    $site = Storefront::site();
    $isContact = $page->template === 'Contact Us';
    $banner = $page->imageFile ? asset($page->image()) : null;
    $footerPages = \App\Models\Post::where('type', 0)->where('status', 'active')
        ->whereIn('slug', ['about-us', 'contact-us', 'privacy-policy', 'terms-and-conditions', 'refund-and-return-policy'])
        ->get(['id', 'name', 'slug']);
    $wa = preg_replace('/^0/', '880', preg_replace('/\D/', '', $site['phone']));
?>

<?php $__env->startSection('title', $page->seo_title ?: $page->name . ' | ' . $site['name']); ?>
<?php $__env->startSection('meta_description', $page->seo_description ?: (\Illuminate\Support\Str::limit(strip_tags((string) ($page->short_description ?: $page->description)), 160) ?: $site['meta_description'])); ?>
<?php if($page->seo_keyword): ?>
    <?php $__env->startSection('meta_keywords', $page->seo_keyword); ?>
<?php endif; ?>

<?php $__env->startSection('content'); ?>
<section class="page-hero">
    <div class="hero__glow hero__glow--1"></div>
    <div class="container position-relative">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb breadcrumb--light">
                <li class="breadcrumb-item"><a href="<?php echo e(route('index')); ?>">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page"><?php echo e($page->name); ?></li>
            </ol>
        </nav>
        <h1><?php echo e($page->name); ?></h1>
        <?php if($page->short_description): ?>
            <p><?php echo e(strip_tags($page->short_description)); ?></p>
        <?php elseif($isContact): ?>
            <p>Have a question about a course or your payment? Our team is happy to help.</p>
        <?php endif; ?>
    </div>
</section>

<section class="page-body">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-8">
                <article class="page-card">
                    <?php if($banner): ?><img src="<?php echo e($banner); ?>" alt="<?php echo e($page->name); ?>" class="page-banner"><?php endif; ?>

                    <?php if($isContact): ?>
                        <div class="contact-grid">
                            <?php if($site['phone']): ?>
                                <a href="tel:<?php echo e(Storefront::tel()); ?>" class="contact-tile"><span class="contact-tile__icon"><i class="bi bi-telephone"></i></span><span><small>Call us</small><strong><?php echo e($site['phone']); ?></strong></span></a>
                                <a href="https://wa.me/<?php echo e($wa); ?>" target="_blank" rel="noopener" class="contact-tile"><span class="contact-tile__icon" style="background:#25D366"><i class="bi bi-whatsapp"></i></span><span><small>WhatsApp</small><strong>Chat with us</strong></span></a>
                            <?php endif; ?>
                            <?php if($site['email']): ?>
                                <a href="mailto:<?php echo e($site['email']); ?>" class="contact-tile"><span class="contact-tile__icon" style="background:#EA580C"><i class="bi bi-envelope"></i></span><span><small>Email</small><strong><?php echo e($site['email']); ?></strong></span></a>
                            <?php endif; ?>
                            <?php if(isset($site['social']['facebook'])): ?>
                                <a href="<?php echo e($site['social']['facebook']); ?>" target="_blank" rel="noopener" class="contact-tile"><span class="contact-tile__icon" style="background:#1877F2"><i class="bi bi-messenger"></i></span><span><small>Facebook</small><strong>Message our page</strong></span></a>
                            <?php endif; ?>
                            <div class="contact-tile"><span class="contact-tile__icon" style="background:#0F172A"><i class="bi bi-geo-alt"></i></span><span><small>Address</small><strong><?php echo e($site['address']); ?></strong></span></div>
                            <div class="contact-tile"><span class="contact-tile__icon" style="background:#12B76A"><i class="bi bi-clock"></i></span><span><small>Support hours</small><strong>Sat – Thu, 10am – 8pm</strong></span></div>
                        </div>
                    <?php endif; ?>

                    <?php if(trim(strip_tags((string) $page->description))): ?>
                        <div class="cms-content"><?php echo clean_html($page->description); ?></div>
                    <?php elseif(!$isContact): ?>
                        <p class="text-muted mb-0">This page is being updated. Please check back soon.</p>
                    <?php endif; ?>
                </article>
            </div>

            <aside class="col-lg-4">
                <?php if($footerPages->count()): ?>
                    <div class="side-card">
                        <h6>Information</h6>
                        <ul class="side-links">
                            <?php $__currentLoopData = $footerPages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $fp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <li><a href="<?php echo e(route('pageView', $fp->slug)); ?>" class="<?php echo e($fp->id === $page->id ? 'is-active' : ''); ?>"><?php echo e($fp->name); ?> <i class="bi bi-chevron-right"></i></a></li>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </ul>
                    </div>
                <?php endif; ?>
                <div class="side-card side-cta">
                    <h5>Ready to start learning?</h5>
                    <p class="mb-3">Explore practical courses taught in Bangla, or start with a free course today.</p>
                    <div class="d-grid gap-2">
                        <a href="<?php echo e(route('courses.index')); ?>" class="btn btn-light">Browse Courses</a>
                        <a href="<?php echo e(route('courses.free')); ?>" class="btn btn-outline-light">Watch Free Courses</a>
                    </div>
                </div>
                <?php if(!$isContact && $site['phone']): ?>
                    <div class="side-card">
                        <h6>Need help?</h6>
                        <a href="tel:<?php echo e(Storefront::tel()); ?>" class="contact-tile"><span class="contact-tile__icon"><i class="bi bi-headset"></i></span><span><small>Call our team</small><strong><?php echo e($site['phone']); ?></strong></span></a>
                    </div>
                <?php endif; ?>
            </aside>
        </div>
    </div>
</section>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('frontend.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\xampp\htdocs\trainersbd\resources\views/frontend/page.blade.php ENDPATH**/ ?>