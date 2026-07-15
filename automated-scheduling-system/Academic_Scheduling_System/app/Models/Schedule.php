<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Schedule extends Model
{
    // These fields are allowed to be saved via Schedule::create()
    protected $fillable = [
    'department_id',
    'course_code',
    'instructor_id',
    'type',
    'day',
    'period',
    'year',
    'semester',
    'section',
    'status',
];
    /**
     * Get the instructor assigned to this schedule slot.
     */
    public function instructor(): BelongsTo
    {
        return $this->belongsTo(InstructorProfile::class, 'instructor_id', 'user_id');
    }

    /**
     * Get the course associated with this schedule.
     */
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class, 'course_code', 'course_code');
    }
}