<?php namespace Way\Generators\Commands;

use Illuminate\Console\Command;
use Way\Generators\Filesystem\FileAlreadyExists;
use Way\Generators\Generator;

abstract class GeneratorCommand extends Command {

    /**
     * @var \Way\Generators\ModelGenerator
     */
    protected $generator;

    /**
     * @param Generator $generator
     */
    public function __construct(Generator $generator)
    {
        $this->generator = $generator;

        parent::__construct();
    }

    /**
     * Fetch the template data
     *
     * @return array
     */
    protected abstract function getTemplateData();

    /**
     * The path where the file will be created
     *
     * @return mixed
     */
    protected abstract function getFileGenerationPath();

    /**
     * Compile and generate the file
     */
    public function fire()
    {
        $filePathToGenerate = $this->getFileGenerationPath();

        try
        {
            $this->generator->make(
                $this->option('templatePath'),
                $this->getTemplateData(),
                $filePathToGenerate
            );

            $this->info("Created: {$filePathToGenerate}");
        }

        catch (FileAlreadyExists $e)
        {
            $this->error("The file, {$filePathToGenerate}, already exists! I don't want to overwrite it.");
        }
    }

} 