<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \App\AnswerOption;

class AnswerOptionController extends Controller
{
    function confirmDelete(AnswerOption $answerOption) {
    	$node = $answerOption->question->node;
    	return view('delete', [
            'title' => $answerOption->content,
            'cancel_url' => '/node/edit/' . $node->id,
            'delete_action' => '/answeroption/' . $answerOption->id . '/delete'
        ]);
    }

    function delete(AnswerOption $answerOption) {
    	$node = $answerOption->question->node;
    	$answerOption->delete();
    	return redirect('/node/edit/' . $node->id);
    }
}
