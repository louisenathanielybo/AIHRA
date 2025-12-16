<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class FlaggedResponse extends Model
{
    protected $table = 'flaggedresponse';
    protected $primaryKey = 'flaggedID';
    public $timestamps = false;
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'employeeNum',
        'queryID',
        'reasonID',
        'reason',
        'timeStamp',
        'status'
    ];

    protected $casts = [
        'timeStamp' => 'datetime',
    ];

    protected static function booted()
    {
        parent::booted();
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
