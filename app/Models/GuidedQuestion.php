<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GuidedQuestion extends Model
{
    protected $table = 'guidedquery'; // ✅ Correct table name
    protected $primaryKey = 'gq_id';
    public $timestamps = false;

    protected $fillable = [
        'parent_id', 'question_text', 'answer_text', 'level', 'category'
    ];

    /**
     * Get child questions
     */
    public function children(): HasMany
    {
        return $this->hasMany(GuidedQuestion::class, 'parent_id', 'gq_id');
    }

    /**
     * Get parent question
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(GuidedQuestion::class, 'parent_id', 'gq_id');
    }

    /**
     * Check if this is a final question (has answer)
     */
    public function isFinal(): bool
    {
        return !empty($this->answer_text) || $this->children()->count() === 0;
    }
}