<?php

namespace App\Listeners;

use App\Events\NodeViewedEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Providers\LTIServiceProvider;

use \Auth;
use Log;

class UserNodeViewed
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
     * @param  NodeViewedEvent  $event
     * @return void
     */
    public function handle(NodeViewedEvent $event)
    {
        if ($user = Auth::user()) {
            $nodesSeen = ($user->setting('nodes_seen')) ? $user->setting('nodes_seen') : [];
            $nodesSeen = array_unique(array_merge($nodesSeen, [$event->node->id]));
            $user->addSetting('nodes_seen', $nodesSeen);
            $user->save();
            if($module = $user->moduleWithPreferences($event->node->module)) {
                $module->pivot->preferences = json_encode(['last_node_seen' => $event->node->id, 'last_node_date' => date('Y-m-d H:i:s')]);                
                $module->pivot->save();
            } else {
                $user->modules()->attach($event->node->module, ['preferences' => json_encode(['last_node_seen' => $event->node->id, 'last_node_date' => date('Y-m-d H:i:s')])]);
            }

            //Provide score to LTI provider
            if($event->node->module->setting('score_by') == 'pages') {
                if(!empty($user->lti_consumer)) {
                    Log::info('returning score by pages seen');
                    $provider = new LTIServiceProvider();
                    $score = $event->node->module->scoreForUser(\Auth::user());
                    if($event->node->module->setting('score_threshold') == null || ($event->node->module->setting('score_threshold') / 100) <= $score) {
                        $provider->returnScore($score); //TODO: This should be done asynchronously in prod, use queues?
                    }
                }
            }
        }
    }
}
