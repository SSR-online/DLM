<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DiscussionParticipant extends Model
{
	protected $fillable = ['user_id', 'discussion_id', 'is_typing', 'updated_at']; 

	function Discussion() {
		return $this->belongsTo('App\DiscussionBlock', 'discussion_id');
	}

	function User() {
		return $this->belongsTo('App\User');
	}
}
