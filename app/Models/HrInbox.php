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
        'created_at'
    ];

    public $timestamps = false;
}
