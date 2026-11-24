<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CourseOrderItem extends Model
{
    protected $guarded = ['id'];

    public function order()
    {
        return $this->belongsTo(CourseOrder::class, 'order_id');
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }
}
