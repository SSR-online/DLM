<?php

namespace App\Observers;

use App\Layout;

class LayoutObserver
{
    public function deleting(Layout $layout)
    {
		if($layout->slots) {
			foreach($layout->slots as $slot) {
				$slot->delete();
			}
	   	}
    }
}
