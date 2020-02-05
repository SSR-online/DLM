<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class ModuleTest extends TestCase
{
	use DatabaseMigrations;
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testCreateModule()
    {
    	$module = new \App\Module();
    	$module->title = 'Testmodule';
    	$module->save();

    	//
    	$response = $this->get('/module/1');
        $response->assertStatus(200);

        $this->assertDatabaseHas('modules', ['title'=>'Testmodule']);
    }
}
