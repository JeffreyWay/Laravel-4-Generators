<?php namespace Way\Generators\Commands;

use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class ModelGeneratorCommand extends GeneratorCommand
{

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
        $path = $this->getNamespacePath() ? : $this->getPathByOptionOrConfig('path', 'model_target_path');
        $this->createFolderForModel($path);
        return $path . '/' . ucwords($this->argument('modelName')) . '.php';
    }

    /**
     * Fetch the template data
     *
     * @return array
     */
    protected function getTemplateData()
    {
        return [
            'NAME' => ucwords($this->argument('modelName')),
            'NAMESPACE' => $this->getNamespaceParam()
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
            ['modelNamespace', InputArgument::OPTIONAL, 'The namespace of the desired Eloquent model'],
        ];
    }

    /**
     * Create namespace param for template
     *
     * @return string
     */
    protected function getNamespaceParam()
    {
        if (!empty($this->argument('modelNamespace'))) {
            $namespace = 'namespace ' . $this->argument('modelNamespace') . ';';
            return $namespace;
        }
        return '';
    }

    /**
     * Get path from namespace starting with be app_path
     *
     * @return string
     */
    protected function getNamespacePath()
    {
        $namespace = $this->argument('modelNamespace');
        if (!empty($namespace)) {
            return app_path() . '/' . str_replace('\\', '/', $namespace);
        }
        return '';
    }

    /**
     * Check if path where we save model exists if not, create path folders
     *
     * @param string $path
     *
     * @return bool
     */
    protected function createFolderForModel($path)
    {
        if (realpath($path) === false) {
            return mkdir($path, 0777, true);
        }
        return true;
    }

}
