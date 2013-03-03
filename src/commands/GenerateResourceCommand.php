<?php

namespace Way\Generators\Commands;

use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Illuminate\Support\Pluralizer;

class GenerateResourceCommand extends Generate {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'generate:resource';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Generate a new resource.';

	/**
	 * The type of file generation.
	 *
	 * @var string
	 */
	protected $type = 'resource';

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();
	}

	public function fire()
	{
		$name = ucwords(strtolower($this->argument('fileName')));
		$pluralName = Pluralizer::plural($name);

		// Create the model
		$this->call('generate:model', array('fileName' => $name));


		// Create the controller
		$this->call(
			'controller:make',
			array(
				'name' => $pluralName . 'Controller'
			)
		);


		// Create a test
		$this->call(
			'generate:test',
			array(
				'fileName' => $pluralName . 'ControllerTest',
				'--path' => 'tests/controllers',
				'--controller' => strtolower($pluralName)
			)
		);


		// Create the migration
		$this->call(
			'generate:migration',
			array(
				'fileName' => 'create_' . strtolower($pluralName) . '_table',
				'--fields' => $this->option('fields')
			)
		);

		// Update the routes.php file
		\File::append(
			app_path() . '/routes.php',
			"\n\nRoute::resource('" . strtolower($pluralName) . "', '" . $pluralName . "Controller');"
		);


		// Create the views
		if ( ! \File::exists(app_path() . '/views/' . strtolower($pluralName)) )
		{
			\File::makeDirectory(app_path() . '/views/' . strtolower($pluralName));
		}

		$views = array('index', 'show', 'create', 'edit');
		foreach($views as $view)
		{
			$this->call(
				'generate:view',
				array(
					'fileName' => "{$view}",
					'--path' => 'views/' . strtolower($pluralName)
				)
			);
		}


		// Create the seed file
		$this->call(
			'generate:seed',
			array(
				'tableName' => $pluralName
			)
		);
	}

	protected function applyDataToStub()
	{
		return null;
	}

	/**
	 * Get the console command options.
	 *
	 * @return array
	 */
	protected function getOptions()
	{
		return array(
			array('fields', null, InputOption::VALUE_OPTIONAL, 'Schema fields', null)
		);
	}

	/**
	 * Get the console command arguments.
	 *
	 * @return array
	 */
	protected function getArguments()
	{
		return array(
			array('fileName', InputArgument::REQUIRED, 'Name of the resource.'),
		);
	}

}
