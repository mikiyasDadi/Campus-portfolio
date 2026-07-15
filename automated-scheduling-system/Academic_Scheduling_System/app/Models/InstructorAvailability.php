<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InstructorAvailability extends Model
{
    use HasFactory;

    protected $fillable = [
        'instructor_profile_id',
        'day_of_week',
        'time_slot_id',
        'type',
        'department_id'
    ];

    /**
     * Relationship back to the Instructor Profile.
     * Maps instructor_profile_id to the user_id (PK) of the profile.
     */
    public function instructor()
    {
        return $this->belongsTo(InstructorProfile::class, 'instructor_profile_id', 'user_id');
    }
}