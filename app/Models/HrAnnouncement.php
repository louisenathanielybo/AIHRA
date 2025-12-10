<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HrAnnouncement extends Model
{
    protected $table = 'announcements';

    protected $fillable = [
        'employeeNum',
        'title',
        'description',
        'image',
        'createdAt',
        'isActive'
    ];

    public $timestamps = false; // since you're using createdAt, not Laravel's timestamps
}
