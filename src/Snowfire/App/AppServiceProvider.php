<?php namespace Snowfire\App;

use Illuminate\Support\ServiceProvider;
use Config;
use Illuminate\Foundation\AliasLoader;

class AppServiceProvider extends ServiceProvider {

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
		include __DIR__ . '/../../routes.php';

		$this->publishes([
			__DIR__ . '/../../config/snowfire_app.php' => config_path('snowfire_app.php'),
		]);
	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->mergeConfigFrom(
			__DIR__ . '/../../config/snowfire_app.php', 'snowfire_app'
		);

		$this->app['snowfireApp'] = $this->app->share(function($app)
		{
			$defaultConfig = [
				'acceptUrl' => route('snowfireApp.accept'),
				'uninstallUrl' => route('snowfireApp.uninstall'),
				'tabUrl' => route('snowfireApp.tab'),
				'actions' => [],
			];

			$config = array_merge(
				$defaultConfig,
				Config::get('snowfire_app')
			);

			return new SnowfireApp($config);
		});

		AliasLoader::getInstance()->alias('Snowfire', 'Snowfire\App\Facades\SnowfireApp');
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return array('snowfireApp');
	}

}
