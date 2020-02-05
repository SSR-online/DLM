<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AnswerOption extends Model
{
    public function question() {
    	return $this->belongsTo('App\QuestionBlock', 'question_block_id');
    }
}
