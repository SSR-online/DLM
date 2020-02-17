<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use \App\Module;
use \App\Node;
use \App\Layout;
use \App\LayoutNode;
use ZipArchive;
use File;
use DB;
use Log;

class ModuleImporter extends Controller
{
    private $nodeMapping; // Holds mapping from original node ID's to new node ID's.
    private $slotMapping; // Holds mapping from original node slot ID to new slot id.
    private $module;

    public function import() {
        $this->authorize('create', Module::class);
        return view('module.import');
    }

    public function processImport( Request $request ) {
        $this->authorize('create', Module::class);
        $file = $request->file('file');
        $extractedPath = $this->extractZip($file);
        
        DB::beginTransaction();

        $moduleToImport = json_decode(file_get_contents($extractedPath . DIRECTORY_SEPARATOR . 'contents.json'));


        $this->module = $this->createModule($moduleToImport);
        $this->module->save();

        $this->importFiles($extractedPath, $moduleToImport->id);

        $this->createNodes($moduleToImport);
        $this->setupNodeRelationships();
        DB::commit();
        return redirect('/');
    }

    private function extractZip( $file ) {
        $zip = new ZipArchive();
        
        if ($zip->open($file) !== true) {
            dd('error');
        }
        $zip->extractTo(storage_path('app/tmp/' . $file->getClientOriginalName() . '-extracted'));
        return storage_path('app/tmp/' . $file->getClientOriginalName() . '-extracted');
    }

    //Import all files in their relative place, except the contents.json file, images and files are supported
    private function importFiles($path, $oldModuleId) {
        if(file_exists($path . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR . $oldModuleId)) {
            if(!file_exists(storage_path('app' . DIRECTORY_SEPARATOR .'public' . DIRECTORY_SEPARATOR .'images'))) {
                mkdir(storage_path('app' . DIRECTORY_SEPARATOR .'public' . DIRECTORY_SEPARATOR .'images'));
            }
            rename($path . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR . $oldModuleId, storage_path('app' . DIRECTORY_SEPARATOR .'public' . DIRECTORY_SEPARATOR .'images' . DIRECTORY_SEPARATOR . $this->module->id));
        }
        if(file_exists($path . DIRECTORY_SEPARATOR . 'files' . DIRECTORY_SEPARATOR . $oldModuleId)) {
            if(!file_exists(storage_path('app' . DIRECTORY_SEPARATOR .'public' . DIRECTORY_SEPARATOR .'files'))) {
                mkdir(storage_path('app' . DIRECTORY_SEPARATOR .'public' . DIRECTORY_SEPARATOR .'files'));
            }
            rename($path . DIRECTORY_SEPARATOR . 'files' . DIRECTORY_SEPARATOR . $oldModuleId, storage_path('app' . DIRECTORY_SEPARATOR .'public' . DIRECTORY_SEPARATOR .'files' . DIRECTORY_SEPARATOR . $this->module->id));
        }
    }

    private function createModule($moduleObject) {
        $new_module = new Module();
        $new_module->title = $moduleObject->title . ' (kopie)';
        $new_module->description = $moduleObject->description;
        foreach($moduleObject->settings as $key=>$value) {
            $new_module->addSetting($key, $value);
        }
        return $new_module;
    }

    private function createNodes($moduleObject) {
        foreach($moduleObject->nodes as $node) {
            $newNode = new Node();
            $newNode->title = $node->title;
            $newNode->slug = $node->slug;
            $newNode->description = $node->description;
            $newNode->block_type = $node->block_type;
            $newNode->sort_order = $node->sort_order;
            $newNode->is_page = $node->is_page;

            $newNode->parent_id = $node->parent_id;
            $newNode->previous_id = $node->previous_id;
            $newNode->next_id = $node->next_id;
            $newNode->layout_slot_id = $node->layout_slot_id;
            if(!empty($node->settings)) {
                foreach($node->settings as $key=>$value) {
                    $newNode->addSetting($key, $value);
                }
            }

            $newNode->module()->associate($this->module);
            $newNode->save();
            $this->nodeMapping[$node->id] = $newNode->id;
            //Todo: Blocks, relationships and layouts, 
            $this->createLayouts($node, $newNode);
            $this->createBlock($node, $newNode);
        }
    }

    private function createLayouts($node, $newNode) {
        if(count($node->layouts)>0) {
            foreach($node->layouts as $layout) {
                $newLayout = new Layout();
                $newLayout->type = $layout->type;
                $newNode->layouts()->save($newLayout);
                $newLayout->save();
                // Slots get auto-created on save, so find them
                // and fill the slot mapping table accordingly
                for($i = 0; $i < $newLayout->slots->count(); $i++) {
                    $this->slotMapping[$layout->slots[$i]->id] = $newLayout->slots[$i]->id;
                }
            }
        }
    }

    private function createBlock($nodeObject, $newNode) {
        $class = $nodeObject->block_type;
        if($class == null) { return; }
        $block = new $class();        
        if(!is_subclass_of($block, 'App\Block')) {
            dd([$block, 'not a known block']);

        }
        $blockObject = $nodeObject->block;
        
        if($nodeObject->block_type == 'App\ImageBlock' || $nodeObject->block_type == 'App\FileBlock') {
            $blockObject->path = $this->changePath($blockObject->path);
        }
        
        $block->hydrateFromImport($blockObject);
        $block->save();
        $newNode->block()->associate($block);
        $newNode->save();
    }

    private function changePath($path) {
        if($path == null) { return; }
        $path_parts = explode(DIRECTORY_SEPARATOR, $path);
        if(count($path_parts) != 3) { Log::error('No valid path found'); return; }
        $path_parts[1] = $this->module->id;
        $new_path = implode(DIRECTORY_SEPARATOR, $path_parts);
        return $new_path;
    }

    private function setupNodeRelationships() {
        //Take all the new nodes, and change their relationship ids to the correct new ones, based on the relationship mapping.
        //Don't forget the settings columns
        foreach($this->module->nodes as $node) {
            $node->parent_id = $this->mappedNode($node->parent_id);
            $node->next_id = ($node->next_id > -1) ? $this->mappedNode($node->next_id) : $node->next_id;
            $node->previous_id = ($node->previous_id > -1) ? $this->mappedNode($node->previous_id) : $node->previous_id;
            // $node->layout_slot_id = ($node->layout_slot_id) ? $this->nodeMapping[$node->layout_slot_id] : null;
            $jumps = $node->setting('jump_nodes');
            if(is_array($jumps) && count($jumps) > 0) {
                $new_jumps = [];
                foreach($jumps as $jump) {
                    $new_jump = [];
                    $new_jump['id'] = ($jump['id'] != -1) ? $this->mappedNode($jump['id']) : -1;
                    $new_jump['name'] = $jump['name'];
                    $new_jumps[] = $new_jump;
                }
                $node->addSetting('jump_nodes', $new_jumps);
            }
            $node->layout_slot_id = $this->mappedSlot($node->layout_slot_id);
            $node->save();

            if($node->block_type == 'App\LinkBlock') {
                $node->block->target_id = $this->mappedNode($node->block->target_id);
                $node->block->save();
            }
        }
    }

    private function mappedNode($id) {
        if($id != null) {
            if(array_key_exists($id, $this->nodeMapping)) {
                return $this->nodeMapping[$id];
            } else {
                Log::info("Key $id not found in mapping \n, returning -1 (not found)");
                return -1;
            }
        }
        return null;
    }

    private function mappedSlot($id) {
        if($id != null) {
            if(array_key_exists($id, $this->slotMapping)) {
                return $this->slotMapping[$id];
            } else {
                Log::info("Key $id not found in mapping \n, returning -1 (not found)");
                return -1;
            }
        }
        return null;
    }
    //Create the blocks as well, copying files? should blocks have an 'createFromImport' function?
    //Don't forget to stop rolling back the transaction (and commit)
    
}