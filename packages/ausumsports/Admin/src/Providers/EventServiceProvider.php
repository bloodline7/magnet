<?php
	namespace Ausumsports\Admin\Providers;

	use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
    use Illuminate\Support\Facades\Broadcast;

    /**
     * Do not use Log Method
     * Class EventServiceProvider
     * @package Ausumsports\Admin\Providers
     */
    class EventServiceProvider extends ServiceProvider
	{

		/**
		 * Register any events for your application.
		 *
		 * @return void
		 */
		public function boot() {

			parent::boot();

			Broadcast::routes();
		}
	}
