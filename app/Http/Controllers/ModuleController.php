<?php

namespace App\Http\Controllers;

use ZipArchive;
use File;
use Response;
use Auth;
use DateTime;
use DateTimeImmutable;
use DateInterval;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use \App\Module;
use \App\Node;
use App\Events\NodeViewedEvent;

class ModuleController extends Controller
{

    public function show( Module $module) {
        $this->authorize('view', $module);
        
        if($redirect = $this->followRememberedPosition($module)) {
            return $redirect;
        }

        $node = $module->nodes()->whereNull('parent_id')->first();
        if(!$node) { abort(404); }
        event(new NodeViewedEvent($node));
        return view('node.show', ['module' => $module, 'node' => $node]);
    }

    public function edit( Module $module ) {
        $this->authorize('update', $module);
    	return view('module.edit', ['module' => $module ] );
    }

    /** If the user has visited recently, redirect to last shown node */
    public function followRememberedPosition($module) {
        $remember_position = $module->setting('remember_position');
        if(!isset($remember_position)) { return; }
        $preferences = Auth::user()->preferencesForModule($module);
        if(!$preferences) { return; }
        $lastDate = DateTime::createFromFormat('Y-m-d H:i:s', $preferences->last_node_date);
        $now = new DateTimeImmutable();

        $interval = new DateInterval('PT'.intval($remember_position).'H');

        //If the user has visited recently, open last seen page
        if($lastDate->add($interval) > $now) {
            $node_id = $module->nodes()->find($preferences->last_node_seen)->id;
            return redirect('/module/' . $module->id . '/' . $node_id);
        }
    }

    /* This method should not be on the module */
    public function toggleEditing( Request $request, Module $module ) {
        $this->authorize('update', $module);
        $isediting = session('isediting');
        if($isediting === true) {
            session(['isediting' => false]);
            session(['ismoving' => false]);
        } else {
            session(['isediting' => true]);
        }
        return back();
    }

    /* This method should not be on the module */
    public function stopMoving( Request $request, Module $module ) {
        $this->authorize('update', $module);
        session(['ismoving' => false]);
        return back();
    }

    public function sortNodes( Request $request, Module $module, Node $parent = null) {
        $potentialIds = $request->get('order');
        $nodes = Node::whereIn('id', $potentialIds)->where('module_id', $module->id)->get();
        for ($i=0; $i < count($potentialIds); $i++) { 
            $node = $nodes->firstWhere('id', $potentialIds[$i]);
            if($node) {
                if($parent) {
                    $node->parent()->associate($parent);
                } else {
                    $node->parent()->dissociate();
                }
                $node->sort_order = $i;
                $node->save();
            }
        }
        return $nodes->toJson();
    }

    public function create() {
        $this->authorize('create', Module::class);
        $module = new Module();
        return view('module.create', ['module' => $module ]);
    }

    public function save( Request $request, Module $module = null ) {
    	if( $module == null ) {
    		$module = new Module();
    	} else {
            $this->authorize('update', $module);
        }
    	$module->title = $request->input('title');
    	$module->description = $request->input('description');
        $module->addSetting('score_threshold', $request->input('score_threshold'));
        $module->addSetting('score_by', $request->input('score_by'));
        $module->addSetting('score_by_page_percentage', $request->input('score_by_page_percentage'));
        $module->addSetting('score_by_quiz_percentage', $request->input('score_by_quiz_percentage'));
        $module->addSetting('remember_position', $request->input('remember_position'));

        if($request->input('archive')) {
            $module->archived = true;
        } else {
            $module->archived = null;
        }

    	foreach($module->nodes as $node) {
    		if( $request->has('node_sort_order_' . $node->id)) {
    			$node->sort_order = $request->input('node_sort_order_' . $node->id);
    			$node->save();
    		}
    	}

    	$module->save();

        //Always add a node to new modules
        if(count($module->nodes) == 0) {
            return redirect('/module/'.$module->id.'/page/create/');
        }

    	return redirect('/module/' . $module->id);
    }

    public function confirmDelete( Module $module ) {
        $this->authorize('delete', $module);
        return view('delete', [
            'title' => $module->title,
            'cancel_url' => '/module/edit/' . $module->id,
            'delete_action' => '/module/' . $module->id . '/delete'             
        ]);
    }

    public function delete( Request $request, Module $module ) {
        $this->authorize('delete', $module);

        foreach($module->nodes as $node) {
            $node->delete();
        }

        $module->delete();
        return redirect('/');

        //TODO JB 23/03/2018: Delete files
    }

    public function export( Module $module ) {
        $module = Module::with(['nodes.block', 'nodes.layouts.slots'])->find($module)->first();
        $json = $module->toJson(JSON_PRETTY_PRINT);
        $zip = new ZipArchive();
        $zip_name = 'module-' . $module->id . '-tmp.dlm';
        $files = [];
        foreach($module->nodes as $node) {
            if($node->block) {
                $node->block->serializeChildren();
            }
            if($node->block && in_array(class_basename($node->block), ['ImageBlock', 'FileBlock'])) {
                $files[] = $node->block->path;
            }
            if(class_basename($node->block) == 'H5PBlock') {

            }
        }
        if(!File::exists(storage_path('app/tmp/'))) {
            File::makeDirectory(storage_path('app/tmp'));
        }
        if ($zip->open(storage_path('app/tmp/' . $zip_name), ZipArchive::CREATE) !== true) {
            return Response::json(array('error' => true, 'message' => 'Error creating zip file'));
        }
        // Add the files
        $zip->addFromString('contents.json', $json);
        if(count($files) > 0) {
            foreach($files as $file) {
                if($file != null) {
                    $zip->addFile(storage_path('app/public/' . $file), $file);
                }
            }
        }
        $zip->close();
        return Storage::disk('local')->download('/tmp/' . $zip_name, $zip_name, [
            'Content-Type: application/octet-stream', 
            'Content-Disposition: attachment; filename="'.$zip_name.'"',
            'Content-Transfer-Encoding: binary'
        ]);
    }

    public function archive(Request $request, Module $module) {
        $module->archived = true;
        $module->save();
        return back();
    }  
}