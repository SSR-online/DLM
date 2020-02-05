<?php

namespace App;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class FileBlock extends Block
{
    static $displayName = 'File';

    private $allowedDisplayValues = [
        'inline',
        'download'
    ];

    protected $dispatchesEvents = [
        'deleting' => Observers\FileBlockObserver::class,
    ];

    function getClassListAttribute() {
        $classes = [];
        $classes[] = $this->getExtensionClass();
        $classes[] = $this->setting('display');
        return implode(' ', $classes);
    }

    function getLinkClassAttribute( $value ) {
        $classes = [];
        $classes[] = $this->getExtensionClass();
        $classes[] = $this->setting('display');
        return implode(' ', $classes);
    }

    function getExtensionClass() {
        $parts = explode('.', $this->path);
        $extension = strtolower(array_pop($parts));
        if(in_array($extension, ['pdf', 'doc', 'docx', 'ppt', 'pttx'])) { return $extension; }
        return '';
    }

    function getDownloadPathAttribute( $value ) {
        return '/module/' . $this->node->module->id .  '/node/' . $this->node->id . '/download';
    }

    function process(Request $request) {
    	if($request->file) {
            if($request->file('file')) {
    		    $path = $request->file('file')->store('files/' . $this->node->module->id, 'public');
    			$this->path = $path;
    		} else {
                $this->path = $request->file;
    		}
    	}
        $display = in_array( $request->get('display'), $this->allowedDisplayValues) ?  $request->get('display') : 'download';
        $this->addSetting('display', $display);
		$this->node->save();
    }
}
