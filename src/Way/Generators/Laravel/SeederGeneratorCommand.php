<?php namespace Way\Generators\Laravel;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Way\Generators\Compilers\TemplateCompiler;
use Way\Generators\Filesystem\FileAlreadyExists;
use Way\Generators\Generator;

class SeederGeneratorCommand extends GeneratorCommand {

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'generate:seed';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate a database table seeder';

    /**
     * @var \Way\Generators\ModelGenerator
     */
    private $generator;

    /**
     * @param Generator $generator
     */
    public function __construct(Generator $generator)
    {
        $this->generator = $generator;

        parent::__construct();
    }

    /**
     * Generate the seeder class
     *
     * @return mixed
     */
    public function fire()
    {
        $templateData = $this->getTemplateData();
        $filePathToGenerate = $this->getFileGenerationPath($this->argument('tableName'));

        try
        {
            // This section is what actually compiles the template, and generates the file
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
        $tableName = ucwords($this->argument('tableName'));

        return $this->option('path') . "/{$tableName}TableSeeder.php";
    }

    /**
     * Fetch the template data
     *
     * @return array
     */
    protected function getTemplateData()
    {
        $tableName = ucwords($this->argument('tableName'));

        return [
            'CLASS' => "{$tableName}TableSeeder",
            'MODEL' => str_singular($tableName)
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
            array('tableName', InputArgument::REQUIRED, 'The name of the table to seed')
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
            array('path', null, InputOption::VALUE_OPTIONAL, 'Where should the file be created?', app_path('database/seeds')),
            array('templatePath', null, InputOption::VALUE_OPTIONAL, 'What is the path to the template for this generator?', __DIR__.'/../templates/seed.txt')
        );
    }

}
