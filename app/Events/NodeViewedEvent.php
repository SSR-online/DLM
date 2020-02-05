<?php

namespace App\Events;

use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use App\Node;

class NodeViewedEvent
{
    use Dispatchable, SerializesModels;

    public $node;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Node $node)
    {
        $this->node = $node;
    }
}
