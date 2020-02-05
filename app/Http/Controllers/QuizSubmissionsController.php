<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Node;
use App\QuizAttempt;
use App\QuizResults;

class QuizSubmissionsController extends Controller
{
    public function show( Node $node) {
        $this->authorize('viewResults', $node->block);
        $attempts = $node->block->attempts()->orderBy('updated_at', 'DESC')->get()->filter(function($attempt) {
            foreach($attempt->quiz->node->children->filter(function($item) {
                return (class_basename($item->block) == 'QuestionBlock');
            }) as $question) {
                if(!$question->block->attemptForUser($attempt->user_id, $attempt)) {
                    return false;
                }
            }
            return true;
        })->unique('user_id');
        // dd($attempts);
        return view('block.quiz.showsubmissions', [ 'node' => $node, 'block' => $node->block, 'attempts' => $attempts] );
    }

    public function single( Node $node, QuizAttempt $attempt) {
    	$this->authorize('viewResults', $node->block);
        return view('block.quiz.showsinglesubmission', [ 'node' => $node, 'attempt' => $attempt] );	
    }

    public function aggregate( Node $node ) {
        $this->authorize('viewResults', $node->block);
        $results = new QuizResults( $node->block );
        return view('block.quiz.showaggregateresults', [ 'node' => $node, 'results' => $results] ); 
    }
}