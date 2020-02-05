<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class QuestionAttempt extends Model
{
    protected $casts = [
    	'answer' => 'array'
    ];

	public function question() {
	    return $this->belongsTo('App\QuestionBlock');
	}
	
	public function quizAttempt() {
	    return $this->belongsTo('App\QuizAttempt');
	}

	public function user() {
	    return $this->belongsTo('App\User');
	}

	public function getCorrectAttribute( $value ) {
		if(!$this->answer) { return false; }
		if($this->question->question_type == 'mc') {
			if(count($this->question->correct_answers) != count($this->answer)) { return false; }
			foreach($this->question->correct_answers as $correct_answer) {
				if(!in_array($correct_answer->id, $this->answer)) {
					return false;
				}
			}

		} else if($this->question->question_type == 'order') {
			$order = $this->question->setting('answer_order');
			$answer = explode(',', $this->answer);
			
			return ($order == $answer);
		}
		return true;
	}

	public function getAnswerObjectsAttribute( $value ) {
		$answer = null;
		if($this->question->question_type == 'order') {
			foreach(explode(',', $this->answer) as $answer_key) {
				$answer[] = AnswerOption::find($answer_key);
			}
		} else if($this->question->question_type == 'mc') {
			foreach($this->answer as $answer_key) {
				$answer[] = AnswerOption::find($answer_key);
			}
		} else if($this->question->question_type == 'open') {
			$theAnswer = new \stdClass();
			$theAnswer->content = $this->answer;
			$answer[] = $theAnswer;

		}
		return $answer;
	}
}
