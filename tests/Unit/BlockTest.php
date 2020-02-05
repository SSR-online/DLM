<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class BlockTest extends TestCase
{
	use DatabaseMigrations;
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testQuestionBlock()
    {
        $module = new \App\Module();
    	$module->title = 'Testmodule';
    	$module->save();

        $node = new \App\Node();
        $node->title = 'Testnode';
        $node->module()->associate($module);
        $node->save();

        $block = new \App\QuestionBlock();
        $block->question_type = 'mc';
        $block->save();
        $node->block()->associate($block);
        $node->save();

        $this->assertDatabaseHas('nodes', ['block_type' => 'App\QuestionBlock', 'block_id' => 1]);

    	$response = $this->get('/module/' . $module->id . '/' . $node->id);
        $response->assertStatus(200);
        $response->assertViewHas('node');
    }

    public function testAddAnswerOption() {
        $module = new \App\Module();
        $module->title = 'Testmodule';
        $module->save();

        $node = new \App\Node();
        $node->title = 'Testnode';
        $node->module()->associate($module);
        $node->save();

        $block = new \App\QuestionBlock();
        $block->save();
    }
}