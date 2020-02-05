<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\QuestionAttempt;
use App\QuestionBlock;
use Auth;
use View;

class QuestionAttemptController extends Controller
{
	function postAttempt( Request $request, int $questionId ) {
		$question = QuestionBlock::find($questionId);

		if(!$attempt = $question->attemptForUser()) {
			$attempt = new QuestionAttempt;	
			$attempt->user_id = Auth::user()->id;
		}
		$attempt->answer = $request->get('answer');
		$question->attempts()->save($attempt);
		$attempt->save();

		//Return the rendered question(results) block in a json response
		if($request->get('returnJson') == 'true') {
			$view = View::make('block.showQuestionBlock', ['block' => $question]);
			$contents = $view->render();
			return response()->json(['status' => 'ok', 'content' => $contents]);
		}
		//return to the previous (question or quiz) view
		return back();
	}


	function deleteQuestionAttempt( Request $request, int $questionId ) {
		$question = QuestionBlock::find($questionId);
		if($attempt = $question->attemptForUser()) {
			$attempt->delete();
		}
		//Return the rendered question(results) block in a json response
		if($request->get('returnJson') == 'true') {
			$view = View::make('block.showQuestionBlock', ['block' => $question]);
			$contents = $view->render();
			return response()->json(['status' => 'ok', 'content' => $contents]);
		}
		//return to the previous (question or quiz) view
		return back();
	}
}