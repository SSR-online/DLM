<?php

namespace App;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Log;

class ImageBlock extends Block
{

    static $displayName = 'Image';

    protected $dispatchesEvents = [
        'deleting' => Observers\ImageBlockObserver::class,
    ];

    function process(Request $request) {
    	if($request->image) {
            if($request->file('image')) {
    		    $path = $request->file('image')->store('images/' . $this->node->module->id, 'public');
    			$this->path = $path;
    		} else {
                $this->path = $request->image;
    		}
    	}
		$this->alt = $request->input('alt');
		$this->longdesc = $request->input('longdesc');
		$this->node->addSetting('display', $request->input('display'));
		$this->node->save();
    }

    public function hydrateFromImport($object) {
        $this->path = $object->path;
        $this->alt = $object->alt;
        $this->longdesc = $object->longdesc;
        return parent::hydrateFromImport($object);
    }
}
