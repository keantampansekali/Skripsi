<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class StokRendah implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $item;
    public $tipe; // 'bahan_baku' atau 'produk'
    public $stokLama;
    public $stokBaru;
    public $idCabang;

    /**
     * Create a new event instance.
     */
    public function __construct($item, string $tipe, int $stokLama, int $stokBaru, int $idCabang)
    {
        $this->item = $item;
        $this->tipe = $tipe;
        $this->stokLama = $stokLama;
        $this->stokBaru = $stokBaru;
        $this->idCabang = $idCabang;
    }

    /**
     * Get the channels the event should broadcast on.
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('cabang.' . $this->idCabang),
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'stok.rendah';
    }
}

