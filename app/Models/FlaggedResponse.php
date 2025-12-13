<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class FlaggedResponse extends Model
{
    protected $table = 'flaggedresponse';
    protected $primaryKey = 'flaggedID';
    public $timestamps = false;
<<<<<<< HEAD
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'flaggedID',
=======
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        // 'flaggedID', // Let DB auto-increment
>>>>>>> 21f0fed8913e61a3dc40934bf89c506deb9e72b9
        'employeeNum',
        'queryID',
        'reasonID',
        'timeStamp',
        'status'
    ];

    protected $casts = [
        'timeStamp' => 'datetime',
    ];

<<<<<<< HEAD
    protected static function booted()
    {
        parent::booted();

        static::creating(function (FlaggedResponse $model) {
            if (empty($model->flaggedID)) {
                $model->flaggedID = (string) Str::uuid();
            }
        });
    }
=======
    // No UUID logic needed; flaggedID is auto-increment integer
>>>>>>> 21f0fed8913e61a3dc40934bf89c506deb9e72b9

    public function user()
    {
        return $this->belongsTo(User::class, 'employeeNum', 'employeeNum');
    }

    public function queryRecord()
    {
        return $this->belongsTo(Query::class, 'queryID', 'queryID');
    }
}
