<?php namespace Way\Generators\Laravel;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Way\Generators\Compilers\TemplateCompiler;
use Way\Generators\Filesystem\FileAlreadyExists;
use Way\Generators\ModelGenerator;

class ModelGeneratorCommand extends GeneratorCommand {

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
	protected $description = 'Generate a model';

    /**
     * @var \Way\Generators\ModelGenerator
     */
    private $generator;

    /**
     * @param ModelGenerator $generator
     */
    public function __construct(ModelGenerator $generator)
	{
        $this->generator = $generator;

		parent::__construct();
    }

	/**
	 * Generate the model
	 *
	 * @return mixed
	 */
	public function fire()
	{
        $templateData = $this->getTemplateData();
        $filePathToGenerate = $this->getFileGenerationPath($this->argument('nameOfModel'));

        // Compile and generate
        try
        {
            $this->generator->setTemplatePath($this->option('templatePath'));
            $compiledTemplate = $this->generator->compile($templateData, new TemplateCompiler);
            $this->generator->generate($filePathToGenerate, $compiledTemplate);

            // Alert user of file creation
            $this->info("Created: {$filePathToGenerate}");
        }

        catch (FileAlreadyExists $e)
        {
           return $this->error("The file, {$filePathToGenerate}, already exists! I don't want to overwrite it.");
        }

	}

    /**
     * The path where the file will be created
     *
     * @return mixed
     */
    protected function getFileGenerationPath()
    {
        return $this->option('path') . '/' . ucwords($this->argument('nameOfModel')) . '.php';
    }

    /**
     * Fetch the template data
     *
     * @return array
     */
    protected function getTemplateData()
    {
        return [
            'NAME' => ucwords($this->argument('nameOfModel'))
        ];
    }

	/**
	 * Get the console command arguments.
	 *
	 * @return array
	 */
	protected function getArguments()
	{
		return array(
			array('nameOfModel', InputArgument::REQUIRED, 'The name of the desired Eloquent model')
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
			array('path', null, InputOption::VALUE_OPTIONAL, 'Where should the file be created?', app_path('models')),
            array('templatePath', null, InputOption::VALUE_OPTIONAL, 'What is the path to the template for this generator?', __DIR__.'/../templates/model.txt')
		);
	}

}
