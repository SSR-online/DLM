<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\User;
use Cache;

class Module extends Model
{
	use Traits\HasSettings;
	protected $casts = [ 'settings' => 'array' ];

	private $nodesByParent = -1;

	public function nodes() {
	        return $this->hasMany('App\Node')->orderBy('sort_order');
	}

	public function nodesByParent( bool $withBlocks = false ) {
		if(is_int($this->nodesByParent)) {
			$nodes = $this->nodes->load(['module', 'children', 'block', 'parent'])->filter( function($node, $withBlocks) {
				// Optionally skip nodes (block can determine if node has children)
				return ($node->block) ? $node->block->hasChildren : true;
			})->groupBy('parent_id');
			$this->nodesByParent = (!$nodes->isEmpty()) ? $nodes : false;	
		};
		return $this->nodesByParent;
	}

	public function scoreForUser( User $user ) {
		$type = $this->setting('score_by');
		switch ($type) {
			case 'pages':
				$pagesSeen = $user->setting('nodes_seen');
				if(!$pagesSeen || !is_array($pagesSeen)) { return null; }
				$pageNodes = $this->nodes()->where(function($query){
					$query->whereNull('parent_id')->orWhere('is_page', 1);
				})->get();
				$pageCount = count($pageNodes);
				$nodesSeen = $pageNodes->filter( function($node) use ($pagesSeen) {
					return in_array($node->id, $pagesSeen);
				});
				$pagesSeenCount = count($nodesSeen);
				$score = min($pagesSeenCount / $pageCount, 1);
				return $score;
				break;
			case 'quiz':
				$quizNodes = $this->nodes->filter(function($node) {
		            return class_basename($node->block) == 'QuizBlock';
		        });
		        $scores = collect();
		        foreach($quizNodes as $node) {
		            $attempt = $node->block->current_user_attempt();
		            if(!$attempt || !$attempt->complete) {
		                return; // User hasn't completed all quizzes, no need to send scores
		            }
		            $scores->push($attempt->score);
		        }
		        $average = $scores->sum() / max($scores->count(), 1);
		        return $average / 10; //Scoring expects a value between 0 and 1, or a percentage;
		        break;
			default:
				return null;
				break;
		}
	}
}
