<?php

namespace Way\Generators;

use Illuminate\Support\ServiceProvider;

class GeneratorsServiceProvider extends ServiceProvider {

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
		$this->package('way/generators');
	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->app['generate.test'] = $this->app->share(function($app)
		{
			return new GenerateTestCommand($app);
		});

		$this->app['generate.model'] = $this->app->share(function($app)
		{
			return new GenerateModelCommand($app);
		});

		$this->app['generate.view'] = $this->app->share(function($app)
		{
			return new GenerateViewCommand($app);
		});

		$this->app['generate.migration'] = $this->app->share(function($app)
		{
			return new GenerateMigrationCommand($app);
		});

		$this->app['generate.resource'] = $this->app->share(function($app)
		{
			return new GenerateResourceCommand($app);
		});

		$this->app['generate.seed'] = $this->app->share(function($app)
		{
			return new GenerateSeedCommand($app);
		});

		$this->commands(
			'generate.test',
			'generate.view',
			'generate.migration',
			'generate.seed',
			'generate.resource',
			'generate.model'
		);
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return array();
	}

}