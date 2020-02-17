<?php

namespace App;

use DB;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;

class Block extends Model
{
	protected $casts = [ 'settings' => 'array' ];

	public $hasChildren = false;

	public $configuration = [];

	public function __construct(array $attributes = []) {
		parent::__construct($attributes);
		$blockType = DB::table('block_types')->where('block', class_basename($this))->first();
		if($blockType && $blockType->configuration) {
			$blockType->configuration = json_decode($blockType->configuration, $assoc = true);
			foreach($this->configuration as $key=>$conf) {
				$this->configuration[$key] = (array_key_exists($key, $blockType->configuration)) ? $blockType->configuration[$key] : $this->configuration[$key];
			}
		}
	}

	public function node() {
		return $this->morphOne('App\Node', 'block');
	}

	public function duplicate() {
		return $this->replicate();
	}

	public function setting($key) {
		if(!is_array($this->settings)) { return false; }
		if(array_key_exists($key, $this->settings)) {
			return $this->settings[$key];
		}
		return false;
	}

	public function addSetting($key, $value) {
		$this->settings = ($this->settings) ? $this->settings : [];
		$this->settings = array_merge($this->settings, [$key => $value]);
		return true;
	}

	public function process( Request $request) {
		
	}

	public function serializeChildren() {
		return null;
	}

	public function hydrateFromImport($object) {
		if(property_exists($object, 'settings')) { $this->settings = $object->settings; }
		return $this;
	}

    protected function get_configuration($key) {
    	if(!array_key_exists($key, $this->configuration)) { return false; }
        return (array_key_exists('value', $this->configuration[$key])) ? $this->configuration[$key]['value'] : $this->configuration[$key]['default'];
    }

	public function saveConfiguration($configuration = null) {
		if($configuration) { $this->configuration = $configuration; }
		$blockType = DB::table('block_types')->where('block', class_basename($this))->first();
		if($blockType) {
			DB::table('block_types')->where('block', class_basename($this))->update(['configuration' => json_encode($this->configuration)]);
		} else {
			DB::table('block_types')->insert([
				'block' => class_basename($this),
				'configuration' => json_encode($this->configuration)
			]);
		}
	}
}