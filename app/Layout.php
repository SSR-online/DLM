<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use \App\LayoutSlot;

class Layout extends Model
{
 	public static $types = [
 		'full' 			=> 'Full width',
 		'standard' 		=> 'Standard', 
 		'twocol' 		=> 'Two columns', 
 		'withsidebar' 	=> 'Wide and small'
 	];

	protected $dispatchesEvents = [
        'deleting' => Observers\LayoutObserver::class,
    ];

	public function node() {
		return $this->belongsTo('App\Node');
	}

	public function slots() {
		return $this->hasMany('App\LayoutSlot');
	}

	public function slotCount() {
		$slots = 1;
		if( $this->type == 'twocol'
		 || $this->type == 'withsidebar'
		) {
			$slots = 2;
		}
		return $slots;
	}

	public function isCreated() {
		$slot_count = $this->slotCount();
		for($i = 0; $i < $slot_count; $i++) {
			$slot = new LayoutSlot();
			$slot->layout()->associate($this);
			$slot->save();
		}
	}

	public function isSaved() {
		$slot_count = $this->slotCount();
		$old_slot_count = count($this->slots);
		if($old_slot_count > $slot_count) {
			for($i = $slot_count; $i < $old_slot_count; $i++) {
				$this->slots->last()->delete();
			}
		} else if($old_slot_count < $slot_count) {
			for($i = $old_slot_count; $i < $slot_count; $i++) {
				$slot = new LayoutSlot();
				$slot->layout()->associate($this);
				$slot->save();
			}
		}
	}
}