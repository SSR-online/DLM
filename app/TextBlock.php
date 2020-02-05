<?php

namespace App;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;

class TextBlock extends Block
{
	private $display_classes = [
		'goals' => ['goals', 'highlight'],
		'intro' => ['intro'],
        'tip' => ['tip', 'highlight'],
        'highlight' => ['highlight'],
        'sources' => ['sources'],
        'duration' => ['duration']
	];

    static $displayName = 'Text';

    public function process( request $request ) {
    	$display_as = ($request->input('display_as') != -1) ? $request->input('display_as') : null;
    	$this->addSetting('display_as', $display_as);
    	$this->addSetting('class_list', $request->input('classlist'));
        $this->addSetting('timestamp', str_replace(',', '.', $request->get('timestamp')));
        $unskippable = ($request->input('unskippable')) ? 1 : 0;
        $this->addSetting('unskippable', $unskippable);
    	$this->content = $request->input('content');
    }

    public function getclassListAttribute() {
    	$classes = [];
    	if($this->setting('display_as') && array_key_exists($this->setting('display_as'), $this->display_classes) ) {
    		$classes = $this->display_classes[$this->setting('display_as')];
    	}
    	if($classlist = $this->setting('class_list')) {
	    	$classes = array_merge($classes, explode(' ', trim($classlist)));
	    }
	    return implode(' ', $classes);
    }

    public function hydrateFromImport($object) {
        $this->content = $object->content;
        return parent::hydrateFromImport($object);
    }
}