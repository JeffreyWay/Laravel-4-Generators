<?php namespace Way\Generators\Commands;

use Way\Generators\Generators\ModelGenerator;
use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Way\Generators\NameParser;

class ModelGeneratorCommand extends BaseGeneratorCommand {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'generate:model';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Generate a new model.';

	/**
	 * Model generator instance.
	 *
	 * @var Way\Generators\Generators\ModelGenerator
	 */
	protected $generator;

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct(ModelGenerator $generator)
	{
		parent::__construct();

		$this->generator = $generator;
	}

    /**
     * Get the path to the file that should be generated.
     *
     * @param NameParser $nameparser
     * @return string
     */
    protected function getPath(NameParser $nameparser)
    {
        return $this->option('path') . '/' . $nameparser->get('dirname') . '/' . $nameparser->get('model') . '.php';
    }

	/**
	 * Get the console command arguments.
	 *
	 * @return array
	 */
	protected function getArguments()
	{
		return array(
			array('name', InputArgument::REQUIRED, 'Name of the model to generate.'),
		);
	}

	/**
	 * Get the console command options.
	 *
	 * @return array
	 */
	protected function getOptions()
	{
		return array(
			array('path', null, InputOption::VALUE_OPTIONAL, 'Path to the models directory.', app_path() . '/models'),
			array('template', null, InputOption::VALUE_OPTIONAL, 'Path to template.', __DIR__.'/../Generators/templates/model.txt')
		);
	}

}
