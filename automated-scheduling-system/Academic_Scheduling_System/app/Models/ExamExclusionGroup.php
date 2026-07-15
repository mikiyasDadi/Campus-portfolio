<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExamExclusionGroup extends Model
{
    protected $fillable = ['department_id', 'set_name'];

    public function courses()
    {
        // Using a pivot table to link multiple courses to one set
        return $this->belongsToMany(Course::class, 'exam_exclusion_group_courses', 'group_id', 'course_id');
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }
}