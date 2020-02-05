<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', 'ModulesController@index');

// Route::get('/h5p', 'H5PController@index');
// 
// Route::get('/lti', 'LTIController@postLogin');
// Route::post('/lti', 'LTIController@postLogin');
// 

Route::post('/lti/', 'LTIController@postLogin');
Route::post('/lti/{module}', 'LTIController@postLogin');
Route::get('/lti/contentitem/{module}', 'LTIController@returnContentItem');
Route::post('/lti/contentitem/{module}', 'LTIController@returnContentItem');

Route::middleware('auth')->group(function() {
	Route::get('/settings/{block?}', 'SettingController@list');
	Route::post('/setting/edit/', 'SettingController@save');
	Route::get('/setting/create', 'SettingController@create');
	Route::post('/setting/create', 'SettingController@save');
	Route::post('/setting/update/{block}', 'SettingController@saveBlock');

	//Log route
	Route::get('/log/view/{lines?}/{date?}', 'LogViewerController@show');
	// LTI route
	Route::get('/lti/consumers', 'LTIController@getConsumers');
	Route::post('/lti/consumer/save', 'LTIController@postConsumer');
	Route::get('/lti/consumer/{consumerId?}', 'LTIController@getConsumer');
	Route::get('/lti/outcome/{grade}', 'LTIController@postOutcome');

	// Module routes
	Route::post('/module/{module}/toggleediting', 'ModuleController@toggleEditing');
	Route::post('/module/{module}/stopmoving', 'ModuleController@stopMoving');
	Route::post('/module/{module}/sortnodes/{parent?}', 'ModuleController@sortNodes');
	Route::post('/module/{module}/archive', 'ModuleController@archive');

	Route::get('/module/edit/{module}', 'ModuleController@edit');
	Route::post('/module/edit/{module}', 'ModuleController@save');
	Route::get('/module/create', 'ModuleController@create');
	Route::post('/module/create', 'ModuleController@save');
	Route::get('/module/import', 'ModuleImporter@import');
	Route::post('/module/import', 'ModuleImporter@processImport');
	Route::get('/module/{module}', 'ModuleController@show');
	Route::get('/module/{module}/delete', 'ModuleController@confirmDelete');
	Route::post('/module/{module}/delete', 'ModuleController@delete');
	Route::get('/module/{module}/export', 'ModuleController@export');

	// Node routes
	Route::get('/module/{module}/page/create/', 'NodeController@createAsPage');
	Route::get('/module/{module}/page/create/{node}/', 'NodeController@createAsPage');
	Route::get('/module/{module}/section/create/', 'NodeController@create');
	Route::get('/module/{module}/node/{node}/section/create', 'NodeController@create');
	Route::get('/module/{module}/node/{node}/section/create', 'NodeController@create');
	Route::get('/node/{node}/slot/{layoutslot}/create', 'NodeController@createInSlot');

	Route::get('/module/{module}/{node}', 'NodeController@show');
	Route::get('/node/edit/{node}', 'NodeController@edit');
	Route::post('/node/edit/{node}', 'NodeController@save');
	Route::post('/node/create', 'NodeController@save');
	Route::get('/node/{node}/delete', 'NodeController@confirmDelete');
	Route::post('/node/{node}/delete', 'NodeController@delete');
	Route::get('/node/{node}', 'NodeController@show');
	Route::get('/node/{node}/duplicate', 'NodeController@duplicate');
	Route::get('/node/{node}/jump/{jump_node}/delete', 'NodeController@getDeleteJump');
	Route::get('/node/{node}/jump/delete', 'NodeController@getDeleteJump');
	Route::post('/node/{node}/jump/{jump_node}/delete', 'NodeController@postDeleteJump');
	Route::get('/node/{node}/jump/delete', 'NodeController@postDeleteJump');
	

	Route::get('/node/{node}/move/targetnode/{targetnode?}', 'NodeController@moveToTargetNode'); //TODO make this a post, and allow front?back
	Route::get('/node/{node}/move/targetslot/{targetslot}/{position?}', 'NodeController@moveToTargetSlot'); //TODO make this a post
	Route::get('/node/{node}/move/', 'NodeController@startMove');
	Route::get('/node/{node}/stopmove/', 'NodeController@stopMove');

	// Layout routes
	Route::get('/node/{node}/layout/create', 'LayoutController@create');
	Route::post('/node/{node}/layout/create', 'LayoutController@postCreate');
	Route::get('/layout/{layout}/edit', 'LayoutController@edit');
	Route::post('/layout/{layout}/edit', 'LayoutController@postEdit');
	Route::get('/layout/{layout}/delete', 'LayoutController@confirmDelete');
	Route::post('/layout/{layout}/delete', 'LayoutController@delete');

	// Layout slot routes
	Route::post('/slot/{slot}/sort', 'SlotController@sort');

	// Block routes
	Route::get('/module/{module}/node/{node}/block/select', 'BlockController@select');
	Route::get('/node/{node}/slot/{layoutslot}/block/select', 'BlockController@selectWithSlot');
	Route::get('/layout/{layout}/block/select', 'BlockController@selectFromLayout');
	Route::get('/node/{node}/block/create/{type}', 'BlockController@create');
	Route::get('/node/{node}/slot/{layoutslot}/block/create/{type}', 'BlockController@createWithSlot');

	// Quiz routes
	Route::post('/quiz/attempt/{node}', 'QuizAttemptController@postAttempt');
	Route::get('/quiz/{node}/submissions', 'QuizSubmissionsController@show');
	Route::get('/quiz/{node}/submissions/aggregate', 'QuizSubmissionsController@aggregate');
	Route::get('/quiz/{node}/submissions/{attempt}', 'QuizSubmissionsController@single');

	// Question routes
	Route::post('/question/attempt/{questionid}', 'QuestionAttemptController@postAttempt');
	Route::post('/question/attempt/{questionid}/delete', 'QuestionAttemptController@deleteQuestionAttempt');
	Route::get('/answeroption/{answerOption}/delete', 'AnswerOptionController@confirmDelete');
	Route::post('/answeroption/{answerOption}/delete', 'AnswerOptionController@delete');

	// Discussion routes
	Route::post('/discussion/{node}/post', 'DiscussionController@add');
	Route::get('/discussion/{node}/messages/{since?}', 'DiscussionController@messages');
	Route::post('/discussion/{node}/typing', 'DiscussionController@setIsTyping');

	// File routes
	Route::get('/module/{module}/node/{node}/download', 'FileController@download');
});

Auth::routes();