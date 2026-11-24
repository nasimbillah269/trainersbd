<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Instructor extends Model
{
    protected $guarded = ['id'];
    protected $casts = ['status' => 'boolean'];

    public function courses()
    {
        return $this->hasMany(Course::class);
    }

    public function initials(): string
    {
        return collect(preg_split('/\s+/', trim($this->name)))->filter()->take(2)
            ->map(fn ($p) => mb_strtoupper(mb_substr($p, 0, 1)))->join('');
    }

    public function photoUrl(): ?string
    {
        return $this->photo ? asset($this->photo) : null;
    }
}
