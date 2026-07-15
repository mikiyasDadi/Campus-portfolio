<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExamSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id', 
        'day_number', 
        'period', 
        'exam_date',
        'room_name', 
        'year', 
        'semester', 
        'section',
        'department_id',
        'inv1_id',
        'inv2_id',
        'inv1_name',
        'inv2_name'
    ];

    public function course()
    {
        return $this->belongsTo(Course::class, 'course_id');
    }

    public function inv1()
    {
        return $this->belongsTo(InstructorProfile::class, 'inv1_id', 'user_id');
    }

    public function inv2()
    {
        return $this->belongsTo(InstructorProfile::class, 'inv2_id', 'user_id');
    }
}