<?php

namespace App\Events;

use App\Models\ChatMessage;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageSent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public ChatMessage $message)
    {
        //
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        if ($this->message->group_id) {
            // Untuk pesan grup, broadcast ke channel grup
            return [new PrivateChannel('group.' . $this->message->group_id)];
        } else {
            // Untuk pesan personal, broadcast ke channel pribadi penerima
            return [new PrivateChannel('chat.' . $this->message->receiver_id)];
        }
    }

    public function broadcastWith()
    {
        // Sertakan semua data yang diperlukan di frontend
        return [
            "id" => $this->message->id,
            "sender_id" => $this->message->sender_id,
            "receiver_id" => $this->message->receiver_id,
            "group_id" => $this->message->group_id,
            "message" => $this->message->message,
            "created_at" => $this->message->created_at->toDateTimeString(), // Penting untuk tampilan chat
            "sender_name" => $this->message->sender->name, // Sertakan nama pengirim
        ];
    }
}
