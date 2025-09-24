<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $table = 'users'; // explicit, just to be safe
    protected $primaryKey = 'employeeNum'; // if you still have an `id` column (default PK)

    protected $fillable = [
        'employeeNum',
        'email',
        'password',
        'firstName',
        'lastName',
        'middleName',
        'role',
        'sex',
        'age',
        'profile_picture',
        'status',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public $timestamps = false; // ⚡ set true only if your table has created_at/updated_at

    // Disable auto hashing since you’re storing plain text for now
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
}
