<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \App\Module;

class ModulesController extends Controller
{
	private $sortables = [
		'title'  => 'title',
		'datemodified'  => 'updated_at',
		'category' => 'category'
	];

    public function index( Request $request) {
		$d = collect(
			['Digitale',
			'De',
			'Digital',
			'Development',
			'Design']
		);
		$l = collect(
			['Leeromgeving',
			'Les',
			'Leer',
			'Learning',
			'Lesson']
		);
		$m = collect(
			['Module',
			'Machine',
			'Maker',
			'Manager',
			'Misson']
		);
		$tagline = $d->random() . ' ' . $l->random() . ' ' . $m->random();
		$order = ($request->input('sortorder') == 'asc') ? 'desc' : 'asc';
		$sortBy = $request->input('sortby');
		
		$sortOrder['title'] = ($sortBy == 'title') ? $order : 'asc';
		$sortOrder['category'] = ($sortBy == 'category') ? $order : 'asc';
		$sortOrder['datemodified'] = ($sortBy == 'datemodified') ? $order : 'asc';

		$sort['title']['url'] = '?sortby=title&sortorder='.$sortOrder['title'];
		$sort['category']['url'] = '?sortby=category&sortorder='.$sortOrder['category'];
		$sort['datemodified']['url'] = '?sortby=datemodified&sortorder='.$sortOrder['datemodified'];

		$sort['title']['order'] = $sortOrder['title'];
		$sort['category']['order'] = $sortOrder['category'];
		$sort['datemodified']['order'] = $sortOrder['datemodified'];

		if($sortBy) {
			$sortByInDb = $this->sortables[$sortBy];
		} else {
			$sortByInDb = 'title';
		}
		$modules_query = Module::orderBy($sortByInDb, $order);
		if(!$request->input('showarchived')) {
			$modules_query->whereNull('archived');
		}
		$modules = $modules_query->get();
		if($request->getQueryString() != '') {
			$archiveurl = $request->fullUrl() . '&showarchived=true';
		} else {
			$archiveurl = $request->fullUrl() . '?showarchived=true';
		}
		return view('welcome', 
			['tagline' => $tagline, 'sort' => $sort, 'modules' => $modules, 'archiveurl' => $archiveurl]);
	}
}