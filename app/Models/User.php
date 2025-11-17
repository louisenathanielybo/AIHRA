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
        'name',
        'role',
        'sex',
        'age',
        'dob',
        'profile_picture',
        'status',
        'about',
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

    // Accessor for name attribute - returns stored name or constructs from firstName/lastName
    public function getNameAttribute($value)
    {
        // If name is stored in database, return it
        if (!empty($value)) {
            return $value;
        }
        
        // Otherwise construct from firstName, middleName, lastName
        $parts = array_filter([
            $this->attributes['firstName'] ?? null,
            $this->attributes['middleName'] ?? null,
            $this->attributes['lastName'] ?? null
        ]);
        
        return !empty($parts) ? implode(' ', $parts) : 'N/A';
    }
}