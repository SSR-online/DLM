<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Auth;

class QuizAttempt extends Model
{	
	public function user() {
		return $this->belongsTo('App\User');
	}
	public function quiz() {
		return $this->belongsTo('App\QuizBlock');
	}

    public function question_attempts() {
	    return $this->hasMany('App\QuestionAttempt');
	}

    public function currentQuestion() {
	    return $this->belongsTo('App\QuestionBlock');
	}

	public function questionAttemptsByquizOrder() {
		$question_attempts = $this->question_attempts;
		$attempts = [];
		foreach($this->quiz->node->children->filter(function($item) {
			return (class_basename($item->block) == 'QuestionBlock');
		}) as $question) {
			$attempts[] = $question_attempts->where('question_id', $question->block->id)->first();
		}
		return collect($attempts);
	}

	public function getCompleteAttribute( $value ) {
		foreach($this->quiz->node->children->filter(function($item) {
			return (class_basename($item->block) == 'QuestionBlock');
		}) as $question) {
			if(!$question->block->attemptForUser(Auth::user()->id, $this)) {
				return false;
			}
		}
		return true;
	}

	public function getScoreAttribute( $value ) {
		if(!$this->complete) {
			return false;
		}
		$score = 0;
		$total = $this->quiz->node->children->filter(function($item) {
			return (class_basename($item->block) == 'QuestionBlock');
		})->count();
		foreach($this->question_attempts as $attempt) {
			if($attempt->correct) {
				$score++;
			}
		}
		return $score / $total * 10;
	}
}