@php
    use App\Support\Storefront;
    $ins  = $course->instructor;
    $cat  = $course->category;
    $off  = $course->discountPercent();
    $free = $course->isFree();
    $url  = $course->url();
    $img  = $course->thumbnailUrl();
    $rating = $course->rating();
@endphp
<article class="course-card" data-course-card data-category="{{ $cat->slug ?? '' }}" data-type="{{ $course->type }}" data-title="{{ \Illuminate\Support\Str::lower($course->title . ' ' . ($cat->name ?? '')) }}">
    <a href="{{ $url }}" class="course-thumb {{ $img ? 'course-thumb--img' : '' }}" aria-label="{{ $course->title }}">
        @if($img)
            <img src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7" data-src="{{ $img }}" alt="{{ $course->thumbnail_alt ?: $course->title }}" class="lazy" decoding="async">
        @else
            <i class="bi {{ $course->icon() }} course-thumb__icon"></i>
        @endif
        @if($cat)<span class="course-thumb__cat">{{ $cat->name }}</span>@endif
        @if($course->badge)
            <span class="course-badge course-badge--{{ \Illuminate\Support\Str::slug($course->badge) }}">{{ $course->badge }}</span>
        @endif
        @if($free)
            <span class="course-thumb__play"><i class="bi bi-play-fill"></i></span>
        @endif
    </a>
    <div class="course-card__body">
        <div class="course-meta">
            <span><i class="bi bi-play-circle"></i> {{ $course->lessonsCount() }} lessons</span>
            @if($course->duration)<span><i class="bi bi-clock"></i> {{ $course->duration }}</span>@endif
        </div>
        <h3 class="course-card__title"><a href="{{ $url }}">{{ $course->title }}</a></h3>
        @if($ins)
            <div class="course-card__instructor">
                <span class="avatar avatar--sm">{{ $ins->initials() }}</span>
                {{ $ins->name }}
            </div>
        @endif
        @if($rating > 0)
            <div class="rating">
                <strong>{{ number_format($rating, 1) }}</strong>
                <span class="stars" style="--rating: {{ $rating }}" aria-label="Rated {{ $rating }} out of 5"></span>
                <span class="rating__count">({{ number_format($course->reviewsCount()) }})</span>
            </div>
        @endif
    </div>
    <div class="course-card__footer">
        @if($free)
            <div class="price"><span class="price__now price__now--free">Free</span></div>
            <a href="{{ $url }}" class="btn btn-soft btn-sm"><i class="bi bi-play-fill"></i> Watch Now</a>
        @else
            <div class="price">
                <span class="price__now">{{ Storefront::money($course->price) }}</span>
                @if($off)
                    <span class="price__old">{{ Storefront::money($course->old_price) }}</span>
                @endif
            </div>
            <button type="button" class="btn btn-soft btn-sm" data-add-to-cart='@json($course->cartData())'>
                <i class="bi bi-bag-plus"></i> <span>Add to Cart</span>
            </button>
        @endif
    </div>
</article>
