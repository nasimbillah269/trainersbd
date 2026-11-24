<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Course extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'learn_points' => 'array',
        'requirements' => 'array',
        'featured'     => 'boolean',
        'price'        => 'float',
        'old_price'    => 'float',
    ];

    /* ---------- Relations ---------- */

    public function category()
    {
        return $this->belongsTo(CourseCategory::class, 'category_id');
    }

    public function instructor()
    {
        return $this->belongsTo(Instructor::class);
    }

    public function modules()
    {
        return $this->hasMany(CourseModule::class)->orderBy('sort')->orderBy('id');
    }

    public function lessons()
    {
        return $this->hasMany(CourseLesson::class);
    }

    public function reviews()
    {
        return $this->hasMany(CourseReview::class);
    }

    public function approvedReviews()
    {
        return $this->hasMany(CourseReview::class)->where('status', 'approved');
    }

    public function orderItems()
    {
        return $this->hasMany(CourseOrderItem::class);
    }

    /* ---------- Scopes ---------- */

    public function scopeActive($q)
    {
        return $q->where('status', 'active');
    }

    /** Eager-load everything a course card needs. */
    public function scopeForCards($q)
    {
        return $q->with(['category:id,name,slug,icon', 'instructor:id,name,title,photo'])
            ->withCount(['lessons', 'approvedReviews as reviews_count'])
            ->withAvg('approvedReviews as rating_avg', 'rating');
    }

    /* ---------- Helpers ---------- */

    public function isFree(): bool
    {
        return $this->type === 'free';
    }

    public function url(): string
    {
        return route('course.show', $this->slug);
    }

    public function thumbnailUrl(): ?string
    {
        if (!$this->thumbnail) {
            return null;
        }
        return Str::startsWith($this->thumbnail, ['http://', 'https://']) ? $this->thumbnail : asset($this->thumbnail);
    }

    public function discountPercent(): int
    {
        if ($this->isFree() || $this->old_price <= $this->price || $this->old_price <= 0) {
            return 0;
        }
        return (int) round((1 - $this->price / $this->old_price) * 100);
    }

    public function rating(): float
    {
        $avg = $this->rating_avg ?? $this->approvedReviews()->avg('rating');
        return $avg ? round((float) $avg, 1) : 0.0;
    }

    public function reviewsCount(): int
    {
        return (int) ($this->reviews_count ?? $this->approvedReviews()->count());
    }

    public function lessonsCount(): int
    {
        return (int) ($this->lessons_count ?? $this->lessons()->count());
    }

    public function icon(): string
    {
        return $this->category->icon ?? 'bi-journal-code';
    }

    /** Data the browser cart stores for this course. */
    public function cartData(): array
    {
        return [
            'id'         => $this->id,
            'slug'       => $this->slug,
            'title'      => $this->title,
            'price'      => $this->price,
            'old_price'  => $this->old_price,
            'instructor' => $this->instructor->name ?? '',
            'icon'       => $this->icon(),
            'image'      => $this->thumbnailUrl(),
            'lessons'    => $this->lessonsCount(),
            'duration'   => $this->duration,
            'url'        => $this->url(),
        ];
    }

    public static function uniqueSlug(string $title, ?int $ignoreId = null): string
    {
        $base = Str::slug($title) ?: 'course';
        $slug = $base;
        $i = 2;
        while (static::where('slug', $slug)->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))->exists()) {
            $slug = $base . '-' . $i++;
        }
        return $slug;
    }
}
