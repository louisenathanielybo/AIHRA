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
        'response_deadline',
        'resolution_deadline',
        'responded_at',
        'resolved_at',
        'is_expired',
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
        'response_deadline' => 'datetime',
        'resolution_deadline' => 'datetime',
        'responded_at' => 'datetime',
        'resolved_at' => 'datetime',
        'is_expired' => 'boolean',
    ];
}
