<?php namespace Way\Generators\Commands;

use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

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
        $path = $this->getValueByOptionOrConfig('path', 'model_target_path');

        return $path. '/' . ucwords($this->argument('modelName')) . '.php';
    }

    /**
     * Fetch the template data
     *
     * @return array
     */
    protected function getTemplateData()
    {
        $optionParentClass = $this->getValueByOptionOrConfig('parentClass', 'model_parent_class');
        $optionTable = $this->option('table');

        $tableLine = "\n";
        if (!empty($optionTable))
        {
            $tableLine = sprintf("protected \$table = '%s';\n\n", $optionTable);
        }

        return [
            'NAME' => ucwords($this->argument('modelName')),
            'PARENT_CLASS' => $optionParentClass,
            'TABLE' => $optionTable,
            'TABLE_LINE' => $tableLine,
        ];
    }

    /**
     * Get path to the template for the generator
     *
     * @return mixed
     */
    protected function getTemplatePath()
    {
        return $this->getValueByOptionOrConfig('templatePath', 'model_template_path');
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
        $options = parent::getOptions();

        $options[] = ['parentClass', 'parent-class', InputOption::VALUE_REQUIRED, 'The class that model extends from'];
        $options[] = ['table', null, InputOption::VALUE_REQUIRED, 'The name of model\'s table'];

        return $options;
    }
}
