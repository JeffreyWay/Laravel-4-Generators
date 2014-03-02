<?php namespace Way\Generators\Laravel;

use Illuminate\Console\Command;

abstract class GeneratorCommand extends Command {

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

} 