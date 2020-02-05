<?php
namespace App\Traits;

trait HasSettings {
	public function setting($key, $filter = true) {
		if(!is_array($this->settings)) { return null; }
		if(array_key_exists($key, $this->settings)) {
			if(method_exists($this, 'filter_setting') && $filter) {
				return $this->filter_setting($key, $this->settings[$key]);	
			}
			return $this->settings[$key];
		}
		return null;
	}

	public function addSetting($key, $value) {
		$this->settings = ($this->settings) ? $this->settings : [];
		$this->settings = array_merge($this->settings, [$key => $value]);
		return true;
	}
}