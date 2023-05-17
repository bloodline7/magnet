<?php
namespace Ausumsports\Admin\Events;

use http\Env\Request;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class Personal implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $messege;
    private $channel;
    public $request;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($message, $channel = "")
    {
        $this->messege = $message;
        $this->channel = $channel;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        if ($this->channel)
            return new channel($this->channel);
        else
            return new PrivateChannel('personal.' . auth()->user()->id);
    }

    /**
     * @return string
     */
    public function broadcastAs()
    {
        return 'personal';
    }


    //채널을 수신할때 받을 데이터
    public function broadcastWith()
    {
        return [
            'message' => $this->messege,
            'name' => auth()->user()->name,
            'email' => auth()->user()->email
        ];
    }

}
