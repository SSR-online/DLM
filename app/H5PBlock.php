<?php

namespace App;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

use App\Providers\H5PProvider;
use App\Providers\H5PFileStorageProvider;

use H5PCore;
use H5PValidator;
use H5PStorage;

class H5PBlock extends Block
{
    protected $table = 'h5p_blocks';

	static $displayName = 'H5P';

    public function mainLibrary() {
    	return $this->belongsTo('App\H5PLibrary', 'main_library_id');
    }

    public function libraries() {
    	return $this->belongsToMany('App\H5PLibrary', 'h5p_blocks_libraries', 'block_id', 'library_id')->withPivot('weight')->orderBy('pivot_weight', 'asc');
    }

    public function json_object() {
    	if(!$this->mainLibrary) { return; }
    	$interface = new H5PProvider($this->id);
		$storage = new H5PFileStorageProvider(Storage::disk('public')->getDriver()->getAdapter()->getPathPrefix() . 'h5p');
		$h5p = new H5PCore($interface, $storage, Storage::disk('public')->getDriver()->getAdapter()->getPathPrefix() . 'h5p');
    	
    	$preloadeddependencies = $h5p->loadContentDependencies($this->id, 'preloaded');

    	$filtered = $this->filtered;
    	if(empty($filtered)) {
			$content = $h5p->loadContent($this->id);
			$filtered = $h5p->filterParameters($content);
	    }

		$files = $h5p->getDependenciesFiles($preloadeddependencies);
		$scripts = [];
		$css = [];
		foreach($files['scripts'] as $file) {
			$scripts[] = '/storage/h5p' . $file->path . $file->version;
		}
		foreach($files['styles'] as $file) {
			$css[] = '/storage/h5p' . $file->path . $file->version;
		}
    	$data = [
		    'library' => $this->mainLibrary->machine_name . ' ' . $this->mainLibrary->major_version . '.' . $this->mainLibrary->minor_version,
		    'jsonContent' => $filtered,
		    'fullScreen' => false,
		    'exportUrl' => env('APP_URL'),
		    'title' => 'test',
		    'displayOptions' => [
		    	'frame' => false,
		    	'export' => true,
		    	'embed' => true,
		    	'copyright' => true,
		    	'icon' => true
		    ],
		    'url' => env('APP_URL') . "/h5p/",
		    'contentUrl' => env('APP_URL') . "/storage/h5p/content/" . $this->id,
		    'contentUserData' => [
		        0 => [
		        	"state" => false
		        ]
		    ],
		    'styles' => $css,
		    'scripts' => $scripts,
		    "exportUrl" => "/path/to/download.h5p",
  			"embedCode" => "<iframe src=\"https://mysite.com/h5p/1234/embed\" width=\":w\" height=\":h\" frameborder=\"0\" allowfullscreen=\"allowfullscreen\"></iframe>",
  			"resizeCode" => "<script src=\"https://mysite.com/h5p-resizer.js\" charset=\"UTF-8\"><\/script>",
  			"mainId" => $this->mainLibrary->id
		];
		return json_encode($data);
    }

    public function process( Request $request ) {
    	$tmpName = $request->file('h5pfile')->hashName();
    	$tmpName = substr($tmpName, 0, strlen($tmpName) - strlen('.zip')) . '.h5p';
    	$tmpFolder = '/tmp/' . substr($tmpName, 0, strlen($tmpName) - strlen('.zip'));
    	$path = $request->file('h5pfile')->storeAs('/tmp', $tmpName);

    	$interface = new H5PProvider($this->id);
		$storage = new H5PFileStorageProvider(Storage::disk('public')->getDriver()->getAdapter()->getPathPrefix() . 'h5p');
		$h5p = new H5PCore($interface, $storage, Storage::disk('public')->getDriver()->getAdapter()->getPathPrefix() . 'h5p');
		
		//set getUploadedH5pPath / folder path
		$interface->uploadedPath = Storage::disk('local')->getDriver()->getAdapter()->getPathPrefix() . $path;
		$interface->uploadedFolderPath = Storage::disk('local')->getDriver()->getAdapter()->getPathPrefix() . $tmpFolder;
		
		// Then validate (and extract).
		$validator = new H5PValidator($interface, $h5p);
		if( !$validator->isValidPackage() ) {
			$interface->displayMessages();
			exit();
		}
		// Then:
		$h5pStorage = new H5PStorage($interface, $h5p);
		$h5pStorage->savePackage();
		$content = $h5p->loadContent($h5pStorage->contentId);
    	$filtered = $h5p->filterParameters($content);
    }
}