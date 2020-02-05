<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class H5PLibrary extends Model
{
    protected $table = 'h5p_libraries';

    public function required_libraries() {
    	return $this->belongsToMany('App\H5PLibrary', 'h5p_library_libraries', 'library_id', 'required_library_id')->withPivot('dependency_type');
    }
}