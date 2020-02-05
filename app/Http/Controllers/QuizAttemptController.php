<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Node;
use App\QuizAttempt;
use App\QuestionAttempt;
use App\QuestionBlock;
use App\QuizBlock;
use Auth;
use App\Events\QuizCompletedEvent;

class QuizAttemptController extends Controller
{
	function postAttempt( Request $request, Node $node ) {
		if( $request->input('start_attempt')) {
			return $this->createQuizAttempt($node);
		} else {
			return $this->processAttempt($request, $node);
		}
	}

	private function createQuizAttempt($node) {
		$quiz_attempt = new QuizAttempt;
		$quiz_attempt->quiz_id = $node->block->id;
		$quiz_attempt->user_id = Auth::user()->id;
		$quiz_attempt->last_question = $node->children()->first()->id;
		$quiz_attempt->save();
		return redirect($node->path());
	}

	/**
	 * Processes the attempt submission, 
	 * adding the question attempt to the quiz attempt, saving the answer.
	 * 
	 * @param  Request $request The page request object
	 * @param  Node $node       The quiz node
	 * @return Response         A redirect to the quiz node
	 */
	private function processAttempt($request, $node) {
		if(empty($request->answer)) { return back(); }
		$quiz_completed_before_attempt = $this->is_complete($node, Auth::user()->id);
		$questionId = $request->get('questionid');

		//save quiz attempt
		if(!$quiz_attempt = $node->block->current_user_attempt()) {
			$quiz_attempt = new QuizAttempt;
			$quiz_attempt->quiz_id = $node->block->id;
			$quiz_attempt->user_id = Auth::user()->id;
		}
		$quiz_attempt->last_question = $questionId;
		$quiz_attempt->save(); //Save now, so there is an ID for the question attempt

		//save question attempt (wip: This code is in questionattemptcontroller too)		
		$question = Node::find($questionId)->block;
		if(!$attempt = $question->attemptForUser()) {
			$attempt = new QuestionAttempt;
			$attempt->user_id = Auth::user()->id;
			$attempt->quizAttempt()->associate($quiz_attempt);
		}
		$attempt->answer = $request->answer;
		$question->attempts()->save($attempt);
		$attempt->save();
		$quiz_attempt->save();
		
		$quiz_attempt->complete = $this->is_complete($node, Auth::user()->id);
		$quiz_attempt->save();


		if($node->block->feedback_type == 'direct') {
			// Keep the next display on this question, since we want
			// to show feedback
			$request->session()->flash($node->block->id . '-' . 'currentNode', $question->node);
		}

		if($quiz_attempt->complete && !$quiz_completed_before_attempt) {
			$this->handleCompletion($node, $quiz_attempt);
			$request->session()->flash('currentNode', null); // Reset current node if we're visiting the feedback page
			return redirect($node->path())->with($node->block->id . '-' . 'showresults', 'true');
		}
		return redirect($node->path());
	}


	// TODO: MAke this work
	// Requires getting results for ALL quizzes, only sending results
	// when all quizzes are complete, and if this is the case, calculating
	// an average and sending that.
	// The module should be responsible for this, quizzes don't need to know about each other.
	private function handleCompletion($node, $quizAttempt) {
		//send completion event
		event(new QuizCompletedEvent($node, $quizAttempt));
	}

	private function is_complete($node, $user_id = 1) {
		foreach($node->children->filter(function($item) {
			return (class_basename($item->block) == 'QuestionBlock');
		}) as $question) {
			if(!$question->block->attemptForUser($user_id)) {
				return false;
			}
		}
		return true;
	}
}