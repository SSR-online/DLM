<?php

namespace App\Observers;

use App\FileBlock;
use \Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class FileBlockObserver
{
    public function deleting(FileBlock $block)
    {
        Log::info('deleting file block');
    	$module = $block->node->module;
    	$path = $block->path;
    	foreach($module->nodes as $node) {
            if($node->is($block->node)) { continue; }
    		if($node->block && class_basename($node->block) == 'FileBlock') {
    			if($node->block->path == $block->path) {
                    Log::info('file still in use, not deleting');
    				return; //Don't delete file, used by other block
    			}
    		}
    	}
        Storage::disk('public')->delete($block->path);
    }
}
