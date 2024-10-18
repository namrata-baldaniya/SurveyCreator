<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserSurvey extends Model
{
    use HasFactory;
    protected $table = 'user_survey';
    protected $fillable = [
        'name',
        'age',
        'gender',
    ];

    // Relationships
    public function answers()
    {
        return $this->hasMany(Answer::class);
    }
}
