<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \App\Node;
use \App\LayoutSlot;

use Log;
class SlotController extends Controller {

	// This receives (via post) an array of node ids to sort into itself
	// The array may be dirtied with non-existent ids
	public function sort(Request $request, LayoutSlot $slot) {
		$potentialIds = $request->get('order');
		$nodes = Node::whereIn('id', $potentialIds)->get();
		for ($i=0; $i < count($potentialIds); $i++) { 
			$node = $nodes->firstWhere('id', $potentialIds[$i]);
			if($node) {
				$node->layoutSlot()->associate($slot);
				$node->sort_order = $i;
				$node->save();
			}
			Log::info('node ' .$node->id);
		}
		return $nodes->toJson();
	}
}