<?php

namespace App\Http\Controllers;

use Auth;
use App\Module;
use App\Node;
use App\FileBlock;
use Illuminate\Http\Request;
use Storage; 

class FileController extends Controller {

	public function download(Request $request, Module $module, Node $node) {
		$this->authorize('view', $node);
		if(class_basename($node->block) !== 'FileBlock') { return abort(404); }

		return response()->download(Storage::disk('public')->getDriver()->getAdapter()->getPathPrefix().$node->block->path);
	}
}