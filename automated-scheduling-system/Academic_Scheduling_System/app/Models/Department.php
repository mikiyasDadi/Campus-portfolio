<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'user_id', // This is the foreign key for the Dept Head
        'faculty_id',
        'class_duration', 'lab_duration', 'total_periods',
        'instructor_count', 'course_count'
    ];

    /**
     * Relationship: The Faculty this department belongs to
     */
    public function faculty()
    {
        return $this->belongsTo(Faculty::class);
    }

    /**
     * Relationship: The Department Head (linked via user_id)
     */
    public function head()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relationship: Instructors/Staff belonging to this department
     * We explicitly tell Laravel to match 'department_id' in the users table 
     * with the 'id' of this department.
     */
    public function users()
    {
        return $this->hasMany(User::class, 'department_id', 'id');
    }

    /**
     * Relationship: Courses belonging to this department
     * We explicitly tell Laravel to match 'department_id' in the courses table 
     * with the 'id' of this department.
     */
    public function courses()
    {
        return $this->hasMany(Course::class, 'department_id', 'id');
    }
}