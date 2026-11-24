<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CourseLesson extends Model
{
    protected $guarded = ['id'];
    protected $casts = ['is_preview' => 'boolean'];

    public function module()
    {
        return $this->belongsTo(CourseModule::class, 'module_id');
    }

    /** Extract an 11-char YouTube ID from a URL or a bare ID. */
    public static function youtubeId(?string $value): ?string
    {
        if (!$value) {
            return null;
        }
        $value = trim($value);
        if (preg_match('~^[A-Za-z0-9_-]{11}$~', $value)) {
            return $value;
        }
        if (preg_match('~(?:youtu\.be/|youtube(?:-nocookie)?\.com/(?:watch\?(?:.*&)?v=|embed/|shorts/|live/|v/))([A-Za-z0-9_-]{11})~', $value, $m)) {
            return $m[1];
        }
        return null;
    }

    public function videoId(): ?string
    {
        return self::youtubeId($this->video);
    }
}
