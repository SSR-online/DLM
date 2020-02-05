<?php

namespace App;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use App\Rules\Embeddable;

class LinkBlock extends Block
{

    static $displayName = 'Link';
    
	protected $casts = [
		'settings' => 'array'
	];

    private $allowedDisplayValues = [
        'menu',
        'jump',
        'list',
        'inline'
    ];

	public function targetNode() {
		return $this->belongsTo('App\Node', 'target_id');
	}

    function getClassListAttribute() {
        $classes = [];
        $classes[] = $this->setting('display');
        return implode(' ', $classes);
    }

    public function process( Request $request) {
        $this->url = $request->get('url');
        $targetNode = Node::find($request->get('target_id'));
        $this->targetNode()->associate($targetNode);
        $settings = ($this->settings) ? $this->settings : [];
        $settings['display'] = in_array($request->get('display'), $this->allowedDisplayValues) ? $request->get('display') : 'list';

        if($settings['display'] == 'inline') {
            // Can't use this because server can't call out
            // $validatedData = $request->validate([
            //     'url' => ['required', new Embeddable]
            // ]);
        }

        $this->settings = $settings;
    }

    public function hydrateFromImport($object) {
        $this->target_id = $object->target_id;
        $this->url = $object->url;
        $this->url = $object->url;
        return parent::hydrateFromImport($object);
    }
}