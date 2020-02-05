<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Auth;
use Parsedown;

class Message extends Model
{
	public function discussion() {
		return $this->belongsTo('App\DiscussionBlock', 'block_id');
	}

	public function parent() {
		return $this->belongsTo('App\Message', 'parent_id');
	}
	
	public function children() {
		return $this->hasMany('App\Message', 'parent_id')->orderBy('sort_order');
	}

	public function user() {
		return $this->belongsTo('App\User');
	}

	public function getMessageAttribute( $value ) {
		$parsedown = new Parsedown();
		$parsedown->setSafeMode(true);
		return $parsedown->text($value);
	}

	public function isMine() {
		return Auth::user()->is($this->user);
	}
}
