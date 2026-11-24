@extends('frontend.layouts.app')

@php
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
@endphp

@section('title', $course->meta_title ?: $course->title . ' | TrainersBD')
@section('meta_description', $course->meta_description ?: $course->subtitle)
@if($course->meta_keywords)
    @section('meta_keywords', $course->meta_keywords)
@endif

@section('content')

{{-- ============ COURSE HEADER ============ --}}
<section class="course-hero">
    <div class="hero__glow hero__glow--1"></div>
    <div class="container position-relative">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb breadcrumb--light">
                <li class="breadcrumb-item"><a href="{{ route('index') }}">Home</a></li>
                @if($category)
                    <li class="breadcrumb-item"><a href="{{ route('index') }}?category={{ $category->slug }}#courses">{{ $category->name }}</a></li>
                @endif
                <li class="breadcrumb-item active" aria-current="page">{{ \Illuminate\Support\Str::limit($course->title, 40) }}</li>
            </ol>
        </nav>
        <div class="row">
            <div class="col-lg-8">
                <div class="d-flex flex-wrap gap-2 mb-3">
                    @if($course->badge)
                        <span class="course-badge course-badge--{{ \Illuminate\Support\Str::slug($course->badge) }} position-static">{{ $course->badge }}</span>
                    @endif
                    @if($category)
                        <span class="tag-light"><i class="bi {{ $category->icon ?: 'bi-grid' }}"></i> {{ $category->name }}</span>
                    @endif
                </div>
                <h1 class="course-hero__title">{{ $course->title }}</h1>
                <p class="course-hero__subtitle">{{ $course->subtitle }}</p>
                <div class="course-hero__meta">
                    @if($rating > 0)
                        <div class="rating rating--light">
                            <strong>{{ number_format($rating, 1) }}</strong>
                            <span class="stars" style="--rating: {{ $rating }}"></span>
                            <a href="#reviews">({{ number_format($reviews->count()) }} {{ \Illuminate\Support\Str::plural('review', $reviews->count()) }})</a>
                        </div>
                    @endif
                    <span><i class="bi bi-people"></i> {{ number_format($course->students_count) }} students</span>
                    <span><i class="bi bi-translate"></i> {{ $course->language }}</span>
                    <span><i class="bi bi-arrow-repeat"></i> Updated {{ $course->updated_at->format('F Y') }}</span>
                </div>
                @if($instructor)
                    <div class="course-hero__author">
                        @if($instructor->photoUrl())
                            <img class="avatar avatar--img" src="{{ $instructor->photoUrl() }}" alt="{{ $instructor->name }}">
                        @else
                            <span class="avatar">{{ $instructor->initials() }}</span>
                        @endif
                        <span>Created by <a href="#instructor">{{ $instructor->name }}</a><small>{{ $instructor->title }}</small></span>
                    </div>
                @endif
            </div>
        </div>
    </div>
</section>

<section class="course-body">
    <div class="container">
        <div class="row g-5">
            {{-- ============ MAIN ============ --}}
            <div class="col-lg-8 order-2 order-lg-1">

                <div class="course-tabs" data-scrollspy-nav>
                    <a href="#overview" class="active">Overview</a>
                    <a href="#curriculum">Curriculum</a>
                    @if($instructor)<a href="#instructor">Instructor</a>@endif
                    <a href="#reviews">Reviews</a>
                </div>

                <div class="highlights">
                    <div><i class="bi bi-bar-chart"></i><span>Level</span><strong>{{ $course->level ?: 'All Levels' }}</strong></div>
                    <div><i class="bi bi-play-circle"></i><span>Lessons</span><strong>{{ $allLessons->count() }}</strong></div>
                    <div><i class="bi bi-clock"></i><span>Duration</span><strong>{{ $course->duration ?: 'Self-paced' }}</strong></div>
                    <div><i class="bi bi-translate"></i><span>Language</span><strong>{{ $course->language }}</strong></div>
                </div>

                <section id="overview" class="content-block">
                    @if(!empty($course->learn_points))
                        <div class="learn-box">
                            <h2 class="block-title">What you'll learn</h2>
                            <ul class="learn-list">
                                @foreach($course->learn_points as $item)
                                    <li><i class="bi bi-check2"></i> {{ $item }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <h2 class="block-title mt-5">About this course</h2>
                    <div class="prose" data-readmore>
                        {!! clean_html($course->description) !!}
                        @if(!empty($course->requirements))
                            <h4>Requirements</h4>
                            <ul>
                                @foreach($course->requirements as $req)
                                    <li>{{ $req }}</li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                    <button class="btn btn-link px-0 readmore-btn" type="button" data-readmore-toggle>Show more <i class="bi bi-chevron-down"></i></button>
                </section>

                <section id="curriculum" class="content-block">
                    <div class="d-flex flex-wrap justify-content-between align-items-end gap-2 mb-3">
                        <div>
                            <h2 class="block-title mb-1">Course curriculum</h2>
                            <p class="text-muted mb-0">{{ $course->modules->count() }} modules &middot; {{ $allLessons->count() }} lessons @if($course->duration)&middot; {{ $course->duration }} total @endif</p>
                        </div>
                        @if($course->modules->count() > 1)
                            <button class="btn btn-link px-0" type="button" data-expand-all>Expand all</button>
                        @endif
                    </div>
                    @if($course->modules->isEmpty())
                        <p class="text-muted">The curriculum will be published soon.</p>
                    @endif
                    <div class="accordion curriculum" id="curriculumAcc">
                        @foreach($course->modules as $mi => $module)
                            <div class="accordion-item">
                                <h3 class="accordion-header">
                                    <button class="accordion-button {{ $mi === 0 ? '' : 'collapsed' }}" type="button" data-bs-toggle="collapse" data-bs-target="#mod{{ $module->id }}" aria-expanded="{{ $mi === 0 ? 'true' : 'false' }}">
                                        <span class="curriculum__index">{{ str_pad($mi + 1, 2, '0', STR_PAD_LEFT) }}</span>
                                        <span class="curriculum__title">{{ $module->title }}</span>
                                        <span class="curriculum__count">{{ $module->lessons->count() }} lessons</span>
                                    </button>
                                </h3>
                                <div id="mod{{ $module->id }}" class="accordion-collapse collapse {{ $mi === 0 ? 'show' : '' }}">
                                    <ul class="lesson-list">
                                        @foreach($module->lessons as $lesson)
                                            @php($vid = ($free || $lesson->is_preview) ? $lesson->videoId() : null)
                                            <li class="{{ $vid ? 'is-playable' : 'is-locked' }}">
                                                @if($vid)
                                                    <button type="button" class="lesson" data-video="{{ $vid }}" data-video-title="{{ $lesson->title }}">
                                                        <i class="bi bi-play-circle-fill"></i>
                                                        <span class="lesson__title">{{ $lesson->title }}</span>
                                                        @if(!$free)<span class="lesson__preview">Preview</span>@endif
                                                        <span class="lesson__time">{{ $lesson->duration }}</span>
                                                    </button>
                                                @else
                                                    <div class="lesson">
                                                        <i class="bi bi-lock"></i>
                                                        <span class="lesson__title">{{ $lesson->title }}</span>
                                                        <span class="lesson__time">{{ $lesson->duration }}</span>
                                                    </div>
                                                @endif
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </section>

                @if($instructor)
                    <section id="instructor" class="content-block">
                        <h2 class="block-title">Your instructor</h2>
                        <div class="instructor-card">
                            @if($instructor->photoUrl())
                                <img class="avatar avatar--xl avatar--img" src="{{ $instructor->photoUrl() }}" alt="{{ $instructor->name }}">
                            @else
                                <span class="avatar avatar--xl">{{ $instructor->initials() }}</span>
                            @endif
                            <div>
                                <h4>{{ $instructor->name }}</h4>
                                <p class="instructor-card__role">{{ $instructor->title }}</p>
                                <div class="instructor-card__stats">
                                    <span><i class="bi bi-collection-play-fill"></i> {{ $instructor->courses()->where('status', 'active')->count() }} courses</span>
                                    <span><i class="bi bi-people-fill"></i> {{ number_format($instructor->courses()->where('status', 'active')->sum('students_count')) }} students</span>
                                </div>
                                <p class="mb-0">{{ $instructor->bio }}</p>
                            </div>
                        </div>
                    </section>
                @endif

                <section id="reviews" class="content-block">
                    <h2 class="block-title">Student reviews</h2>
                    @if($reviews->count())
                        <div class="review-summary">
                            <div class="review-summary__score">
                                <strong>{{ number_format($rating, 1) }}</strong>
                                <span class="stars" style="--rating: {{ $rating }}"></span>
                                <small>Course rating &middot; {{ number_format($reviews->count()) }} reviews</small>
                            </div>
                            <div class="review-summary__bars">
                                @foreach($breakdown as $star => $pct)
                                    <div class="bar-row">
                                        <span>{{ $star }} <i class="bi bi-star-fill"></i></span>
                                        <div class="bar"><span style="width: {{ $pct }}%"></span></div>
                                        <span class="bar-row__pct">{{ $pct }}%</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="review-list" data-review-list>
                            @foreach($reviews as $i => $review)
                                <article class="review" @if($i >= 5) hidden data-review-more @endif>
                                    <span class="avatar" style="--bg: {{ ['#1a4fb5', '#e80810', '#10B981'][$i % 3] }}">{{ $review->initials() }}</span>
                                    <div>
                                        <div class="review__head">
                                            <strong>{{ $review->name }}</strong>
                                            <span class="stars stars--sm" style="--rating: {{ $review->rating }}"></span>
                                            <small>{{ $review->created_at->diffForHumans() }}</small>
                                        </div>
                                        <p>{{ $review->review }}</p>
                                    </div>
                                </article>
                            @endforeach
                        </div>
                        @if($reviews->count() > 5)
                            <button type="button" class="btn btn-outline-dark mt-3" data-review-toggle>Show all {{ $reviews->count() }} reviews</button>
                        @endif
                    @else
                        <p class="text-muted">No reviews yet. Be the first to share your experience.</p>
                    @endif

                    <div class="review-form">
                        <h5>Write a review</h5>
                        <p class="text-muted small">Took this course? Share your experience. Reviews are published after approval.</p>
                        <form data-review-form action="{{ route('course.review', $course->slug) }}" novalidate>
                            <div class="star-input mb-3" role="radiogroup" aria-label="Your rating">
                                @for($s = 5; $s >= 1; $s--)
                                    <input type="radio" name="rating" id="star{{ $s }}" value="{{ $s }}" required>
                                    <label for="star{{ $s }}" title="{{ $s }} stars"><i class="bi bi-star-fill"></i></label>
                                @endfor
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

            {{-- ============ PURCHASE CARD ============ --}}
            <aside class="col-lg-4 order-1 order-lg-2">
                <div class="buy-card" id="buyCard">
                    <button type="button" class="course-thumb buy-card__thumb {{ $img ? 'course-thumb--img' : '' }}"
                            @if($previewId) data-video="{{ $previewId }}" data-video-title="{{ $previewTitle }}" @endif aria-label="Preview this course">
                        @if($img)
                            <img src="{{ $img }}" alt="{{ $course->thumbnail_alt ?: $course->title }}" fetchpriority="high" decoding="async">
                        @else
                            <i class="bi {{ $course->icon() }} course-thumb__icon"></i>
                        @endif
                        @if($previewId)
                            <span class="course-thumb__play course-thumb__play--lg"><i class="bi bi-play-fill"></i></span>
                            <span class="buy-card__preview-label">{{ $free ? 'Start watching' : 'Preview this course' }}</span>
                        @endif
                    </button>
                    <div class="buy-card__body">
                        @if($free)
                            <div class="price price--xl"><span class="price__now price__now--free">Free</span></div>
                            <p class="buy-card__note"><i class="bi bi-youtube"></i> Watch every lesson here, no payment or sign-up needed.</p>
                            @if($previewId)
                                <button type="button" class="btn btn-primary btn-lg w-100" data-video="{{ $previewId }}" data-video-title="{{ $previewTitle }}">
                                    <i class="bi bi-play-fill"></i> Start Watching
                                </button>
                            @endif
                            <a href="#curriculum" class="btn btn-outline-dark btn-lg w-100 mt-2">View All Lessons</a>
                        @else
                            <div class="price price--xl">
                                <span class="price__now">{{ Storefront::money($course->price) }}</span>
                                @if($off)
                                    <span class="price__old">{{ Storefront::money($course->old_price) }}</span>
                                    <span class="price__off">{{ $off }}% off</span>
                                @endif
                            </div>
                            @if($off)
                                <p class="buy-card__urgency"><i class="bi bi-alarm"></i> Discount price for the current batch</p>
                            @else
                                <div class="mb-3"></div>
                            @endif
                            <button type="button" class="btn btn-primary btn-lg w-100" data-add-to-cart='@json($cartData)'>
                                <i class="bi bi-bag-plus"></i> <span>Add to Cart</span>
                            </button>
                            <button type="button" class="btn btn-accent btn-lg w-100 mt-2" data-buy-now='@json($cartData)'>
                                Enroll Now <i class="bi bi-arrow-right"></i>
                            </button>
                            <p class="buy-card__pay">Pay with
                                @foreach(Storefront::paymentMethods() as $pm)
                                    <span class="pay-chip pay-chip--sm" style="--chip: {{ $pm['color'] }}">{{ $pm['key'] === 'bank' ? 'Bank' : $pm['name'] }}</span>
                                @endforeach
                            </p>
                        @endif

                        <h6 class="buy-card__heading">This course includes</h6>
                        <ul class="includes-list">
                            @foreach($includes as [$icon, $label])
                                <li><i class="bi {{ $icon }}"></i> {{ $label }}</li>
                            @endforeach
                        </ul>

                        @if(Storefront::site()['phone'])
                            <div class="buy-card__help">
                                <i class="bi bi-headset"></i>
                                <div><strong>Need help enrolling?</strong><small>Call {{ Storefront::site()['phone'] }}</small></div>
                            </div>
                        @endif
                    </div>
                </div>
            </aside>
        </div>
    </div>
</section>

{{-- ============ RELATED ============ --}}
@if($related->count())
<section class="section section--tint">
    <div class="container">
        <div class="section-head">
            <div>
                <span class="eyebrow">Keep learning</span>
                <h2>Students also viewed</h2>
            </div>
            <a href="{{ route('index') }}#courses" class="btn btn-outline-dark">View all courses</a>
        </div>
        <div class="course-grid">
            @foreach($related as $item)
                @include('frontend.partials.course-card', ['course' => $item])
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- Mobile sticky buy bar --}}
<div class="mobile-buybar d-lg-none">
    <div>
        @if($free)
            <span class="price__now price__now--free">Free</span>
        @else
            <span class="price__now">{{ Storefront::money($course->price) }}</span>
            @if($off)<span class="price__old">{{ Storefront::money($course->old_price) }}</span>@endif
        @endif
    </div>
    @if($free)
        @if($previewId)<button type="button" class="btn btn-primary" data-video="{{ $previewId }}" data-video-title="{{ $previewTitle }}"><i class="bi bi-play-fill"></i> Watch Free</button>@endif
    @else
        <button type="button" class="btn btn-accent" data-buy-now='@json($cartData)'>Enroll Now</button>
    @endif
</div>

@endsection
