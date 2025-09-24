<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssignedItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'item_name',
        'assigned_to',
        'quantity',
        'date_assigned',
    ];
}
