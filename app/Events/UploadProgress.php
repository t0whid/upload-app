<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UploadProgress implements ShouldBroadcast
{
    use Dispatchable, SerializesModels;

    public $phase;      // "laravel->1fichier"
    public $percent;    // 0..100
    public $bytes;      // bytes uploaded
    public $speed;      // MB/s
    public $message;    // optional

    public function __construct($phase, $percent, $bytes, $speed, $message = '')
    {
        $this->phase = $phase;
        $this->percent = $percent;
        $this->bytes = $bytes;
        $this->speed = $speed;
        $this->message = $message;
    }

    public function broadcastOn()
    {
        return new Channel('upload-progress');
    }

    public function broadcastWith()
    {
        return [
            'phase' => $this->phase,
            'percent' => $this->percent,
            'bytes' => $this->bytes,
            'speed' => $this->speed,
            'message' => $this->message,
        ];
    }

    public function broadcastAs()
    {
        return 'progress-updated';
    }
}
