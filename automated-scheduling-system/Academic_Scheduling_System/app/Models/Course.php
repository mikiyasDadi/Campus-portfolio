<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'course_code',
        'course_name',
        'department_id',
        'ects',
        'hours',
        'year',      // Add this
    'semester'
    ];

    /**
     * Relationship to the Department
     * * We specify 'department_id' as the foreign key to ensure 
     * Laravel connects this course to the correct department row.
     */
    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id');
    }
}