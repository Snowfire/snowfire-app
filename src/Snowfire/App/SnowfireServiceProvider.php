<?php namespace Snowfire\App;

use Illuminate\Support\ServiceProvider;
use Config;
use Illuminate\Foundation\AliasLoader;

class SnowfireServiceProvider extends ServiceProvider {

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
			__DIR__ . '/../../config/snowfire.php' => config_path('snowfire.php'),
		]);

		$this->publishes([
		    __DIR__.'/../../migrations/' => base_path('/database/migrations')
		], 'migrations');
	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->mergeConfigFrom(
			__DIR__ . '/../../config/snowfire.php', 'snowfire'
		);

		$this->app['snowfire'] = $this->app->share(function($app)
		{
			$defaultConfig = [
				'acceptUrl' => route('snowfire.accept'),
				'uninstallUrl' => route('snowfire.uninstall'),
				'tabUrl' => route('snowfire.tab'),
				'actions' => [],
			];

			$config = array_merge(
				$defaultConfig,
				Config::get('snowfire')
			);

			return new Snowfire($config);
		});

		AliasLoader::getInstance()->alias('Snowfire', 'Snowfire\App\Facades\Snowfire');
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return array('snowfire');
	}

}
