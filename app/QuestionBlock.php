<?php

namespace App;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Auth;

use Illuminate\Support\Facades\Log;

class QuestionBlock extends Block
{
	use QuestionTraits\Poll;

	protected $casts = [
		'settings' => 'array'
	];

	public $allowed_types = [
		'mc' => 'Multiple Choice',
		'order' => 'Sorteren',
		'open' => 'Open vraag',
		'poll' => 'Poll'
	];

	private $attempt_for_user = false;

	static $displayName = 'Vraag';

	public function __construct(array $attributes = []) {
		parent::__construct($attributes);
	}

	public function answers() {
		if($answerIds = $this->setting('answer_order')) {
			$answerIdsString = implode(',', $answerIds);
			return $this->hasMany('App\AnswerOption')->orderByRaw('FIELD(id, ' . $answerIdsString .')');
		} else {
			return $this->hasMany('App\AnswerOption');
		}
	}

	public function getAnswersForDisplayAttribute($value) {
		if($this->setting('randomize') == 'true') {
			return $this->hasMany('App\AnswerOption')->get()->shuffle();
		} 
		if($answerIds = $this->setting('answer_order')) {
			$answerIdsString = implode(',', $answerIds);
			return $this->hasMany('App\AnswerOption')->orderByRaw('FIELD(id, ' . $answerIdsString .')')->get();
		} else {
			return $this->hasMany('App\AnswerOption')->get();
		}	
	}

	public function getCorrectAnswersAttribute( $value ) {
		return $this->answers()->where('is_correct', '1')->get();
	}

	public function attempts() {
		return $this->hasMany('App\QuestionAttempt', 'question_id');
	}

	public function attemptForUser( $user_id = null, $quizAttempt = null ) {
		// if(array_key_exists($this->attempt_for_user !== false) { return $this->attempt_for_user; }
		if(!$user_id) { $user_id = Auth::user()->id; }
		if(class_basename(optional($this->node->parent)->block) == 'QuizBlock') {
			if(!$quizAttempt) {
				$quizAttempt = QuizAttempt::where('quiz_id', $this->node->parent->block->id)
					->where('user_id', $user_id)
					->orderBy('updated_at', 'desc')
					->first();
			}
			if(!$quizAttempt) {
				// $this->attempt_for_user = null;
				return null;
			}
			$attempt = $this->attempts()
				->where('user_id', $user_id)
				->where('quiz_attempt_id', $quizAttempt->id)
				->orderBy('created_at', 'desc')
				->first();
			return $attempt;
			// return $this->attempt_for_user;
		}
		return $this->attempts()->where('user_id', $user_id)->orderBy('created_at', 'desc')->first();
		// return $this->attempt_for_user;
	}

	public function answerForUser( $user_id = null, $quizAttempt = null ) {
		if(empty($user_id)) { $user_id = Auth::user()->id; }
		$attempt = $this->attemptForUser($user_id, $quizAttempt);
		if( !$attempt ) { return false; }
		return $attempt->answer;
	}

	public function isAnswerForUser($answer, $user_id = null) {
		if(empty($user_id)) { $user_id = Auth::user()->id; }
		$user_answer = $this->answerForUser($user_id);
		if($user_answer && in_array($answer->id, $user_answer)) {
			return true;
		}
		return false;
	}

	public function isCorrectAnwerForUser($answer, $user_id = null) {
		if(empty($user_id)) { $user_id = Auth::user()->id; }
		if($this->isAnswerForUser($answer, $user_id) && $answer->is_correct) {
			return true;
		}
		return false;
	}

	public function hasCorrectAnswer() {
		$correct = $this->correctAnswers;
		return (count($correct)>=1) ? true : false;
	}

	/**
	 * @return string input_type [checkbox|radio]
	 * 
	 * Returns the correct input type for mc questions;
	 */
	public function input_type() {
		if($this->setting('mc_type') == 'single') {
			return 'radio';
		} else if($this->setting('mc_type') == 'multiple') {
			return 'checkbox';
		}

		//Automatic fallback
		if($this->question_type == 'mc') {
			$has_correct_answer = false;
			foreach($this->answers as $answer) {
				if($answer->is_correct) {
					if($has_correct_answer) {
						return 'checkbox';
					} else {
						$has_correct_answer = true;
					}
				}
			}
			if(!$has_correct_answer) {
				return 'checkbox';
			} else {
				return 'radio';
			}
		}
		return null;
	}

	/**
	 * @return string
	 * 
	 * Return feedback for this question, based on answer
	 */
	public function feedback() {
		if(!$this->attemptForUser()) {
			return null;
		}

		if($this->attemptForUser()->correct) {
			return $this->feedback_correct;
		} else {
			return $this->feedback_incorrect;
		}
	}

    public function answerTotals() {
    	$totals = [];
    	if($this->question_type == 'order') {
    		$correct_answers = $this->answers->implode('content', ', ');
    		$correct = new \stdClass();
    		$correct->content = 'Correct (' . $correct_answers . ')';
    		$incorrect = new \stdClass();
    		$incorrect->content = 'Incorrect';
    		$totals['correct']['count'] = 0;
    		$totals['correct']['answer'] = $correct;
    		$totals['incorrect']['count'] = 0;
    		$totals['incorrect']['answer'] = $incorrect;
    	} else {
    		foreach($this->answers as $answer) {
	    		$totals[$answer->id]['count'] = 0;
	    		$totals[$answer->id]['answer'] = $answer;
	    	}
	    }

	    $users = $this->attempts->map(function($attempt, $key) {
	    	return $attempt->user;
	    })->unique();

	    foreach($users as $user) {
	    	$attempt = $this->attemptForUser($user->id);
	    	if($attempt == null) { continue; } // Skip empty attempts (for instance: Poll q in quiz where some participants haven't answered)
	    	$answers = $attempt->answer;
    		if($this->question_type == 'order') {
    			if($attempt->correct) {
    				$totals['correct']['count'] += 1;
    			} else {
    				$totals['incorrect']['count'] += 1;
    			}
    		} else if($this->question_type != 'open') {
	    		foreach($answers as $answer_key) {
	    			$totals[$answer_key]['count']++;
	    		}
	    	} 
    	}
    	foreach($totals as $key=>$value) {
    		$totals[$key]['percentage'] = sprintf('%.1f', $value['count'] / (count($users) / 100));
    	}
    	return $totals;
    }

    private function add_answer_option( $content = '', $is_correct = 0) {
    	$answer_option = new AnswerOption();
        $answer_option->content = $content;
        $answer_option->is_correct = $is_correct;
        $answer_option->question()->associate($this);
        $answer_option->save();
    }

    private function set_answers($input) {
    	foreach($input['answer'] as $key=>$answer_content) {
            $answer = $this->answers->find($key);
            $answer->content = !empty($answer_content) ? $answer_content : '';
            $answer->is_correct = (array_key_exists( 'answer-correct', $input ) && array_key_exists( $key, $input['answer-correct'] ) );
            $answer->save();
        }
    }

    /**
     * Find an array key that starts with remove_answer_option_
     * The delete buttons have the answeroption id appended to this key
     * since submit buttons can only have their name sent.
     * @param  request $request the request objec
     * @return int|bool          the id of the AnswerOption, or false
     */
    private function should_remove_anwer_option($request) {
    	$keys = array_keys($request->all());
    	$keysconcat = implode(' ', $keys);
    	if(strpos($keysconcat, 'remove_answer_option_') !== false) {
    		foreach($this->answers as $answer) {
    			if(strpos($keysconcat, 'remove_answer_option_' . $answer->id)) {
    				return $answer->id;
    			}
    		}
    	}
    	return false;
    }

    public function process( request $request ) {
		$input = $request->all();
		$redirect = null;
		
        $this->question = $request->input('question');
        $this->feedback_correct = $request->input('feedback_correct');
        $this->feedback_incorrect = $request->input('feedback_incorrect');
        
        if(array_key_exists('add_answer_option', $input)) {
            $this->add_answer_option();
            $redirect = redirect('node/edit/' . $this->node->id);
        } else if(array_key_exists('set_type', $input)) {
        	$this->set_type($input);
        	$redirect = redirect('node/edit/' . $this->node->id);
        } else if($remove_id = $this->should_remove_anwer_option($request)) {
        	$redirect = redirect('/answeroption/'.$remove_id.'/delete');
        }
        
        if( array_key_exists('answer', $input ) ) { 
         	$this->set_answers($input);   
        }

        if( array_key_exists('mc_type', $input ) ) { 
        	$this->addSetting('mc_type', $request->input('mc_type'));
        }

        if( array_key_exists('randomize', $input ) ) { 
        	$this->addSetting('randomize', 'true');
        } else {
        	$this->addSetting('randomize', 'false');
        }

        if( array_key_exists('show_feedback', $input ) ) { 
        	$this->addSetting('show_feedback', true);
        } else {
        	$this->addSetting('show_feedback', false);
        }

        if( array_key_exists('can_restart', $input ) ) { 
        	$this->addSetting('can_restart', true);
        } else {
        	$this->addSetting('can_restart', false);
        }

        if( $answerOrder = $request->get('answer-order') ) {
			$this->addSetting('answer_order', explode(',', $answerOrder));	
        }
		
		$this->addSetting('timestamp', str_replace(',', '.', $request->get('timestamp')));
		$unskippable = ($request->input('unskippable')) ? 1 : 0;
		$this->addSetting('unskippable', $unskippable );

        return $redirect;
    }

    public function duplicate() {
    	$replicant = parent::duplicate();
    	$replicant->save();
    	foreach($this->answers as $answer) {
    		$answer_replicant = $answer->replicate();
    		$answer_replicant->question()->associate($replicant);
    		$answer_replicant->save();
    	}
    	return $replicant;
    }

    private function set_type($input) {
    	if(in_array($input['set_type'], $this->allowed_types)) {
    		$this->question_type = array_search($input['set_type'], $this->allowed_types);
    	}
    	if($this->question_type == 'mc' 
    	|| $this->question_type == 'poll'
    	|| $this->question_type == 'order') {
    		for($i = 0; $i < 2; $i++) {
        		$this->add_answer_option();
	        }
    	}
    }

    public function current_user_can_restart() {
		if($this->attemptForUser()) {
			if($this->setting('can_restart') == true) {
				return true;
			}
		}
		return false;
	}

	public function serializeChildren() {
		$this->answers = $this->answers()->get();
	}

    public function hydrateFromImport($object) {
    	$this->title = $object->title;
    	$this->slug = $object->slug;
		$this->content = $object->content;
        $this->question_type = $object->question_type;
        $this->question = $object->question;
        $this->feedback_correct = $object->feedback_correct;
        $this->feedback_incorrect = $object->feedback_incorrect;
        $this->save();

        if(property_exists($object, 'answers')) {
	        foreach($object->answers as $answer) {
	        	$this->add_answer_option($answer->content, $answer->is_correct);
	        }
	    }
    	return parent::hydrateFromImport($object);
    }
}
