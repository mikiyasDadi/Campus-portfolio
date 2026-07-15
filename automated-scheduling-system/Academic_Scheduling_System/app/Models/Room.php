<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    protected $fillable = [
        'name',
        'type',
        'department_id',
        'order_weight'
    ];

    public function department()
    {
        return $this->belongsTo(Department::class);
    }
}
