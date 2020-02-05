<?php

namespace App\Observers;

use App\ImageBlock;
use \Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ImageBlockObserver
{
    public function deleting(ImageBlock $block)
    {
    	$module = $block->node->module;
    	$path = $block->path;
    	foreach($module->nodes as $node) {
            if($node->is($block->node)) { continue; }
    		if($node->block && class_basename($node->block) == 'ImageBlock') {
    			if($node->block->path == $block->path) {
                    Log::info('image still in use, not deleting');
    				return; //Don't delete image, used by other block
    			}
    		}
    	}
        Storage::disk('public')->delete($block->path);
    }
}
