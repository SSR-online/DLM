<?php

namespace App\Events;

use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use App\Node;
use App\QuizAttempt;

class QuizCompletedEvent
{
    use Dispatchable, SerializesModels;

    public $node;
    public $attempt;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct( Node $node, QuizAttempt $attempt)
    {
        $this->node = $node;
        $this->attempt = $attempt;
    }
}
