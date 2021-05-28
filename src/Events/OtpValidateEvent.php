<?php

namespace Erdemkeren\Otp\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OtpValidateEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $type;
    public $request;
    public $user;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($request,$user,$type)
    {
        $this->type = $type;
        $this->request = $request;
        $this->user = $user;
    }
}
