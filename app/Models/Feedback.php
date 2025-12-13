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
<<<<<<< HEAD
=======
        'subject',
>>>>>>> 21f0fed8913e61a3dc40934bf89c506deb9e72b9
        'suggestion',
        'timeStamp',
    ];
}
