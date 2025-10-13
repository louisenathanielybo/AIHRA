<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HrInbox extends Model
{
    protected $table = 'hr_inbox';

    protected $fillable = [
    'ticket_no',
    'from_user',
    'message',
    'status',
    'priority',
    'category',
    'intent',
    'confidence',
];


    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = true;

    /**
     * Cast dates properly so Laravel recognizes them.
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}
