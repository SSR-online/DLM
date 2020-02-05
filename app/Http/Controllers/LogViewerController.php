<?php

namespace App\Http\Controllers;
use Storage;
use App\Module;

class LogViewerController extends Controller 
{
	function show($maxLines = 100, $dateString = null) {
		$file = 'laravel.log';
		if($dateString != null) {
			$date = date('Y-m-d', strtotime($dateString));
			$file = 'laravel-' . $date . '.log';
		}
		$this->authorize('create', Module::class);
		$path = $storagePath  = Storage::disk('local')->getDriver()->getAdapter()->getPathPrefix() . '../logs/' . $file;
		
		$fp = fopen($path, 'r');
		fseek($fp, -1, SEEK_END); 
		$pos = ftell($fp);
		$content = "";
		$lines = 0;
		$lastLine = '';
		// Loop backwards util "\n" is found.
		while($pos > 0 && $lines < $maxLines) {
			while(($c = fgetc($fp)) != "\n") {
			    $lastLine = $c.$lastLine;
			    if($pos > 0) {
			    	fseek($fp, $pos--);	
			    }
			}
			$lines++;
		}
		$response = response($lastLine);
        $response->header('Content-Type', 'text/plain');
        $response->header('Content-Disposition', 'attachment; filename="log.log"');
        return $response;
	}
}