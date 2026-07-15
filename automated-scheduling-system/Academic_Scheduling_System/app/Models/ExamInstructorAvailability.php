<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExamInstructorAvailability extends Model
{
    protected $table = 'exam_instructor_availabilities';

    // Added department_id to fillable
    protected $fillable = ['instructor_id', 'day_number', 'period', 'is_available', 'department_id'];

    public function instructor()
    {
        return $this->belongsTo(User::class, 'instructor_id');
    }

    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id');
    }
}