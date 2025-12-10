<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Query extends Model
{
    // Disable automatic timestamps
    public $timestamps = false;

    // Optional: define which columns are mass assignable
    protected $fillable = [
        'queryID',
        'employeeNum',
        'question',
        'response',
        'confidenceScore',
        'queryType',
        'questionTime',
        'responseTime',
        'isEscalated',
        'handledBy',
    ];

    /**
     * Boot model events to ensure employeeNum is always valid.
     */
    protected static function booted()
    {
        parent::booted();

        static::creating(function (Query $model) {
            // If employeeNum missing or '0', try to set a valid fallback
            $emp = $model->employeeNum ?? null;
            if (empty($emp) || (string)$emp === '0') {
                if (Auth::check()) {
                    $model->employeeNum = Auth::user()->employeeNum;
                } else {
                    // fallback id - must exist in users table (create below)
                    $model->employeeNum = 'SYSTEM';
                }
            }

            // Ensure queryID exists (optional)
            if (empty($model->queryID)) {
                $model->queryID = (string) \Illuminate\Support\Str::uuid();
            }

            // Set timestamps if missing (optional)
            if (empty($model->questionTime)) {
                $model->questionTime = now();
            }
            if (empty($model->responseTime)) {
                $model->responseTime = now();
            }
        });
    }
}
