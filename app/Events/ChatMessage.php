<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ChatMessage implements ShouldBroadcast{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $chat_room;
    public $sender;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($chat_room, $sender){
        $this->chat_room = $chat_room;
        $this->sender = $sender;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn(){
        return 'chat-channel';
    }

    public function broadcastAs(){
        return 'chat-event';
    }
}
