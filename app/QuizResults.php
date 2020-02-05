<?php

namespace App;

class QuizResults {
	private $quiz;
	
	public function __construct( QuizBlock $quiz ) {
		$this->quiz = $quiz;
	}

	public function question_results() {
		$questionNodes = $this->quiz->node->children->filter(function($node) {
			return class_basename($node->block) == 'QuestionBlock';
		});

		$totals = [];
		foreach($questionNodes as $questionNode) {
			$totals[$questionNode->id]['question'] = $questionNode->block;
			$totals[$questionNode->id]['totals'] = $questionNode->block->answerTotals();
		}
		return $totals;
	}

}