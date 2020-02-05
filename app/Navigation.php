<?php

namespace App;

use DB;
use Auth;

use App\Module;
use App\Node;

use Log;

class Navigation {

	private $nav_nodes;
	private $module;
	private $nodes;
	private $activeNode;
	private $userNodesSeen;
	private $tree;

	public function __construct(Module $module, $activeNode = null) {

		$this->module = $module;
		$this->activeNode = ($activeNode) ? $activeNode : $module->nodes()->first();
		if(is_array(Auth::user()->settings)) {
			$this->userNodesSeen = (array_key_exists('nodes_seen', Auth::user()->settings)) ? Auth::user()->settings['nodes_seen'] : [];
		} else {
			$this->userNodesSeen = [];
		}
		$this->nodes = $this->flatNodes();
	}

	public function tree() {
		if(!$this->tree) {
			$this->tree = $this->nodeTree($this->nodes->where('parent_id', null));
		}
		return $this->tree;
	}

	private function flatNodes() {
		$nodeRecords = DB::table('nodes')
			->select('id', 'title', 'settings', 'parent_id', 'is_page', 'block_id')
			->where('module_id', $this->module->id)
			->orderBy('sort_order')
			->get();
		$filtered_nodes = collect();
		foreach($nodeRecords as $node) {
			$settings = json_decode($node->settings);
			$node->show_in_menu = ($settings && property_exists($settings, 'show_in_menu') && $settings->show_in_menu == true);
		
			if((!$node->is_page && $node->parent_id != null && !$node->show_in_menu)) { continue; }
            if($node->parent_id!=null && !$node->show_in_menu && !session('isediting')) {
             continue; }
			$filtered_nodes->push($node);
		}
		return $filtered_nodes;
	}

	public function nodeTree($nodes, $level = 0, $parent_id = null) {
		$nodeTree = [];
		if(!$this->nodes) { return $nodeTree; }
		foreach($nodes as $node) {
	        $node->active = false;
	        $node->completed = in_array($node->id, $this->userNodesSeen);
			if($this->activeNode->id == $node->id || $this->isActiveNodeAncestor($node)) {
	            $node->active = true;
	        }

	        $node->classes = '';
	        $node->path_url = '/module/' . $this->module->id . '/' . $node->id;
	        $nodeTree[$node->id]['node'] = $node;
	        $children = $this->nodes->where('parent_id', $node->id);
	        if(!empty($children)) {
	        	$next_level = $level + 1;
	            $nodeTree[$node->id]['children'] = $this->nodeTree($children, $next_level, $node->id);
	        }
		}
		return $nodeTree;
	}

    private function nodeList($nodes, $activeNode) {
        $nodeList = [];
        if(!$nodes) { return $nodeList; }
        foreach($nodes as $node) {
            if((!$node->is_page && $node->parent_id != null && !$node->showInMenu)) { continue; }
            if($node->parent && !$node->showInMenu && !session('isediting')) {
             continue; }
            if($this->activeNode->has_ancestor($node) || $node->has_descendant($this->activeNode)) {
                $node->active = true;
            }
            $node->classes = $node->classString();
            $node->path_url = $node->path();
            $nodeList[$node->id]['node'] = $node;
            if(!empty($node->children)) {
                $nodeList[$node->id]['children'] = $this->nodeList($node->children, $this->activeNode);
            }
        }
        return $nodeList;
    }

	private function isActiveNodeAncestor($node) {
		$ancestors = $this->ancestorsForNode($this->activeNode);
		foreach($ancestors as $ancestor) {
			if($ancestor->id == $node->id) {
				return true;
			}
		}
		return false;
	}

	private function isActiveNodeDescendant($node) {
		$ancestors = $this->ancestorsForNode($node);
		foreach($ancestors as $ancestor) {			
			if($ancestor->id == $this->activeNode->id) {
				return true;
			}
		}
		return false;
	}

	private function ancestorsForNode($node) {
		$nodes = [];
		if($parent = $this->nodes->firstWhere('id', $node->parent_id)) {
			$nodes[] = $parent;
			$ancestors = $this->ancestorsForNode($parent);
			if($ancestors) {
				$nodes = array_merge($nodes, $ancestors);
			}
		}
		return $nodes;
	}

	private function parentNodes($flatNodes) {
		return $flatNodes->filter(function($node) {
			return $node->parent_id == null;
		});
	}

	private function childNodesForParent($node_id) {
		return $flatNodes->filter(function($node) use ($node_id) {
			return $node->parent_id == $node_id;
		});
	}

        // if(!$this->nav_nodes) {
        //     $nodesByParent = $this->module->nodesByParent();
        //     dd($this->flatNodes());
        //     $this->nav_nodes = $this->nodeList($nodesByParent[''], $this->activeNode);
        // };
        // dd(json_encode($this->nav_nodes));
        // return $this->nav_nodes;
 //    public function nodesByParent() {
		
	// }
}