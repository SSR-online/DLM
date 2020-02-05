<?php

namespace App\Observers;

use App\Node;
use \Illuminate\Support\Facades\Log;

class NodeObserver
{
    public function deleting(Node $node)
    {
		if($node->layouts) {
			foreach($node->layouts as $layout) {
				$layout->delete();
			}
	   	}

        if($node->block) {
            $node->block->delete();
        }
    }
}
