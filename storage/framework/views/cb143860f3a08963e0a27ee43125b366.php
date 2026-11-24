<?php
    use App\Support\Storefront;
    $ins  = $course->instructor;
    $cat  = $course->category;
    $off  = $course->discountPercent();
    $free = $course->isFree();
    $url  = $course->url();
    $img  = $course->thumbnailUrl();
    $rating = $course->rating();
?>
<article class="course-card" data-course-card data-category="<?php echo e($cat->slug ?? ''); ?>" data-type="<?php echo e($course->type); ?>" data-title="<?php echo e(\Illuminate\Support\Str::lower($course->title . ' ' . ($cat->name ?? ''))); ?>">
    <a href="<?php echo e($url); ?>" class="course-thumb <?php echo e($img ? 'course-thumb--img' : ''); ?>" aria-label="<?php echo e($course->title); ?>">
        <?php if($img): ?>
            <img src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7" data-src="<?php echo e($img); ?>" alt="<?php echo e($course->thumbnail_alt ?: $course->title); ?>" class="lazy" decoding="async">
        <?php else: ?>
            <i class="bi <?php echo e($course->icon()); ?> course-thumb__icon"></i>
        <?php endif; ?>
        <?php if($cat): ?><span class="course-thumb__cat"><?php echo e($cat->name); ?></span><?php endif; ?>
        <?php if($course->badge): ?>
            <span class="course-badge course-badge--<?php echo e(\Illuminate\Support\Str::slug($course->badge)); ?>"><?php echo e($course->badge); ?></span>
        <?php endif; ?>
        <?php if($free): ?>
            <span class="course-thumb__play"><i class="bi bi-play-fill"></i></span>
        <?php endif; ?>
    </a>
    <div class="course-card__body">
        <div class="course-meta">
            <span><i class="bi bi-play-circle"></i> <?php echo e($course->lessonsCount()); ?> lessons</span>
            <?php if($course->duration): ?><span><i class="bi bi-clock"></i> <?php echo e($course->duration); ?></span><?php endif; ?>
        </div>
        <h3 class="course-card__title"><a href="<?php echo e($url); ?>"><?php echo e($course->title); ?></a></h3>
        <?php if($ins): ?>
            <div class="course-card__instructor">
                <span class="avatar avatar--sm"><?php echo e($ins->initials()); ?></span>
                <?php echo e($ins->name); ?>

            </div>
        <?php endif; ?>
        <?php if($rating > 0): ?>
            <div class="rating">
                <strong><?php echo e(number_format($rating, 1)); ?></strong>
                <span class="stars" style="--rating: <?php echo e($rating); ?>" aria-label="Rated <?php echo e($rating); ?> out of 5"></span>
                <span class="rating__count">(<?php echo e(number_format($course->reviewsCount())); ?>)</span>
            </div>
        <?php endif; ?>
    </div>
    <div class="course-card__footer">
        <?php if($free): ?>
            <div class="price"><span class="price__now price__now--free">Free</span></div>
            <a href="<?php echo e($url); ?>" class="btn btn-soft btn-sm"><i class="bi bi-play-fill"></i> Watch Now</a>
        <?php else: ?>
            <div class="price">
                <span class="price__now"><?php echo e(Storefront::money($course->price)); ?></span>
                <?php if($off): ?>
                    <span class="price__old"><?php echo e(Storefront::money($course->old_price)); ?></span>
                <?php endif; ?>
            </div>
            <button type="button" class="btn btn-soft btn-sm" data-add-to-cart='<?php echo json_encode($course->cartData(), 15, 512) ?>'>
                <i class="bi bi-bag-plus"></i> <span>Add to Cart</span>
            </button>
        <?php endif; ?>
    </div>
</article>
<?php /**PATH D:\xampp\htdocs\trainersbd\resources\views/frontend/partials/course-card.blade.php ENDPATH**/ ?>