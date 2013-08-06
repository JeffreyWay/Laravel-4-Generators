<?php namespace Way\Generators\Commands;

use Way\Generators\Generators\ResourceGenerator;
use Way\Generators\Cache;
use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Illuminate\Support\Pluralizer;

class MissingTableFieldsException extends \Exception {}

class ScaffoldGeneratorCommand extends ResourceGeneratorCommand {

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'generate:scaffold';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate scaffolding for a resource.';

    /**
     * Get the path to the template for a model.
     *
     * @return string
     */
    protected function getModelTemplatePath()
    {
        return __DIR__.'/../Generators/templates/scaffold/model.txt';
    }

    /**
     * Get the path to the template for a controller.
     *
     * @return string
     */
    protected function getControllerTemplatePath()
    {
        return __DIR__.'/../Generators/templates/scaffold/controller.txt';
    }


    /**
     * Get the path to the template for a controller.
     *
     * @return string
     */
    protected function getTestTemplatePath()
    {
        return __DIR__.'/../Generators/templates/scaffold/controller-test.txt';
    }

    /**
     * Get the path to the template for a view.
     *
     * @return string
     */
    protected function getViewTemplatePath($view = 'view')
    {
        return __DIR__."/../Generators/templates/scaffold/views/{$view}.txt";
    }

    public function generateMisc()
    {
        $this->generateTest();
    }

    /**
     * Call generate:test
     *
     * @return void
     */
    protected function generateTest()
    {
        if ( ! file_exists(app_path() . '/tests/controllers'))
        {
            mkdir(app_path() . '/tests/controllers');
        }

        $this->call(
            'generate:test',
            array(
                'name' => Pluralizer::plural(strtolower($this->model)) . 'Test',
                '--template' => $this->getTestTemplatePath(),
                '--path' => app_path() . '/tests/controllers'
            )
        );
    }
    public function generateViews()
    {
        parent::generateViews();
        $layouts = app_path() . '/views/layouts';

        $this->generator->folders($layouts);

        $this->generateView('scaffold', $layouts);
    }
}