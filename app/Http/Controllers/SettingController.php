<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Node;
use App\Block;
use App\Setting;
use Log;

class SettingController extends Controller
{
    private function blocks_with_configuration() {
        $results = [];
        $node = new Node();
        $blocks = $node->getChildTypesAttribute();
        foreach($blocks as $block_name) {
            $block = new $block_name();
            if(count($block->configuration) > 0) {
                $results[] = $block;
            }
        }
        return collect($results);
    }

    private function nav($blocks) {
        foreach($blocks as $block) {
            $nav[] = [
                'name' => class_basename($block),
                'text' => get_class($block)::$displayName
            ];
        }
        return $nav;
    }

	public function list($block = null) {
        $this->authorize('view', Setting::class);
        $blocks = $this->blocks_with_configuration();
        $nav = $this->nav($blocks);
        $viewVariables = ['settings'=>Setting::all(), 'nav' => $nav];
        if($block) {
            $configBlock = $blocks->first(function($val) use($block) {
                return class_basename($val) == $block;
            });
            $viewVariables['configuration'] = $configBlock->configuration;
            $viewVariables['blockname'] = class_basename($configBlock);
        }
        return view('setting.list', $viewVariables);
    }

    public function create() {
        $this->authorize('create', Setting::class);
        $setting = new Setting();
        return view('setting.create');
    }

    public function save( Request $request, Setting $setting = null ) {
    	if( $setting == null ) {
    		$setting = new Setting();
    	}
        $this->authorize('update', Setting::class);
        $setting->name = $request->get('name');
        $setting->value = $request->get('value');
        
    	$setting->save();
    	return redirect('/settings/');
    }

    public function saveBlock(Request $request, $blockClass = null) {
        $blockClass = "App\\" . $blockClass;
        $block = new $blockClass();
        foreach($block->configuration as $key=>$config) {
            if(is_array($config['default'])) {
                $new_array = $request->get($key);
                $new_array = preg_split("/\r\n|\n|\r/", $new_array);
                $block->configuration[$key]['value'] = $new_array;
            } else {
                $block->configuration[$key]['value'] = ($request->get($key)) ? $request->get($key) : false;
            }
        }
        $block->saveConfiguration();
        return back();
    }
}