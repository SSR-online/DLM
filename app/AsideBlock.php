<?php

namespace App;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;

class AsideBlock extends Block
{
	public $can_change_parent = false;

	static $displayName = 'Aside';

    public function process(Request $request) {
    	$this->content = $request->input('content');
    	$this->html_id = $request->input('html_id');
    }

    public function hydrateFromImport($object) {
    	$this->content = $object->content;
    	$this->html_id = $object->html_id;
    	return parent::hydrateFromImport($object);
    }
}