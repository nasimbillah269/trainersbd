<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CourseCategory extends Model
{
    protected $guarded = ['id'];
    protected $casts = ['status' => 'boolean'];

    public function courses()
    {
        return $this->hasMany(Course::class, 'category_id');
    }

    /** Course categories are flat; the admin menu builder expects these. */
    public function getSubctgsAttribute()
    {
        return collect();
    }

    public function image()
    {
        return null;
    }

    public function scopeActive($q)
    {
        return $q->where('status', true)->orderBy('sort')->orderBy('name');
    }
}
