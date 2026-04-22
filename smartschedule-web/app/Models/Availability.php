<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Availability extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'day',
        'start_time',
        'end_time',
    ];

    protected $appends = ['day_of_week', 'is_active'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getDayOfWeekAttribute()
    {
        $map = ['Sun' => 0, 'Mon' => 1, 'Tue' => 2, 'Wed' => 3, 'Thu' => 4, 'Fri' => 5, 'Sat' => 6];
        return $map[$this->day] ?? 0;
    }

    public function getIsActiveAttribute()
    {
        return true;
    }
}
