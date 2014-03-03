<?php namespace Way\Generators;

use Illuminate\Support\ServiceProvider;
use Way\Generators\Commands\ControllerGeneratorCommand;
use Way\Generators\Commands\ModelGeneratorCommand;
use Way\Generators\Commands\ResourceGeneratorCommand;
use Way\Generators\Commands\SeederGeneratorCommand;

class GeneratorsServiceProvider extends ServiceProvider {

	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = false;

	/**
	 * Register the commands
	 *
	 * @return void
	 */
	public function register()
	{
        foreach(['Model', 'Controller', 'Migration', 'Seeder', 'Resource'] as $command)
        {
            $this->{"register$command"}();
        }
	}

    /**
     * Register the model generator
     */
    protected function registerModel()
    {
        $this->app['generate.model'] = $this->app->share(function($app)
        {
            $generator = $this->app->make('Way\Generators\Generator');

            return new ModelGeneratorCommand($generator);
        });

        $this->commands('generate.model');
    }

    /**
     * Register the controller generator
     */
    protected function registerController()
    {
        $this->app['generate.controller'] = $this->app->share(function($app)
        {
            $generator = $this->app->make('Way\Generators\Generator');

            return new ControllerGeneratorCommand($generator);
        });

        $this->commands('generate.controller');
    }

    /**
     * Register the migration generator
     */
    protected function registerMigration()
    {
        $this->app['generate.migration'] = $this->app->share(function($app)
        {
            return $this->app->make('Way\Generators\Commands\MigrationGeneratorCommand');
        });

        $this->commands('generate.migration');
    }

    /**
     * Register the seeder generator
     */
    protected function registerSeeder()
    {
        $this->app['generate.seeder'] = $this->app->share(function($app)
        {
            $generator = $this->app->make('Way\Generators\Generator');

            return new SeederGeneratorCommand($generator);
        });

        $this->commands('generate.seeder');
    }

    /**
     * Register the resource generator
     */
    protected function registerResource()
    {
        $this->app['generate.resource'] = $this->app->share(function($app)
        {
            $generator = $this->app->make('Way\Generators\Generator');

            return new ResourceGeneratorCommand($generator);
        });

        $this->commands('generate.resource');
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
