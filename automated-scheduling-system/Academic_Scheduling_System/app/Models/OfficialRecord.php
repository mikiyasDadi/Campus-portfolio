<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OfficialRecord extends Model
{
    protected $fillable = [
    'id_number',
    'email',
    'first_name',
    'last_name',
    'role_id',
    'department_id',
    'year',
    'section'
];
}