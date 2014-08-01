<?php namespace Way\Generators\Commands;

use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class ControllerGeneratorCommand extends GeneratorCommand {

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'generate:controller';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate a controller';

    /**
     * The path where the file will be created
     *
     * @return mixed
     */
    protected function getFileGenerationPath()
    {
        $path = $this->getPathByOptionOrConfig('path', 'controller_target_path');

        return $path. '/' . $this->argument('controllerName') . '.php';
    }

    /**
     * Fetch the template data
     *
     * @return array
     */
    protected function getTemplateData()
    {
        // LessonsController
        $name = ucwords($this->argument('controllerName'));

        // lessons
        $collection = strtolower(str_replace('Controller', '', $name));

        // lesson
        $resource = str_singular($collection);

        // Lesson
        $model = ucwords($resource);

        // model namespace
        $modelNamespace = $this->option('model-namespace');
        $modelNamespace = $modelNamespace?"use $modelNamespace\\$model;\n":'';

        // namespace
        $namespace = ucwords($this->option('namespace'));
        $namespace = $namespace?"namespace $namespace;":'';

        // view namespace
        $viewNamespace = $this->option('view-namespace');
        $viewNamespace .= $viewNamespace?'::':'';

        return compact('name', 'collection', 'resource', 'model', 'namespace', 'modelNamespace', 'viewNamespace');
    }

    /**
     * Get path to the template for the generator
     *
     * @return mixed
     */
    protected function getTemplatePath()
    {
        return $this->getPathByOptionOrConfig('templatePath', 'controller_template_path');
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['controllerName', InputArgument::REQUIRED, 'The name of the desired controller.']
        ];
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return array_merge(parent::getOptions(), [
            ['namespace', null, InputOption::VALUE_REQUIRED, 'The namespace for this class'],
            ['model-namespace', null, InputOption::VALUE_REQUIRED, 'The namespace for the controller\'s model'],
            ['view-namespace', null, InputOption::VALUE_REQUIRED, 'The namespace for controller\'s views'],
        ]);
    }

}
