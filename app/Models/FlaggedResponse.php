<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class FlaggedResponse extends Model
{
    protected $table = 'flaggedresponse';
    protected $primaryKey = 'flaggedID';
    public $timestamps = false;
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'flaggedID',
        'employeeNum',
        'queryID',
        'reasonID',
        'timeStamp',
        'status'
    ];

    protected $casts = [
        'timeStamp' => 'datetime',
    ];

    protected static function booted()
    {
        parent::booted();

        static::creating(function (FlaggedResponse $model) {
            if (empty($model->flaggedID)) {
                $model->flaggedID = (string) Str::uuid();
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'employeeNum', 'employeeNum');
    }

    public function queryRecord()
    {
        return $this->belongsTo(Query::class, 'queryID', 'queryID');
    }
}
