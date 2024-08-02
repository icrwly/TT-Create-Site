<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TerminusCommandExecuted implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message; // Property to hold the message

    /**
     * Create a new event instance.
     *
     * @param string $message
     */
    public function __construct(string $message)
    {
        $this->message = $message; // Assign the message to the property
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return Channel
     */
    public function broadcastOn(): Channel
    {
        // Return a single Channel instance instead of an array
        return new Channel('pantheon-status'); // Correctly returning a Channel instance
    }
}

