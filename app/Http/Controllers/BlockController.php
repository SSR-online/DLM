<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \App\Module;
use \App\Node;
use \App\Block;
use \App\QuestionBlock;
use \App\LayoutSlot;

class BlockController extends Controller
{
	private $types = [
		'QuestionBlock',
		'TextBlock',
		'DetailsBlock',
		'ImageBlock',
		'VideoBlock',
		'QuizBlock',
		'AsideBlock',
		'LinkBlock',
		'H5PBlock',
		'FileBlock',
		'DiscussionBlock',
	];

	public function select( Module $module, Node $node ) {
        return view('block.select', ['module' => $module, 'node' => $node, 'urlPrefix' => "/node/{$node->id}"] );
	}

	public function selectWithSlot( Node $node, LayoutSlot $layoutslot ) {
        return view('block.select', ['module' => $node->module, 'node' => $node, 'urlPrefix' => "/node/{$node->id}/slot/{$layoutslot->id}"] );
	}
	public function create( Node $node, string $type ) {
		return $this->createWithSlot($node, null, $type);
	}
    public function createWithSlot( Node $node, LayoutSlot $layoutslot = null, string $type ) {
    	if( !in_array($type, $this->types)) { return 'error, not a valid type'; }

    	// Always create a subnode and add the block to that
		$parent_node = $node;
		$module = $node->module;
		$node = new Node();
		$node->module()->associate($module);
		$node->parent()->associate($parent_node);
		if($layoutslot) {
			$node->layoutSlot()->associate($layoutslot);
		}
		$full_type = 'App\\' . $type;
        $block = new $full_type();
        
	    $block->save();
	    $node->block()->associate($block);
        $node->save();

        return view('node.edit', ['node' => $node]);
    }
}
