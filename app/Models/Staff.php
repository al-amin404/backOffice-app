<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Staff extends Model
{
    protected $fillable = [
        'staff_id',
        'name',
        'email',
        'mobile',
        'date_of_birth',
        'designation',
        'department',
        'joining_date',
        'blood_group',
        'nid',
        'address',
        'photo',
    ];
}
