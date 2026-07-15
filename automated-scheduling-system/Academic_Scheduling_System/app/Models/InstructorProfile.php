<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InstructorProfile extends Model
{
    /**
     * Primary key is user_id to match your database schema.
     */
    protected $primaryKey = 'user_id';

    /**
     * Set to false because user_id comes from the users table.
     */
    public $incrementing = false;

    protected $keyType = 'int';

    protected $fillable = [
        'user_id', 
        'first_name', 
        'last_name', 
        'department_id', 
        'status'
    ];

    /**
     * Relationship: Link back to the parent User account.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relationship: Link to availability records.
     * Maps 'instructor_profile_id' in availabilities table to 'user_id' here.
     */
    public function availabilities()
    {
        return $this->hasMany(InstructorAvailability::class, 'instructor_profile_id', 'user_id');
    }

    /**
     * Helper: Check if this instructor is busy at a specific time.
     */
    public function isBusy($day, $slotId)
    {
        return $this->availabilities()
                    ->where('day_of_week', $day)
                    ->where('time_slot_id', $slotId)
                    ->exists();
    }

   /**
 * Relationship: Many-to-Many with Departments.
 * We explicitly define 'user_id' as the foreign key in the pivot table.
 */
public function departments() 
{
    return $this->belongsToMany(
        Department::class, 
        'department_instructor', 
        'user_id',         // The column in the pivot table for THIS model
        'department_id'    // The column in the pivot table for the Department
    );
}

    public function getRouteKeyName()
    {
        return 'user_id';
    }
}