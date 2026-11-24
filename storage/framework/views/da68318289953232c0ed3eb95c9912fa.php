<?php $__env->startSection('title', 'TrainersBD | Online Courses in Bangla'); ?>

<?php $__env->startSection('content'); ?>


<section class="hero">
    <div class="hero__bg" aria-hidden="true">
        <span class="hero__blob hero__blob--1"></span>
        <span class="hero__blob hero__blob--2"></span>
        <span class="hero__grid"></span>
    </div>
    <div class="container position-relative">
        <div class="row align-items-center g-5">
            <div class="col-lg-6">
                <span class="eyebrow"><span class="dot"></span> New batches enrolling now</span>
                <h1 class="hero__title">
                    Learn the skills that
                    <span class="hero__highlight">get you hired
                        <svg viewBox="0 0 300 18" preserveAspectRatio="none" aria-hidden="true"><path d="M2 13C60 4 140 2 298 9" /></svg>
                    </span>
                </h1>
                <p class="hero__text">Practical, project-based courses in Bangla on web development, design, marketing and freelancing, taught by trainers who do this work every day.</p>

                <div class="hero__actions">
                    <a href="#courses" class="btn btn-primary btn-lg">Explore Courses <i class="bi bi-arrow-right"></i></a>
                    <a href="#free-courses" class="hero__play">
                        <span class="hero__play-icon"><i class="bi bi-play-fill"></i></span>
                        <span><strong>Watch free classes</strong><small>No sign-up needed</small></span>
                    </a>
                </div>

                <form class="hero-search" action="#courses" data-hero-search>
                    <i class="bi bi-search"></i>
                    <input type="search" name="q" placeholder="What do you want to learn today?" aria-label="Search courses">
                    <button class="btn btn-primary" type="submit">Search</button>
                </form>
                <div class="hero__popular">
                    <span>Popular:</span>
                    <a href="#courses" data-hero-tag="web">Web Development</a>
                    <a href="#courses" data-hero-tag="graphic">Graphic Design</a>
                    <a href="#courses" data-hero-tag="marketing">Facebook Ads</a>
                    <a href="#courses" data-hero-tag="freelancing">Freelancing</a>
                </div>

                <div class="hero__trust">
                    <div class="avatar-stack">
                        <span class="avatar" style="--bg:#1a4fb5">SA</span>
                        <span class="avatar" style="--bg:#e80810">IK</span>
                        <span class="avatar" style="--bg:#F59E0B">NC</span>
                        <span class="avatar" style="--bg:#10B981">+</span>
                    </div>
                    <div>
                        <div class="rating"><strong><?php echo e($stats['rating'] ?: '5.0'); ?></strong><span class="stars" style="--rating: <?php echo e($stats['rating'] ?: 5); ?>"></span></div>
                        <small>Trusted by <b><?php echo e(number_format($stats['students'])); ?>+</b> learners across Bangladesh</small>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="hero-media">
                    <span class="hero-media__ring" aria-hidden="true"></span>
                    <span class="hero-media__dots" aria-hidden="true"></span>

                    <div id="heroCarousel" class="carousel slide carousel-fade hero-media__frame" data-bs-ride="carousel" data-bs-interval="5000">
                        <div class="carousel-inner">
                            <?php $__empty_1 = true; $__currentLoopData = $slides; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $slide): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <div class="carousel-item <?php echo e($i === 0 ? 'active' : ''); ?>">
                                    <img src="<?php echo e(asset($slide->image())); ?>" alt="<?php echo e($slide->imageFile->alt_text ?: $slide->name); ?>" <?php echo e($i === 0 ? 'fetchpriority=high' : 'loading=lazy'); ?>>
                                    <?php if($slide->name): ?>
                                        <a href="<?php echo e($slide->seo_description ?: '#courses'); ?>" class="hero-media__caption">
                                            <?php if($slide->description): ?><span><?php echo e(strip_tags($slide->description)); ?></span><?php endif; ?>
                                            <strong><?php echo e($slide->name); ?> <i class="bi bi-arrow-up-right"></i></strong>
                                        </a>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <div class="carousel-item active">
                                    <img src="<?php echo e(asset('frontend/images/hero/students-learning.jpg')); ?>" alt="Students learning together">
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="hero-media__nav">
                            <?php $__currentLoopData = $slides; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $slide): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="<?php echo e($i); ?>" class="<?php echo e($i === 0 ? 'active' : ''); ?>" aria-label="Slide <?php echo e($i + 1); ?>"></button>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </div>

                    <div class="hero-media__thumb">
                        <img src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7" data-src="<?php echo e(asset('frontend/images/hero/group-study.jpg')); ?>" alt="Students studying in a group" class="lazy" decoding="async">
                    </div>

                    <div class="float-card float-card--enrol">
                        <span class="chip-icon chip-icon--green"><i class="bi bi-patch-check-fill"></i></span>
                        <div><strong>Payment verified</strong><small>Rafi enrolled in Web Development</small></div>
                    </div>

                    <div class="float-card float-card--stats">
                        <div class="d-flex justify-content-between align-items-start">
                            <div><small>Students this month</small><strong>1,280+</strong></div>
                            <span class="trend"><i class="bi bi-arrow-up-right"></i> 24%</span>
                        </div>
                        <div class="mini-bars" aria-hidden="true">
                            <span style="--h:35%"></span><span style="--h:55%"></span><span style="--h:42%"></span><span style="--h:70%"></span><span style="--h:60%"></span><span style="--h:88%"></span><span style="--h:100%"></span>
                        </div>
                    </div>

                    <div class="float-card float-card--pay">
                        <small>Pay easily with</small>
                        <div class="pay-chips">
                            <?php $__currentLoopData = \App\Support\Storefront::paymentMethods(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pm): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <span class="pay-chip pay-chip--sm" style="--chip: <?php echo e($pm['color']); ?>"><?php echo e($pm['key'] === 'bank' ? 'Bank' : $pm['name']); ?></span>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>


<section class="stats-strip">
    <div class="container">
        <div class="stats-grid">
            <div class="stat"><i class="bi bi-people"></i><div><strong><?php echo e(number_format($stats['students'])); ?>+</strong><span>Learners enrolled</span></div></div>
            <div class="stat"><i class="bi bi-collection-play"></i><div><strong><?php echo e($stats['courses']); ?></strong><span>Expert-led courses</span></div></div>
            <div class="stat"><i class="bi bi-person-video3"></i><div><strong><?php echo e($stats['instructors']); ?></strong><span>Industry trainers</span></div></div>
            <div class="stat"><i class="bi bi-star"></i><div><strong><?php echo e($stats['rating'] ?: '5.0'); ?>/5</strong><span>Average rating</span></div></div>
        </div>
    </div>
</section>


<section class="section">
    <div class="container">
        <div class="section-head">
            <div>
                <span class="eyebrow">Categories</span>
                <h2>Explore top skill categories</h2>
                <p>Pick a field and start building skills that employers and clients are looking for.</p>
            </div>
        </div>
        <div class="category-grid">
            <?php $__currentLoopData = \App\Support\Storefront::categories()->where('courses_count', '>', 0); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <a href="<?php echo e(route('courses.category', $cat->slug)); ?>" class="category-card">
                    <span class="category-card__icon"><i class="bi <?php echo e($cat->icon ?: 'bi-grid'); ?>"></i></span>
                    <span class="category-card__name"><?php echo e($cat->name); ?></span>
                    <span class="category-card__count"><?php echo e($cat->courses_count); ?> <?php echo e(\Illuminate\Support\Str::plural('course', $cat->courses_count)); ?> <i class="bi bi-arrow-right"></i></span>
                </a>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>
</section>


<section class="section section--tint" id="courses">
    <div class="container">
        <div class="section-head">
            <div>
                <span class="eyebrow">Our Courses</span>
                <h2>Popular courses students love</h2>
                <p>Project-based courses with lifetime access and personal support.</p>
            </div>
        </div>

        <div class="course-toolbar">
            <div class="filter-tabs" role="tablist" data-filter-tabs>
                <button type="button" class="active" data-filter="all">All</button>
                <?php $__currentLoopData = \App\Support\Storefront::categories()->where('courses_count', '>', 0); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <button type="button" data-filter="<?php echo e($cat->slug); ?>"><?php echo e($cat->name); ?></button>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
            <div class="toolbar-right">
                <div class="search-pill search-pill--sm">
                    <i class="bi bi-search"></i>
                    <input type="search" placeholder="Search courses" data-course-search aria-label="Search courses">
                </div>
                <select class="form-select form-select-sm sort-select" data-course-sort aria-label="Sort courses">
                    <option value="popular">Most popular</option>
                    <option value="rating">Highest rated</option>
                    <option value="price-asc">Price: low to high</option>
                    <option value="price-desc">Price: high to low</option>
                </select>
            </div>
        </div>

        <div class="course-grid" data-course-grid>
            <?php $__currentLoopData = $courses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $course): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="course-grid__item" data-sort-popular="<?php echo e($course->students_count); ?>" data-sort-rating="<?php echo e($course->rating()); ?>" data-sort-price="<?php echo e($course->price); ?>" data-index="<?php echo e($i); ?>">
                    <?php echo $__env->make('frontend.partials.course-card', ['course' => $course], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
        <div class="empty-filter" data-empty-filter hidden>
            <i class="bi bi-search"></i>
            <h5>No courses found</h5>
            <p>Try another category or search term.</p>
            <button class="btn btn-soft" type="button" data-filter-reset>Show all courses</button>
        </div>
        <div class="text-center mt-5">
            <a href="<?php echo e(route('courses.index')); ?>" class="btn btn-outline-dark btn-lg">View all courses <i class="bi bi-arrow-right"></i></a>
        </div>
    </div>
</section>


<section class="section section--dark" id="free-courses">
    <div class="container">
        <div class="section-head section-head--light">
            <div>
                <span class="eyebrow eyebrow--dark"><i class="bi bi-youtube"></i> Free Courses</span>
                <h2>Start learning free, right now</h2>
                <p>Complete beginner courses you can watch here without any payment or sign-up.</p>
            </div>
        </div>
        <div class="row g-4">
            <?php $__empty_1 = true; $__currentLoopData = $freeCourses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $course): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <?php ($video = \App\Models\CourseLesson::youtubeId($course->preview_video)); ?>
                <div class="col-md-6 col-lg-4">
                    <article class="free-card">
                        <button type="button" class="course-thumb free-card__thumb <?php echo e($course->thumbnailUrl() ? 'course-thumb--img' : ''); ?>"
                                <?php if($video): ?> data-video="<?php echo e($video); ?>" <?php endif; ?> data-video-title="<?php echo e($course->title); ?>" aria-label="Play <?php echo e($course->title); ?>">
                            <?php if($course->thumbnailUrl()): ?>
                                <img src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7" data-src="<?php echo e($course->thumbnailUrl()); ?>" alt="<?php echo e($course->thumbnail_alt ?: $course->title); ?>" class="lazy" decoding="async">
                            <?php else: ?>
                                <i class="bi <?php echo e($course->icon()); ?> course-thumb__icon"></i>
                            <?php endif; ?>
                            <span class="course-thumb__play course-thumb__play--lg"><i class="bi bi-play-fill"></i></span>
                            <?php if($course->duration): ?><span class="free-card__duration"><i class="bi bi-clock"></i> <?php echo e($course->duration); ?></span><?php endif; ?>
                        </button>
                        <div class="free-card__body">
                            <h3><a href="<?php echo e($course->url()); ?>"><?php echo e($course->title); ?></a></h3>
                            <p><?php echo e($course->subtitle); ?></p>
                            <div class="free-card__meta">
                                <?php if($course->instructor): ?><span><i class="bi bi-person"></i> <?php echo e($course->instructor->name); ?></span><?php endif; ?>
                                <span><i class="bi bi-play-circle"></i> <?php echo e($course->lessonsCount()); ?> lessons</span>
                                <span><i class="bi bi-people"></i> <?php echo e(number_format($course->students_count)); ?></span>
                            </div>
                            <a href="<?php echo e($course->url()); ?>" class="btn btn-light w-100 mt-3">View Curriculum <i class="bi bi-arrow-right"></i></a>
                        </div>
                    </article>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <div class="col-12"><p class="text-center">Free courses are coming soon.</p></div>
            <?php endif; ?>
        </div>
    </div>
</section>


<section class="section" id="how-it-works">
    <div class="container">
        <div class="section-head section-head--center">
            <div>
                <span class="eyebrow">How to Enroll</span>
                <h2>Get enrolled in 4 simple steps</h2>
                <p>No account needed. Pay with the mobile banking you already use, and our team handles the rest.</p>
            </div>
        </div>
        <div class="steps">
            <div class="step">
                <span class="step__num">01</span>
                <span class="step__icon"><i class="bi bi-search-heart"></i></span>
                <h5>Choose your course</h5>
                <p>Browse courses, check the curriculum and watch free preview lessons.</p>
            </div>
            <div class="step">
                <span class="step__num">02</span>
                <span class="step__icon"><i class="bi bi-bag-check"></i></span>
                <h5>Add to cart &amp; checkout</h5>
                <p>Enter just your name, mobile number and email. No registration required.</p>
            </div>
            <div class="step">
                <span class="step__num">03</span>
                <span class="step__icon"><i class="bi bi-phone"></i></span>
                <h5>Pay with bKash, Nagad or bank</h5>
                <p>Send the amount and submit your sender number and transaction ID.</p>
            </div>
            <div class="step">
                <span class="step__num">04</span>
                <span class="step__icon"><i class="bi bi-patch-check"></i></span>
                <h5>Get enrolled</h5>
                <p>Our team verifies your payment and contacts you with course access.</p>
            </div>
        </div>
    </div>
</section>


<section class="section section--tint">
    <div class="container">
        <div class="row align-items-center g-5">
            <div class="col-lg-5">
                <span class="eyebrow">Why TrainersBD</span>
                <h2 class="display-title">Learning designed for real results</h2>
                <p class="lead-muted">We focus on skills that lead to jobs and income. Every course is reviewed for quality and taught by people who do the work every day.</p>
                <ul class="check-list mt-4">
                    <li><i class="bi bi-check-circle-fill"></i> Taught in clear, simple Bangla</li>
                    <li><i class="bi bi-check-circle-fill"></i> Real projects for your portfolio</li>
                    <li><i class="bi bi-check-circle-fill"></i> Personal support from our team</li>
                </ul>
                <a href="#courses" class="btn btn-primary btn-lg mt-3">Find Your Course</a>
            </div>
            <div class="col-lg-7">
                <div class="feature-grid">
                    <div class="feature"><span class="feature__icon" style="--fi:#002670"><i class="bi bi-person-video3"></i></span><h5>Expert trainers</h5><p>Learn from working developers, designers and marketers.</p></div>
                    <div class="feature"><span class="feature__icon" style="--fi:#c00008"><i class="bi bi-kanban"></i></span><h5>Project-based</h5><p>Build real work that proves your skills to employers.</p></div>
                    <div class="feature"><span class="feature__icon" style="--fi:#059669"><i class="bi bi-shield-check"></i></span><h5>Safe payment</h5><p>Every payment is verified by our team before enrollment.</p></div>
                    <div class="feature"><span class="feature__icon" style="--fi:#EA580C"><i class="bi bi-headset"></i></span><h5>Real support</h5><p>Questions answered by people, by phone or message.</p></div>
                </div>
            </div>
        </div>
    </div>
</section>


<section class="section">
    <div class="container">
        <div class="section-head section-head--center">
            <div>
                <span class="eyebrow">Student Stories</span>
                <h2>What our learners say</h2>
            </div>
        </div>
        <div class="row g-4">
            <?php $__currentLoopData = $testimonials; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="col-md-4">
                    <figure class="testimonial">
                        <i class="bi bi-quote testimonial__quote"></i>
                        <div class="stars" style="--rating: <?php echo e($t->rating); ?>"></div>
                        <blockquote><?php echo e($t->review); ?></blockquote>
                        <figcaption>
                            <span class="avatar" style="--bg: <?php echo e(['#1a4fb5', '#e80810', '#F59E0B'][$i % 3]); ?>"><?php echo e($t->initials()); ?></span>
                            <span><strong><?php echo e($t->name); ?></strong><small><?php echo e(\Illuminate\Support\Str::limit($t->course->title ?? '', 42)); ?></small></span>
                        </figcaption>
                    </figure>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>
</section>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('frontend.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\xampp\htdocs\trainersbd\resources\views/frontend/home.blade.php ENDPATH**/ ?>