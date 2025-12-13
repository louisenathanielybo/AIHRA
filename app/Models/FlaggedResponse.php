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
        // 'flaggedID', // Let DB auto-increment
        'employeeNum',
        'queryID',
        'reasonID',
        'timeStamp',
        'status'
    ];

    protected $casts = [
        'timeStamp' => 'datetime',
    ];

    // No UUID logic needed; flaggedID is auto-increment integer

    public function user()
    {
        return $this->belongsTo(User::class, 'employeeNum', 'employeeNum');
    }

    public function queryRecord()
    {
        return $this->belongsTo(Query::class, 'queryID', 'queryID');
    }
}
