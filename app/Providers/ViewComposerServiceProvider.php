<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Cache;
use Log;
use Auth;
use App\Navigation;

class ViewComposerServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    
    private $navigation = false;
    private $nav_nodes = null; //old style

    public function boot()
    {
         $this->composeEditMode();
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    private function composeEditMode() {
        view()->composer('*', function ($view) {
            //Check editing rights for module
            $module = array_key_exists('module', $view->getData()) ? $view->getData()['module'] : null;
            $isediting = ($module) ? (session('isediting') && Auth::user()->can('update', $module)) : session('isediting');
            $view->with('isediting', $isediting);
            $view->with('ismoving', session('ismoving'));
        });

        view()->composer('node.nav', function ($view) {
            if(!$this->navigation) {
                $this->navigation = new Navigation($view->getData()['module'], $view->getData()['node']);
            }
            
             // dump($navigation->tree());
            // dd($this->nodes($view->getData()['module'], $view->getData()['node']));
            // $one = $this->echo_tree($this->navigation->tree());
            // $two = $this->echo_tree($this->nodes($view->getData()['module'], $view->getData()['node']));
            // dump($one);
            // dump($two);
            // dd($one === $two);
            // dd(json_encode($this->nodes($view->getData()['module'], $view->getData()['node'])));
            // dd(json_encode($this->navigation->tree()));
            
            $view->with('nav_nodes', $this->navigation->tree());
            // $view->with('nav_nodes', $this->nodes($view->getData()['module'], $view->getData()['node']));
        });
    }

    // private function echo_tree($nodes) {
    //     $string = '';
    //     foreach($nodes as $node_set) {
    //         $string .= 'id: ' . $node_set['node']->id . '<br />';
    //         if(count($node_set['children'])>0) {
    //            $string .= $this->echo_tree($node_set['children']);
    //         }
    //     }
    //     return $string;
    // }

    // private function nodeList($nodes, $activeNode) {
    //     $nodeList = [];
    //     if(!$nodes) { return $nodeList; }
    //     foreach($nodes as $node) {
    //         if((!$node->is_page && !$node->showInMenu)) { continue; }
    //         if($node->parent && !$node->showInMenu && !session('isediting')) {
    //          continue; }
    //         if($activeNode->has_ancestor($node) || $node->has_descendant($activeNode)) {
    //             $node->active = true;
    //         }
    //         $node->classes = $node->classString();
    //         $node->path_url = $node->path();
    //         $nodeList[$node->id]['node'] = $node;
    //         if(!empty($node->children)) {
    //             $nodeList[$node->id]['children'] = $this->nodeList($node->children, $activeNode);
    //         }
    //     }
    //     return $nodeList;
    // }

    // private function nodes($module, $activeNode) {
    //     if(!$this->nav_nodes) {
    //         $nodesByParent = $module->nodesByParent();
    //         $this->nav_nodes = $this->nodeList($nodesByParent[''], $activeNode);
    //     };
    //     return $this->nav_nodes;
    // }
}
