<?php
    use App\Support\Storefront;
    use App\Models\CourseLesson;
    $free       = $course->isFree();
    $off        = $course->discountPercent();
    $instructor = $course->instructor;
    $category   = $course->category;
    $rating     = $course->rating();
    $reviews    = $course->approvedReviews;
    $img        = $course->thumbnailUrl();
    $allLessons = $course->modules->flatMap->lessons;
    $firstVideo = $allLessons->first(fn ($l) => $l->videoId() && ($free || $l->is_preview));
    $previewId  = CourseLesson::youtubeId($course->preview_video) ?: ($firstVideo ? $firstVideo->videoId() : null);
    $previewTitle = $firstVideo->title ?? $course->title;
    $cartData   = $course->cartData();
    $includes = $free
        ? [['bi-play-btn', $allLessons->count() . ' video lessons'], ['bi-clock', ($course->duration ?: 'Self-paced') . ' of content'], ['bi-youtube', 'Watch instantly, no sign-up'], ['bi-phone', 'Watch on mobile & desktop']]
        : [['bi-play-btn', $allLessons->count() . ' lessons'], ['bi-clock', ($course->duration ?: 'Self-paced') . ' of content'], ['bi-folder2-open', 'Project files & resources'], ['bi-people', 'Private support group'], ['bi-award', 'Certificate of completion'], ['bi-infinity', 'Lifetime access']];
?>

<?php $__env->startSection('title', $course->meta_title ?: $course->title . ' | TrainersBD'); ?>
<?php $__env->startSection('meta_description', $course->meta_description ?: $course->subtitle); ?>
<?php if($course->meta_keywords): ?>
    <?php $__env->startSection('meta_keywords', $course->meta_keywords); ?>
<?php endif; ?>

<?php $__env->startSection('content'); ?>


<section class="course-hero">
    <div class="hero__glow hero__glow--1"></div>
    <div class="container position-relative">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb breadcrumb--light">
                <li class="breadcrumb-item"><a href="<?php echo e(route('index')); ?>">Home</a></li>
                <?php if($category): ?>
                    <li class="breadcrumb-item"><a href="<?php echo e(route('index')); ?>?category=<?php echo e($category->slug); ?>#courses"><?php echo e($category->name); ?></a></li>
                <?php endif; ?>
                <li class="breadcrumb-item active" aria-current="page"><?php echo e(\Illuminate\Support\Str::limit($course->title, 40)); ?></li>
            </ol>
        </nav>
        <div class="row">
            <div class="col-lg-8">
                <div class="d-flex flex-wrap gap-2 mb-3">
                    <?php if($course->badge): ?>
                        <span class="course-badge course-badge--<?php echo e(\Illuminate\Support\Str::slug($course->badge)); ?> position-static"><?php echo e($course->badge); ?></span>
                    <?php endif; ?>
                    <?php if($category): ?>
                        <span class="tag-light"><i class="bi <?php echo e($category->icon ?: 'bi-grid'); ?>"></i> <?php echo e($category->name); ?></span>
                    <?php endif; ?>
                </div>
                <h1 class="course-hero__title"><?php echo e($course->title); ?></h1>
                <p class="course-hero__subtitle"><?php echo e($course->subtitle); ?></p>
                <div class="course-hero__meta">
                    <?php if($rating > 0): ?>
                        <div class="rating rating--light">
                            <strong><?php echo e(number_format($rating, 1)); ?></strong>
                            <span class="stars" style="--rating: <?php echo e($rating); ?>"></span>
                            <a href="#reviews">(<?php echo e(number_format($reviews->count())); ?> <?php echo e(\Illuminate\Support\Str::plural('review', $reviews->count())); ?>)</a>
                        </div>
                    <?php endif; ?>
                    <span><i class="bi bi-people"></i> <?php echo e(number_format($course->students_count)); ?> students</span>
                    <span><i class="bi bi-translate"></i> <?php echo e($course->language); ?></span>
                    <span><i class="bi bi-arrow-repeat"></i> Updated <?php echo e($course->updated_at->format('F Y')); ?></span>
                </div>
                <?php if($instructor): ?>
                    <div class="course-hero__author">
                        <?php if($instructor->photoUrl()): ?>
                            <img class="avatar avatar--img" src="<?php echo e($instructor->photoUrl()); ?>" alt="<?php echo e($instructor->name); ?>">
                        <?php else: ?>
                            <span class="avatar"><?php echo e($instructor->initials()); ?></span>
                        <?php endif; ?>
                        <span>Created by <a href="#instructor"><?php echo e($instructor->name); ?></a><small><?php echo e($instructor->title); ?></small></span>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<section class="course-body">
    <div class="container">
        <div class="row g-5">
            
            <div class="col-lg-8 order-2 order-lg-1">

                <div class="course-tabs" data-scrollspy-nav>
                    <a href="#overview" class="active">Overview</a>
                    <a href="#curriculum">Curriculum</a>
                    <?php if($instructor): ?><a href="#instructor">Instructor</a><?php endif; ?>
                    <a href="#reviews">Reviews</a>
                </div>

                <div class="highlights">
                    <div><i class="bi bi-bar-chart"></i><span>Level</span><strong><?php echo e($course->level ?: 'All Levels'); ?></strong></div>
                    <div><i class="bi bi-play-circle"></i><span>Lessons</span><strong><?php echo e($allLessons->count()); ?></strong></div>
                    <div><i class="bi bi-clock"></i><span>Duration</span><strong><?php echo e($course->duration ?: 'Self-paced'); ?></strong></div>
                    <div><i class="bi bi-translate"></i><span>Language</span><strong><?php echo e($course->language); ?></strong></div>
                </div>

                <section id="overview" class="content-block">
                    <?php if(!empty($course->learn_points)): ?>
                        <div class="learn-box">
                            <h2 class="block-title">What you'll learn</h2>
                            <ul class="learn-list">
                                <?php $__currentLoopData = $course->learn_points; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <li><i class="bi bi-check2"></i> <?php echo e($item); ?></li>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <h2 class="block-title mt-5">About this course</h2>
                    <div class="prose" data-readmore>
                        <?php echo clean_html($course->description); ?>

                        <?php if(!empty($course->requirements)): ?>
                            <h4>Requirements</h4>
                            <ul>
                                <?php $__currentLoopData = $course->requirements; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $req): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <li><?php echo e($req); ?></li>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </ul>
                        <?php endif; ?>
                    </div>
                    <button class="btn btn-link px-0 readmore-btn" type="button" data-readmore-toggle>Show more <i class="bi bi-chevron-down"></i></button>
                </section>

                <section id="curriculum" class="content-block">
                    <div class="d-flex flex-wrap justify-content-between align-items-end gap-2 mb-3">
                        <div>
                            <h2 class="block-title mb-1">Course curriculum</h2>
                            <p class="text-muted mb-0"><?php echo e($course->modules->count()); ?> modules &middot; <?php echo e($allLessons->count()); ?> lessons <?php if($course->duration): ?>&middot; <?php echo e($course->duration); ?> total <?php endif; ?></p>
                        </div>
                        <?php if($course->modules->count() > 1): ?>
                            <button class="btn btn-link px-0" type="button" data-expand-all>Expand all</button>
                        <?php endif; ?>
                    </div>
                    <?php if($course->modules->isEmpty()): ?>
                        <p class="text-muted">The curriculum will be published soon.</p>
                    <?php endif; ?>
                    <div class="accordion curriculum" id="curriculumAcc">
                        <?php $__currentLoopData = $course->modules; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $mi => $module): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="accordion-item">
                                <h3 class="accordion-header">
                                    <button class="accordion-button <?php echo e($mi === 0 ? '' : 'collapsed'); ?>" type="button" data-bs-toggle="collapse" data-bs-target="#mod<?php echo e($module->id); ?>" aria-expanded="<?php echo e($mi === 0 ? 'true' : 'false'); ?>">
                                        <span class="curriculum__index"><?php echo e(str_pad($mi + 1, 2, '0', STR_PAD_LEFT)); ?></span>
                                        <span class="curriculum__title"><?php echo e($module->title); ?></span>
                                        <span class="curriculum__count"><?php echo e($module->lessons->count()); ?> lessons</span>
                                    </button>
                                </h3>
                                <div id="mod<?php echo e($module->id); ?>" class="accordion-collapse collapse <?php echo e($mi === 0 ? 'show' : ''); ?>">
                                    <ul class="lesson-list">
                                        <?php $__currentLoopData = $module->lessons; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lesson): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <?php ($vid = ($free || $lesson->is_preview) ? $lesson->videoId() : null); ?>
                                            <li class="<?php echo e($vid ? 'is-playable' : 'is-locked'); ?>">
                                                <?php if($vid): ?>
                                                    <button type="button" class="lesson" data-video="<?php echo e($vid); ?>" data-video-title="<?php echo e($lesson->title); ?>">
                                                        <i class="bi bi-play-circle-fill"></i>
                                                        <span class="lesson__title"><?php echo e($lesson->title); ?></span>
                                                        <?php if(!$free): ?><span class="lesson__preview">Preview</span><?php endif; ?>
                                                        <span class="lesson__time"><?php echo e($lesson->duration); ?></span>
                                                    </button>
                                                <?php else: ?>
                                                    <div class="lesson">
                                                        <i class="bi bi-lock"></i>
                                                        <span class="lesson__title"><?php echo e($lesson->title); ?></span>
                                                        <span class="lesson__time"><?php echo e($lesson->duration); ?></span>
                                                    </div>
                                                <?php endif; ?>
                                            </li>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </ul>
                                </div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </section>

                <?php if($instructor): ?>
                    <section id="instructor" class="content-block">
                        <h2 class="block-title">Your instructor</h2>
                        <div class="instructor-card">
                            <?php if($instructor->photoUrl()): ?>
                                <img class="avatar avatar--xl avatar--img" src="<?php echo e($instructor->photoUrl()); ?>" alt="<?php echo e($instructor->name); ?>">
                            <?php else: ?>
                                <span class="avatar avatar--xl"><?php echo e($instructor->initials()); ?></span>
                            <?php endif; ?>
                            <div>
                                <h4><?php echo e($instructor->name); ?></h4>
                                <p class="instructor-card__role"><?php echo e($instructor->title); ?></p>
                                <div class="instructor-card__stats">
                                    <span><i class="bi bi-collection-play-fill"></i> <?php echo e($instructor->courses()->where('status', 'active')->count()); ?> courses</span>
                                    <span><i class="bi bi-people-fill"></i> <?php echo e(number_format($instructor->courses()->where('status', 'active')->sum('students_count'))); ?> students</span>
                                </div>
                                <p class="mb-0"><?php echo e($instructor->bio); ?></p>
                            </div>
                        </div>
                    </section>
                <?php endif; ?>

                <section id="reviews" class="content-block">
                    <h2 class="block-title">Student reviews</h2>
                    <?php if($reviews->count()): ?>
                        <div class="review-summary">
                            <div class="review-summary__score">
                                <strong><?php echo e(number_format($rating, 1)); ?></strong>
                                <span class="stars" style="--rating: <?php echo e($rating); ?>"></span>
                                <small>Course rating &middot; <?php echo e(number_format($reviews->count())); ?> reviews</small>
                            </div>
                            <div class="review-summary__bars">
                                <?php $__currentLoopData = $breakdown; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $star => $pct): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <div class="bar-row">
                                        <span><?php echo e($star); ?> <i class="bi bi-star-fill"></i></span>
                                        <div class="bar"><span style="width: <?php echo e($pct); ?>%"></span></div>
                                        <span class="bar-row__pct"><?php echo e($pct); ?>%</span>
                                    </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                        </div>

                        <div class="review-list" data-review-list>
                            <?php $__currentLoopData = $reviews; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $review): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <article class="review" <?php if($i >= 5): ?> hidden data-review-more <?php endif; ?>>
                                    <span class="avatar" style="--bg: <?php echo e(['#1a4fb5', '#e80810', '#10B981'][$i % 3]); ?>"><?php echo e($review->initials()); ?></span>
                                    <div>
                                        <div class="review__head">
                                            <strong><?php echo e($review->name); ?></strong>
                                            <span class="stars stars--sm" style="--rating: <?php echo e($review->rating); ?>"></span>
                                            <small><?php echo e($review->created_at->diffForHumans()); ?></small>
                                        </div>
                                        <p><?php echo e($review->review); ?></p>
                                    </div>
                                </article>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                        <?php if($reviews->count() > 5): ?>
                            <button type="button" class="btn btn-outline-dark mt-3" data-review-toggle>Show all <?php echo e($reviews->count()); ?> reviews</button>
                        <?php endif; ?>
                    <?php else: ?>
                        <p class="text-muted">No reviews yet. Be the first to share your experience.</p>
                    <?php endif; ?>

                    <div class="review-form">
                        <h5>Write a review</h5>
                        <p class="text-muted small">Took this course? Share your experience. Reviews are published after approval.</p>
                        <form data-review-form action="<?php echo e(route('course.review', $course->slug)); ?>" novalidate>
                            <div class="star-input mb-3" role="radiogroup" aria-label="Your rating">
                                <?php for($s = 5; $s >= 1; $s--): ?>
                                    <input type="radio" name="rating" id="star<?php echo e($s); ?>" value="<?php echo e($s); ?>" required>
                                    <label for="star<?php echo e($s); ?>" title="<?php echo e($s); ?> stars"><i class="bi bi-star-fill"></i></label>
                                <?php endfor; ?>
                            </div>
                            <div class="row g-3">
                                <div class="col-md-6"><input type="text" class="form-control" name="name" placeholder="Your name" required maxlength="100"></div>
                                <div class="col-md-6"><input type="email" class="form-control" name="email" placeholder="Email address" required maxlength="150"></div>
                                <div class="col-12"><textarea class="form-control" name="review" rows="3" placeholder="What did you like about the course?" required minlength="5" maxlength="2000"></textarea></div>
                            </div>
                            <button class="btn btn-primary mt-3" type="submit">Submit Review</button>
                        </form>
                    </div>
                </section>
            </div>

            
            <aside class="col-lg-4 order-1 order-lg-2">
                <div class="buy-card" id="buyCard">
                    <button type="button" class="course-thumb buy-card__thumb <?php echo e($img ? 'course-thumb--img' : ''); ?>"
                            <?php if($previewId): ?> data-video="<?php echo e($previewId); ?>" data-video-title="<?php echo e($previewTitle); ?>" <?php endif; ?> aria-label="Preview this course">
                        <?php if($img): ?>
                            <img src="<?php echo e($img); ?>" alt="<?php echo e($course->thumbnail_alt ?: $course->title); ?>" fetchpriority="high" decoding="async">
                        <?php else: ?>
                            <i class="bi <?php echo e($course->icon()); ?> course-thumb__icon"></i>
                        <?php endif; ?>
                        <?php if($previewId): ?>
                            <span class="course-thumb__play course-thumb__play--lg"><i class="bi bi-play-fill"></i></span>
                            <span class="buy-card__preview-label"><?php echo e($free ? 'Start watching' : 'Preview this course'); ?></span>
                        <?php endif; ?>
                    </button>
                    <div class="buy-card__body">
                        <?php if($free): ?>
                            <div class="price price--xl"><span class="price__now price__now--free">Free</span></div>
                            <p class="buy-card__note"><i class="bi bi-youtube"></i> Watch every lesson here, no payment or sign-up needed.</p>
                            <?php if($previewId): ?>
                                <button type="button" class="btn btn-primary btn-lg w-100" data-video="<?php echo e($previewId); ?>" data-video-title="<?php echo e($previewTitle); ?>">
                                    <i class="bi bi-play-fill"></i> Start Watching
                                </button>
                            <?php endif; ?>
                            <a href="#curriculum" class="btn btn-outline-dark btn-lg w-100 mt-2">View All Lessons</a>
                        <?php else: ?>
                            <div class="price price--xl">
                                <span class="price__now"><?php echo e(Storefront::money($course->price)); ?></span>
                                <?php if($off): ?>
                                    <span class="price__old"><?php echo e(Storefront::money($course->old_price)); ?></span>
                                    <span class="price__off"><?php echo e($off); ?>% off</span>
                                <?php endif; ?>
                            </div>
                            <?php if($off): ?>
                                <p class="buy-card__urgency"><i class="bi bi-alarm"></i> Discount price for the current batch</p>
                            <?php else: ?>
                                <div class="mb-3"></div>
                            <?php endif; ?>
                            <button type="button" class="btn btn-primary btn-lg w-100" data-add-to-cart='<?php echo json_encode($cartData, 15, 512) ?>'>
                                <i class="bi bi-bag-plus"></i> <span>Add to Cart</span>
                            </button>
                            <button type="button" class="btn btn-accent btn-lg w-100 mt-2" data-buy-now='<?php echo json_encode($cartData, 15, 512) ?>'>
                                Enroll Now <i class="bi bi-arrow-right"></i>
                            </button>
                            <p class="buy-card__pay">Pay with
                                <?php $__currentLoopData = Storefront::paymentMethods(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pm): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <span class="pay-chip pay-chip--sm" style="--chip: <?php echo e($pm['color']); ?>"><?php echo e($pm['key'] === 'bank' ? 'Bank' : $pm['name']); ?></span>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </p>
                        <?php endif; ?>

                        <h6 class="buy-card__heading">This course includes</h6>
                        <ul class="includes-list">
                            <?php $__currentLoopData = $includes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as [$icon, $label]): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <li><i class="bi <?php echo e($icon); ?>"></i> <?php echo e($label); ?></li>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </ul>

                        <?php if(Storefront::site()['phone']): ?>
                            <div class="buy-card__help">
                                <i class="bi bi-headset"></i>
                                <div><strong>Need help enrolling?</strong><small>Call <?php echo e(Storefront::site()['phone']); ?></small></div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </aside>
        </div>
    </div>
</section>


<?php if($related->count()): ?>
<section class="section section--tint">
    <div class="container">
        <div class="section-head">
            <div>
                <span class="eyebrow">Keep learning</span>
                <h2>Students also viewed</h2>
            </div>
            <a href="<?php echo e(route('index')); ?>#courses" class="btn btn-outline-dark">View all courses</a>
        </div>
        <div class="course-grid">
            <?php $__currentLoopData = $related; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php echo $__env->make('frontend.partials.course-card', ['course' => $item], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>
</section>
<?php endif; ?>


<div class="mobile-buybar d-lg-none">
    <div>
        <?php if($free): ?>
            <span class="price__now price__now--free">Free</span>
        <?php else: ?>
            <span class="price__now"><?php echo e(Storefront::money($course->price)); ?></span>
            <?php if($off): ?><span class="price__old"><?php echo e(Storefront::money($course->old_price)); ?></span><?php endif; ?>
        <?php endif; ?>
    </div>
    <?php if($free): ?>
        <?php if($previewId): ?><button type="button" class="btn btn-primary" data-video="<?php echo e($previewId); ?>" data-video-title="<?php echo e($previewTitle); ?>"><i class="bi bi-play-fill"></i> Watch Free</button><?php endif; ?>
    <?php else: ?>
        <button type="button" class="btn btn-accent" data-buy-now='<?php echo json_encode($cartData, 15, 512) ?>'>Enroll Now</button>
    <?php endif; ?>
</div>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('frontend.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\xampp\htdocs\trainersbd\resources\views/frontend/course.blade.php ENDPATH**/ ?>