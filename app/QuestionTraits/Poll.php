<?php

namespace App\QuestionTraits;

trait Poll {

    public function renderPollResults() {
    	$view = \View::make('block.question.pollresult', ['block' => $this]);
    	return $view;
    }
}