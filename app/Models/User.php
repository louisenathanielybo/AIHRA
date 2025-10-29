<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $table = 'users';
    protected $primaryKey = 'employeeNum';
    
    // 🆕 ADD THESE LINES:
    public $incrementing = false; // Since employeeNum might not be auto-incrementing
    protected $keyType = 'string'; // If employeeNum is string

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

    public $timestamps = false;

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    // 🆕 ADD THESE METHODS FOR AUTHENTICATION:
    public function getAuthIdentifierName()
    {
        return 'employeeNum';
    }

    public function getAuthIdentifier()
    {
        return $this->employeeNum;
    }

    public function getAuthPassword()
    {
        return $this->password;
    }
}