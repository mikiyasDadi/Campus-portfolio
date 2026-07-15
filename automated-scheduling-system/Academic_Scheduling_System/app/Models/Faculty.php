<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Faculty extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'code'];

    // Define relationship: A faculty has many users (students/staff)
    public function users()
    {
        return $this->hasMany(User::class);
    }

    // Define relationship: A faculty has many departments
    public function departments()
    {
        return $this->hasMany(Department::class);
    }
}