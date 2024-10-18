<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Answer extends Model
{
    use HasFactory;

    protected $table = 'answers';

    protected $fillable = [
        'survey_id',
        'question_id',
        'question_option_id',
        'user_id',
        'answer_text',
    ];

    /**
     * Relationship with the Question model.
     * An answer belongs to a question.
     */
    public function question()
    {
        return $this->belongsTo(Question::class);
    }

    /**
     * Relationship with the QuestionOption model.
     * An answer may be associated with a question option.
     */
    public function questionOption()
    {
        return $this->belongsTo(QuestionOption::class);
    }
}
