<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Auth;

class QuizBlock extends Block
{
	public $hasChildren = false;
	public $can_change_parent = true;

	static $displayName = 'Quiz';

	private $current_user_attempts = null;
	private $allow_navigation = null;
	private $question_attempts = null;

	public $childTypes = [
		'App\TextBlock',
		'App\QuestionBlock'
	];

	public function attempts() {
		return $this->hasMany('App\QuizAttempt', 'quiz_id');
	}

	public function current_user_attempt() {
		$attempt = $this->current_user_attempts()->first();
		return $attempt;
	}

	public function current_user_attempts() {
		if(!$this->current_user_attempts) {
			$this->current_user_attempts = $this->attempts()
	       ->where('user_id', Auth::user()->id)
	       ->orderBy('updated_at', 'DESC')->get();
		}
		return $this->current_user_attempts;
	}

	public function questionAttemptForUser($questionNode, $userid = null) {
		$attempt = false;
		$attempts = $this->questionAttemptsForUser($userid);
		if($attempts) {
			$attempt = $attempts->firstWhere('question_id', $questionNode->block->id);
		}
		return $attempt;
	}

	public function questionAttemptsForUser( $user_id = null) {
		if($this->questionAttempts) { return $this->questionAttempts; }

		if(!$user_id) { $user_id = Auth::user()->id; }
		$quizAttempt = QuizAttempt::where('quiz_id', $this->id)
			->where('user_id', $user_id)
			->orderBy('updated_at', 'desc')
			->first();
		if(!$quizAttempt) { return null; }
		$questionAttempts = [];
		$questionAttempts = QuestionAttempt::where('user_id', $user_id)
				->where('quiz_attempt_id', $quizAttempt->id)
				->orderBy('created_at', 'desc')->get();
		
		$this->questionAttempts = $questionAttempts;
		return $questionAttempts;
	}
	/**
	 * @return QuizAttempts collection
	 * 
	 * Quiz can be in these states
	 * - Start
	 * - On question
	 * - On feedback (after question)
	 * - Completed (optionally shows feedback)
	 */
	public function currentState() {
		if($this->current_user_attempt() == null) {
			return 'start';
		}
		if($this->nextQuestionNode()) {
			return 'on_question';
		}
	}

	public function current_user_can_restart() {
		if(optional($this->current_user_attempt())->complete) {
			if($this->attempts_allowed == -1 || count($this->current_user_attempts()) < $this->attempts_allowed) {
				return true;
			}
		}
		return false;
	}

	/**
	 * Get the child node before the passed node (for prev/next nav)
	 */
	public function nodeBefore($node) {
		$previous = false;
		if($node == null) { return $this->node->children->last(); }
		foreach($this->node->children as $child_node) {
			if($node->is($child_node)) {
				return $previous;
			} else {
				$previous = $child_node;
			}
		}
		return false;
	}

	/**
	 * Get the child node after the passed node (for prev/next nav)
	 */
	public function nodeAfter($node) {
		$found = false;
		if($node == null) { return false; }
		foreach($this->node->children as $child_node) {
			if($found) {
				return $child_node;
			}
			if($node->is($child_node)) {
				$found = true;
			}
		}
		return false;
	}

	/**
	 * @return Node
	 * 
	 * This returns the next node that needs to be loaded.
	 * It checks which question was last answered, and gets the next
	 * child node for this quiz.
	 */
	public function nextNode() {
		$attempt = $this->current_user_attempt();
		$last_node = Node::find($attempt->last_question);
		if(empty($last_node->block->attemptForUser())) { return $this->node->children->first(); }	
		$found_last = false;
		foreach($this->node->children as $node) {
			if( $found_last ) {
				return $node;
			}
			if( $last_node->is($node) ) {
				$found_last = true;
			}
		}
		return $last_node;
	}

	public function currentNode() {
		//Check for a node in the session first, if it exists, return
		if(session($this->id . '-' . 'currentNode')) {
			return session($this->id . '-' . 'currentNode');
		}
		if(session($this->id . '-' . 'showresults') || request($this->id . '-' . 'showresults')) {
			return null;
		}
		$node_id = request('questionid');
		if(!empty($node_id) && !$node = Node::find($node_id) ) {
			if($attempt = $this->current_user_attempt()) {
				$node = $this->nextNode();
			}
		}
		if(empty($node) || !$node) {
			$node = $this->node->children->first();
		}
		return $node;
	}

	public function getAllowNavigationAttribute( $value ) {
		if($this->allow_navigation !== null) {
			return $this->allow_navigation;
		}
		$value = false;
		if($value == true) { $value = true; }
		if(optional($this->current_user_attempt())->complete) { $value = true; }
		if(session('isediting')) { $value = true; }
		$this->allow_navigation = $value;
		return $value;
	}

	public function process( Request $request ) {
		$this->feedback_type = $request->input('feedback_type');
		$this->allow_navigation = ($request->input('allow_navigation')) ? true : false;
		$this->addSetting('display', $request->input('display'));
		$this->attempts_allowed = ($request->input('attempts_allowed_unlimited')) ? -1 : $request->input('attempts_allowed');

		if($request->input('reset_attempts')) {
			foreach($this->node->children as $child_node) {
				if(class_basename($child_node, 'QuestionBlock')) {
					if($child_node->block->attempts) {
						foreach($child_node->block->attempts as $attempt) {
							$attempt->delete();
						}
					}
				}
			}
			foreach($this->attempts as $attempt) {			
				$attempt->delete();
			}
		}
	}
}
