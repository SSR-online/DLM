<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \App\Node;
use \App\Module;
use \App\Block;
use \App\QuestionBlock;
use \App\AnswerOption;
use \App\LayoutSlot;
use App\Events\NodeViewedEvent;
use App\Providers\LTIServiceProvider;
use Auth;

class NodeController extends Controller
{
    public function show( Request $request, Module $module, Node $node) {
            $url_parts = parse_url($request->getRequestUri());
            if($url_parts['path'] != $node->path()) {
                return redirect($node->path());
            }
            $this->authorize('view', $node);
            event(new NodeViewedEvent($node));
            return view('node.show', [ 'module' => $module, 'node' => $node, 'can_update_module' => Auth::user()->can('update', $module)] );
    }

    public function edit( Node $node ) {
        $this->authorize('update', $node);
        return view('node.edit', ['node' => $node ] );
    }

    public function createInSlot( Node $node, LayoutSlot $layoutslot ) {
        $this->authorize('update', $node);
        $new_node = new Node(); 
        if($node) {
            $new_node->parent()->associate($node);
        }
        $new_node->module()->associate($node->module);
        $new_node->layoutSlot()->associate($layoutslot);
        return view('node.create', ['node' => $new_node]);
    }

    public function create( Module $module, Node $node, $asPage = false ) {
        $this->authorize('update', $module);
        $new_node = new Node(); 
        if($node) {
            $new_node->parent()->associate($node);
        }
        if($asPage) {
            $new_node->is_page = true;
            $new_node->addSetting('show_in_menu', true);
        }
        $new_node->module()->associate($module);

        return view('node.create', ['node' => $new_node ]);
    }

    public function duplicate(Node $node) {
        $node->duplicate();
        return redirect($node->path());
    }
    
    public function createAsPage( Module $module, Node $node, $asPage = false ) {
        return $this->create($module, $node, true);
    }

    public function confirmDelete( Node $node ) {
        $this->authorize('delete', $node);
        return view('delete', [
            'title' => $node->title,
            'cancel_url' => '/node/edit/' . $node->id,
            'delete_action' => '/node/' . $node->id . '/delete'             
        ]);
    }

    public function delete( Node $node ) {
        $this->authorize('delete', $node);
        $path = ($node->parent) ? $node->parent->path() : optional($node->module->nodes->first())->path();
        $node->delete();
        return redirect($path);
    }

    public function save( Request $request, Node $node) {
        // $this->authorize('update', $node);
        if( !$node->module ) {
            $module = Module::find($request->input('module_id'));
            $node->module()->associate($module);
        }
        $node->title = $request->input('title');
        $node->description = $request->input('description');
        $node->is_page = ($request->input('is_page') != null);
        $node->previous_id = $request->input('previous_id');
        $node->next_id = $request->input('next_id');
        $node->layout_slot_id = $request->input('layout_slot_id');

        if($request->input('is_page') != null) { //Can't call is_page yet, because parent hasn't been set here
            $node->layout_slot_id = null; //Pages can't be in a layout slot
        }
        
        $node->sort_order = $this->getSortOrder($node);
        $node->addSetting('previous_title', $request->input('previous_title'));
        $node->addSetting('next_title', $request->input('next_title'));
        $node->addSetting('template', $request->input('template'));
        
        if($request->get('add_jump_link')) {
            $this->add_jump_link($node);
            $redirect = redirect('/node/edit/' . $node->id);
        }

        $show_in_menu = ($request->get('show_in_menu')) ? true : false;
        $node->addSetting('show_in_menu', $show_in_menu);
        
        $jumps = [];
        if($node->setting('jump_nodes', false)) {
            for($i = 1; $i <= count($node->setting('jump_nodes', false)); $i++) {
                $jumps[$i]['id'] = $request->get('jump_id_' . $i);
                $jumps[$i]['name'] = $request->get('jump_name_' . $i);
            }
        }
        $node->addSetting('jump_nodes', $jumps);
        
        if($request->input('parent_id') > 0) {
            $parent = Node::find($request->input('parent_id'));
            $node->parent()->associate($parent);
        } elseif($request->input('parent_id') === 0) {
            $node->parent()->dissociate();
        }
        if($request->input('nodes_sort_order')) {
            $inputs = explode(',', $request->input('nodes_sort_order'));
        }
        foreach($node->children as $child_node) {
            if($child_index = array_keys($inputs, $child_node->id)) {
                $child_node->sort_order = $child_index[0];
            } else {
                $child_node->sort_order = ($request->input('sort_order_' . $child_node->id) > 0) ? $request->input('sort_order_' . $child_node->id) : null;
            }
            $child_node->save();
        }
        $node->save();
        if($node->block) {
            $redirect = $node->block->process( $request );
            $node->block->save();
        }
        if(!empty($redirect)) {
            return $redirect;
        } else {
            return redirect($node->path());
        }
    }

    public function getDeleteJump(Node $node, Node $jump_node) {
        $this->authorize('update', $node);
        return view('delete', [
            'title' => 'jump naar ' . $jump_node->title, 
            'delete_action' => '/node/'.$node->id.'/jump/'.$jump_node->id.'/delete',
            'cancel_url' => '/node/edit/' . $node->id
        ]);
    }

    public function postDeleteJump(Node $node, Node $jump_node) {
        $this->authorize('update', $node);
        $jump_nodes = ($node->setting('jump_nodes', false)) ? $node->setting('jump_nodes', false) : [];
        foreach($jump_nodes as $key=>$jump) {
            if($jump['id'] == $jump_node->id) {
                unset($jump_nodes[$key]);
                break;
            }
        }
        $node->addSetting('jump_nodes', $jump_nodes);
        $node->save();
        return redirect('/node/edit/'. $node->id);
    }

    public function startMove( Node $node ) {
        $this->authorize('update', $node);
        session(['ismoving' => $node->id]);
        return redirect($node->path());
    }

    public function stopMove( Node $node ) {
        $this->authorize('update', $node);
        session(['ismoving' => false]);
        return redirect($node->path());
    }

    // TODO: Edit the slot for this node as well (remove, if ancestor has a slot, always?)
    public function moveToTargetNode( Node $node, Node $targetnode = null) { 
        $this->authorize('update', $node);
        if($targetnode && $targetnode->id != $node->id) {
            $node->parent()->associate($targetnode);
        } else {
            $node->parent()->dissociate();
        }
        $node->layoutSlot()->dissociate();
        $node->save();
        session(['ismoving' => false]);
        return redirect($node->path());
    }

    public function moveToTargetSlot( Node $node, LayoutSlot $targetslot, $position = 'top' ) { 
        $this->authorize('update', $node);
        // Set parent to the first ancestor that's a page, because a node 
        // directly in a layout slot is always a child of the page.
        if($node->page()->id != $node->id) {
            $node->parent()->associate($node->page());
            $node->layoutSlot()->associate($targetslot);
        }
        if($position == 'top') {
            $node->sort_order = 0;
        } else {
            $last_node = $targetslot->nodes->last();
            $node->sort_order = $last_node->sort_order + 1;
        }
        $node->save();
        session(['ismoving' => false]);
        return redirect($node->path());
    }

    private function add_jump_link(Node $node) {
        $jump_nodes = ($node->setting('jump_nodes', false)) ? $node->setting('jump_nodes', false) : [];
        $jump_nodes[count($jump_nodes)+1] = [-1, ''];
        $node->addSetting('jump_nodes', $jump_nodes);
    }

    private function getSortOrder(Node $node) {
        if($node->sort_order !== null) {
            return $node->sort_order;
        } else {
            if($node->parent) {
                return count($node->parent->children) + 1;
            } else {
                return count($node->module->nodes) + 1;
            }
        }
    }
}
