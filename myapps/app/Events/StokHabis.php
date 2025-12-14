<?php

namespace App\Events;

use App\Models\BahanBaku;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class StokHabis implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $bahanBaku;

    /**
     * Create a new event instance.
     */
    public function __construct(BahanBaku $bahanBaku)
    {
        $this->bahanBaku = $bahanBaku;
    }

    /**
     * Get the channels the event should broadcast on.
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('cabang.' . $this->bahanBaku->id_cabang),
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'stok.habis';
    }
}

