<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GuidedQuestion extends Model
{
    use HasFactory;

    protected $table = 'guidedquery';
    protected $primaryKey = 'gq_id';
    public $timestamps = false;

    protected $fillable = [
        'knowledgeID',
        'categoryID',
        'parent_id',
        'question_text',
        'answer_text',
        'LEVEL'
    ];

    protected $casts = [
        'LEVEL' => 'integer'
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