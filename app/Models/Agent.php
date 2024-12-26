<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Agent extends Model
{
    protected $fillable = [
        'name',
        'email',
        'mobile',
        'date_of_birth',
        'nid',
        'address',
        'photo',
    ];


    public function passports(): HasMany
    {
        return $this->hasMany(Passport::class);
    }
}
