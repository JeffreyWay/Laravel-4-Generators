<?php namespace Way\Generators\Commands;

use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Config;

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
     * The path where the file will be created
     *
     * @return mixed
     */
    protected function getFileGenerationPath()
    {
        return $this->option('path') . '/' . ucwords($this->argument('modelName')) . '.php';
    }

    /**
     * Fetch the template data
     *
     * @return array
     */
    protected function getTemplateData()
    {
        return [
            'NAME' => ucwords($this->argument('modelName'))
        ];
    }

    /**
     * Get path to the template for the generator
     *
     * @return mixed
     */
    protected function getTemplatePath()
    {
        return Config::get('generators::config.model_template_path');
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['modelName', InputArgument::REQUIRED, 'The name of the desired Eloquent model']
        ];
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['path', null, InputOption::VALUE_OPTIONAL, 'Where should the file be created?', app_path('models')]
        ];
    }

}
