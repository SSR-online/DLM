<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class NodeTest extends TestCase
{
	use DatabaseMigrations;
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testCreateNode()
    {
    	
    	$module = new \App\Module();
    	$module->title = 'Testmodule';
    	$module->save();

        $node = new \App\Node();
        $node->title = 'Testnode';
        $node->module()->associate($module);
        $node->save();


    	$response = $this->get('/module/' . $module->id . '/' . $node->id);
        $response->assertStatus(200);

        $this->assertDatabaseHas('nodes', ['title'=>'Testnode', 'module_id' => $module->id]);
    }
}