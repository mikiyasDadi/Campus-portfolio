<?php

namespace App\Models;

// These "use" statements are what were missing
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Models\Department;
use App\Models\Faculty;
use Illuminate\Notifications\Notifiable;

/**
 * @property int $id
 * @property string $username
 * @property string $first_name
 * @property string $last_name
 * @property string $email
 * @property int $role_id
 * @property int $department_id
 * @property-read \App\Models\Department|null $department
 * @property-read \App\Models\InstructorProfile|null $profile
 */
class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
    'username',
    'first_name', // Added
    'last_name',  // Added
    'email',
    'password',
    'role_id',
    'department_id',
    'faculty_id',
    'year',
    'section',
    'status',
];

    /**
     * The attributes that should be hidden for serialization.
     */
    /**
     * Alias user_id to id for routing compatibility
     */
    public function getUserIdAttribute()
    {
        return $this->id;
    }

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName()
    {
        return 'id';
    }

    /**
     * Get the attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    /**
     * Define the relationship to the Faculty
     */
    public function faculty()
{
    return $this->belongsTo(Faculty::class, 'faculty_id');
}
/**
 * Get the department the user belongs to.
 */
/**
 * Define the relationship to the Department
 */
public function department()
{
    // Ensure this matches the column in your users table
    return $this->belongsTo(Department::class, 'department_id');
}
// Add this method inside the User class
// Inside the User class
/**
 * Link to the instructor profile
 */
public function profile()
{
    // Ensure 'user_id' is the foreign key in your instructor_profiles table
    return $this->hasOne(InstructorProfile::class, 'user_id');
}
public function departmentHeaded()
{
    return $this->hasOne(Department::class, 'user_id');
}
// app/Models/User.php

public function departments()
{
    // Second argument: pivot table name
    // Third argument: foreign key of the current model (User)
    // Fourth argument: foreign key of the target model (Department)
    return $this->belongsToMany(\App\Models\Department::class, 'department_instructor', 'user_id', 'department_id')
                ->withTimestamps();
}

public function examAvailabilities()
    {
        return $this->hasMany(ExamInstructorAvailability::class, 'instructor_id');
    }

    public function getFullNameAttribute()
    {
        return "{$this->first_name} {$this->last_name}";
    }
}