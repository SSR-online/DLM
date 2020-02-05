<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(App\Module::class, 5)->create()->each(function ($m) {
        	$node = $m->nodes()->save(factory(App\Node::class)->make());
            $block = factory(App\TextBlock::class)->make();
            $block->save();
            $node->block()->associate($block);
            $node->save();
        	$m->nodes()->save(factory(App\Node::class)->make());
        	$m->nodes()->save(factory(App\Node::class)->make());
    });
    }
}
