<?php

namespace App;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;

class DetailsBlock extends Block
{
	public $can_change_parent = false;

	static $displayName = 'Details';

    public function process(Request $request) {
    	$this->summary = $request->input('summary');
    	$this->details = $request->input('details');
    }

    public function hydrateFromImport($object) {
        $this->summary = $object->summary;
        $this->details = $object->details;
        return parent::hydrateFromImport($object);
    }
}