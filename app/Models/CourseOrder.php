<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CourseOrder extends Model
{
    protected $guarded = ['id'];
    protected $casts = ['verified_at' => 'datetime'];

    const STATUSES = ['pending' => 'Pending', 'confirmed' => 'Confirmed', 'hold' => 'On Hold', 'cancelled' => 'Cancelled'];
    const PAYMENT_STATUSES = ['unverified' => 'Unverified', 'verified' => 'Verified', 'invalid' => 'Invalid'];

    public function items()
    {
        return $this->hasMany(CourseOrderItem::class, 'order_id');
    }

    public function verifier()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function methodName(): string
    {
        return ['bkash' => 'bKash', 'nagad' => 'Nagad', 'bank' => 'Bank Transfer'][$this->payment_method] ?? ucfirst($this->payment_method);
    }
}
