<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \App\Node;
use \App\Layout;

class LayoutController extends Controller
{
    public function create( Node $node ) {
        return view('layout.create', ['node' => $node]);
    }

    public function postCreate(Request $request, Node $node ) {
    	$layout = new Layout();
    	$layout->title = $request->input('title');
    	$layout->type = $request->input('type');
    	$node->layouts()->save($layout);
    	$layout->save();
    	return redirect($node->path());
    }

    public function edit ( Layout $layout ) {
        return view('layout.edit', ['layout' => $layout]);
    }

    public function postEdit(Request $request, Layout $layout ) {
        $layout->title = $request->input('title');
        $new_type = $request->input('type');
        if($new_type != $layout->type) {
            $old_slots = $layout->slotCount();
            $layout->type = $new_type;
            $new_slots = $layout->slotCount();

            //merge nodes from higher slot number into lower
            if($new_slots < $old_slots) {
                $first_slot = $layout->slots->first();
                for($i = $new_slots; $i < $old_slots; $i++) {
                    $current = $layout->slots->slice($i)->first();
                    // dump($current);
                    foreach($current->nodes as $node) {
                        $node->layoutSlot()->associate($first_slot);
                        $node->save();
                    }
                }
            }
            $layout->save();
            return redirect($layout->node->path());
        }
    }
    
    public function confirmDelete( Layout $layout ) {
        return view('delete', [
            'title' => $layout->title,
            'cancel_url' => '/layout/' . $layout->id . '/edit',
            'delete_action' => '/layout/' . $layout->id . '/delete'             
        ]);
    }

    public function delete( Layout $layout ) {
        $node = $layout->node;
        foreach($layout->slots as $slot) {
            foreach($slot->nodes as $node) {
                $node->layoutSlot()->dissociate();
                $node->save();
            }
            $slot->delete();
        }
        $layout->delete();
        return redirect($node->path());
    }
}
// 