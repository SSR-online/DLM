<?php

namespace App\Listeners;

use App\Events\QuizCompletedEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Auth;
use Log;
use App\Providers\LTIServiceProvider;

class QuizCompleted
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  QuizCompletedEvent  $event
     * @return void
     */
    public function handle(QuizCompletedEvent $event)
    {
        $module = $event->node->module;
        if($module->setting('score_by') != 'quiz') {
            return; //No need to calculate scores
        }
        $score = $module->scoreForUser(Auth::user());
        if(is_numeric($score)) {
            $this->sendScores($event->node, $score);
        }
    }

    private function sendScores($node, $score) {
        if(!empty(Auth::user()->lti_consumer)) {
            Log::info('returning score by quizzes completed in node: ' . $node->id . ' score: ' . $score);
            $provider = new LTIServiceProvider();
            // $score = $node->scoreForUser(\Auth::user());
            if($node->module->setting('score_threshold') == null || ($node->module->setting('score_threshold') / 100) <= $score) {
                Log::info('sending score');
                $result = $provider->returnScore($score); //TODO: This should be done asynchronously in prod, use queues?
                if(!$result) {
                    Log::info('score NOT sent succesfully');
                }
            }
        }
    }
}
