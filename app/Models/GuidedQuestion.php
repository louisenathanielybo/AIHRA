<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GuidedQuestion extends Model
{
    use HasFactory;

    protected $table = 'guided_questions';
    protected $primaryKey = 'gq_id';

    protected $fillable = [
        'parent_id',
        'question_text',
        'answer_text',
        'LEVEL',
        'linked_intent',
        'display_order',
        'response_type',
        'custom_response',
        'category',
        'status'
    ];

    protected $casts = [
        'custom_response' => 'array',
        'display_order' => 'integer'
    ];

    // Relationships
    public function children()
    {
        return $this->hasMany(GuidedQuestion::class, 'parent_id')->orderBy('display_order');
    }

    public function parent()
    {
        return $this->belongsTo(GuidedQuestion::class, 'parent_id');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    public function scopeByIntent($query, $intentName)
    {
        return $query->where('linked_intent', $intentName);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('display_order')->orderBy('id');
    }
}