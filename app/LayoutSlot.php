<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LayoutSlot extends Model
{
	public function layout() {
		return $this->belongsTo('App\Layout');
	}

	public function nodes() {
		return $this->hasMany('App\Node')->orderBy('sort_order', 'ASC');
	}
}
