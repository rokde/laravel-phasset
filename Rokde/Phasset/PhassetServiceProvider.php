<?php namespace Rokde\Phasset;

use Illuminate\Support\ServiceProvider;
use Rokde\Phasset\Commands\NotifyCommand;
use Rokde\Phasset\Commands\UpdateCommand;
use Rokde\Phasset\Commands\WatchCommand;

class PhassetServiceProvider extends ServiceProvider {

	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = false;

	/**
	 * Bootstrap the application events.
	 *
	 * @return void
	 */
	public function boot()
	{
		$this->package('rokde/phasset');
	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->registerNotifyCommand();
		$this->registerWatchCommand();
		$this->registerUpdateCommand();
	}

	/**
	 * Register the notifier
	 */
	protected function registerNotifyCommand()
	{
		$this->app['phasset.notify'] = $this->app->share(function($app)
		{
			return new NotifyCommand();
		});

		$this->commands('phasset.notify');
	}

	/**
	 * Register the watcher
	 */
	protected function registerWatchCommand()
	{
		$this->app['phasset.watch'] = $this->app->share(function($app)
		{
			return new WatchCommand();
		});

		$this->commands('phasset.watch');
	}

	/**
	 * Register the updater
	 */
	private function registerUpdateCommand()
	{
		$this->app['phasset.update'] = $this->app->share(function($app)
		{
			return new UpdateCommand();
		});

		$this->commands('phasset.update');
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return array(
			'phasset.notify',
			'phasset.watch',
			'phasset.update',
		);
	}
}
