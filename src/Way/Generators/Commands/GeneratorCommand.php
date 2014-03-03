<?php namespace Way\Generators\Commands;

use Illuminate\Console\Command;
use Way\Generators\Compilers\TemplateCompiler;
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
        $templateData = $this->getTemplateData();
        $filePathToGenerate = $this->getFileGenerationPath();

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

} 