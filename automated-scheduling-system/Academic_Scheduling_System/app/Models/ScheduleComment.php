<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ScheduleComment extends Model
{
    protected $fillable = [
        'department_id',
        'user_id',
        'year',
        'semester',
        'section',
        'schedule_type',
        'comment',
        'is_read'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }
}
