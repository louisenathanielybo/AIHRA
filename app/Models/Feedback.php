<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Feedback extends Model
{
    protected $table = 'feedback';
    protected $primaryKey = 'feedbackID';
    public $timestamps = false; // since your table uses "timeStamp" not created_at/updated_at

    protected $fillable = [
        'feedbackID',
        'employeeNum',
        'queryID',
        'rating',
        'suggestion',
        'timeStamp',
    ];
}
