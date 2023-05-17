<?php

	namespace Bloodline7\Util\Events;

	use Illuminate\Broadcasting\Channel;
	use Illuminate\Broadcasting\InteractsWithSockets;
	use Illuminate\Broadcasting\PresenceChannel;
	use Illuminate\Broadcasting\PrivateChannel;
	use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
	use Illuminate\Foundation\Events\Dispatchable;
	use Illuminate\Queue\SerializesModels;
	use Illuminate\Support\Facades\Log;


	class Logger implements ShouldBroadcast
	{
		use Dispatchable, InteractsWithSockets, SerializesModels;
		public $log;

		/**
		 * Create a new event instance.
		 *
		 * @return void
		 */
		public function __construct($log) {
			$this->log = $log;
		}

		/**
		 * Get the channels the event should broadcast on.
		 *
		 * @return \Illuminate\Broadcasting\Channel|array
		 */
		public function broadcastOn()
        {

            if(app()->runningInConsole()) echo $this->log;

            else
			return new Channel('admin');
		}

		/**
		 * @return string
		 */
		public function broadcastAs(): string
        {
			return 'log';
		}

        //채널을 수신할때 받을 데이터
		public function broadcastWith(): array
        {
			return ['log' => $this->log];
		}

	}
