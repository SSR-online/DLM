<?php

namespace App\Http\Controllers;

use Auth;
use App\Node;
use App\DiscussionBlock;
use App\DiscussionParticipant;
use App\Message;
use Illuminate\Http\Request;

class DiscussionController extends Controller
{
    public function add(Request $request, Node $node) {
        if(class_basename($node->block) != 'DiscussionBlock') { return '{}'; }
    	$message = new Message();
    	$message->message = $request->get('message');
    	$message->user()->associate(Auth::user());
    	$message->discussion()->associate($node->block);
    	$message->save();

        //Update typing state
        $this->updateParticipant($node, Auth::user(), false);
    	return redirect($node->path());
    }

    public function messages( Node $node, $since = null) {
    	if(class_basename($node->block) != 'DiscussionBlock') { return '{}'; }
        $this->updateParticipant($node, Auth::user());
    	$messages = [];
    	if($since != null) {
			$messages = $node->block->messages()->where('created_at', '>', $since)->get();
    	} else {
			$messages = $node->block->messages;
    	}
    	$messages = (string) view('block.discussion.messages', ['messages' => $messages]);
        $typing = DiscussionParticipant::where('discussion_id', $node->block->id)->where('is_typing', true)->where('updated_at', '>=', \Carbon\Carbon::now()->subMinute())->count();
        $present = DiscussionParticipant::where('discussion_id', $node->block->id)->where('updated_at', '>=', \Carbon\Carbon::now()->subMinute())->count();
        // dd($typing);
        return response()->json(['messages' => $messages, 'status' => ['typing' => $typing, 'present' => $present]]);
    }

    public function setIsTyping(Request $request, Node $node ) {
        if(class_basename($node->block) != 'DiscussionBlock') { return '{}'; }
        $is_typing = ($request->get('isTyping') == 'true') ? 1 : 0;
        $this->updateParticipant($node, Auth::user(), $is_typing);
    }

    private function updateParticipant($node, $user, $typing = null) {
        if($typing !== null) {
            $participant = DiscussionParticipant::updateOrCreate(['user_id' => $user->id, 'discussion_id' => $node->block->id], ['updated_at' => new \DateTime(), 'is_typing' => $typing]);
        } else {
            $participant = DiscussionParticipant::updateOrCreate(['user_id' => Auth::user()->id, 'discussion_id' => $node->block->id], ['updated_at' => new \DateTime()]);
        }
    }
}