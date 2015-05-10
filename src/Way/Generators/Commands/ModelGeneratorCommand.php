<?php namespace Way\Generators\Commands;

use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Way\Generators\Generator;
use Way\Generators\Parsers\MigrationFieldsParser;

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
     * @param Generator $generator
     * @param MigrationFieldsParser $migrationFieldsParser
     */
    public function __construct(
        Generator $generator,
        MigrationFieldsParser $migrationFieldsParser
    )
    {
        $this->migrationFieldsParser = $migrationFieldsParser;
        parent::__construct($generator);
    }
    
    /**
     * The path where the file will be created
     *
     * @return mixed
     */
    protected function getFileGenerationPath()
    {
        $path = $this->getPathByOptionOrConfig('path', 'model_target_path');

        return $path. '/' . ucwords($this->argument('modelName')) . '.php';
    }

    /**
     * Fetch the template data
     *
     * @return array
     */
    protected function getTemplateData()
    {
        $fields = $this->migrationFieldsParser->parse($this->option('fillable'));
        $padvalue = function($s){ return "'{$s['field']}'"; };
        $paddedFields = implode(', ', array_map($padvalue, $fields));
        return [
            'NAME' => ucwords($this->argument('modelName')),
            'FILLABLE' => $paddedFields
        ];
    }

    /**
     * Get path to the template for the generator
     *
     * @return mixed
     */
    protected function getTemplatePath()
    {
        return $this->getPathByOptionOrConfig('templatePath', 'model_template_path');
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['modelName', InputArgument::REQUIRED, 'The name of the desired Eloquent model'],
        ];
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return array(
            ['fillable', null, InputOption::VALUE_OPTIONAL, 'Fillable attributes for the model'],
            ['path', null, InputOption::VALUE_OPTIONAL, 'Where should the file be created?'],
            ['templatePath', null, InputOption::VALUE_OPTIONAL, 'The location of the template for this generator'],
            ['testing', null, InputOption::VALUE_OPTIONAL, 'For internal use only.']
        );
    }
}
