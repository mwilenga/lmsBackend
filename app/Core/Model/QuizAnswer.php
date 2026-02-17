<?php

namespace App\Core\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class QuizAnswer extends BaseModel
{
    use HasFactory;

    protected $table = 'quiz_answer';
    protected $fillable = [];
    protected $guarded = [];

}