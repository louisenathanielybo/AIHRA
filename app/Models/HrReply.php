<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class HrReply extends Model
{
    protected $table = 'hr_replies';

    protected $primaryKey = 'replyID';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'replyID',
        'ticket_no',
        'hr_message',
        'replied_by',
        'replied_at'
    ];

    public $timestamps = false;

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->replyID)) {
                $model->replyID = (string) Str::uuid();
            }
        });
    }
}
