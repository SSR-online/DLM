<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Message;
use Illuminate\Http\Request;

class DiscussionBlock extends Block
{
    static $displayName = 'Conversation';

    public function messages() {
    	return $this->hasMany('App\Message', 'block_id')->where('archived', '=', null)->orWhere('archived', '=', 0);
    }

    public function process( request $request ) {
    	if($request->get('archive_all') == 'on') {
    		Message::where('block_id', $this->id)->update(['archived' =>1]);
    	}
    }
}
