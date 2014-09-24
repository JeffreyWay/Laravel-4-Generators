<?php namespace Way\Generators\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use File, Config;

class PublishTemplatesCommand extends Command {

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'generate:publish-templates';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Copy generator templates for user modification';
    
    /**
     * Stashed laravel 5 check
     * 
     * @var bool
     */
    protected $is5 = null;

    /**
     * Execute the command
     */
    public function fire()
    {
        $this->copyTemplatesDirectoryForEditing();

        // We also will publish the configuration
        if ($this->is5())
        {
            $this->call('publish:config', ['package' => 'way/generators']);
        }
        else
        {
            $this->call('config:publish', ['package' => 'way/generators']);
        }

        $this->pointConfigFileTemplatesToNewLocation();

        $this->info(
            "The templates have been copied to '{$this->option('path')}'. " .
            "Modify these templates however you wish, and they'll be referenced " .
            "when you execute the associated generator command."
        );
    }

    /**
     * Copy the default templates, so that the user
     * may modify them how they wish.
     */
    protected function copyTemplatesDirectoryForEditing()
    {
        // We'll copy the generator templates
        // to a place where the user can edit
        // them how they wish.
        File::copyDirectory(
            __DIR__.'/../templates',
            $this->option('path')
        );
    }

    /**
     * Update config file to point to the new templates directory
     */
    protected function pointConfigFileTemplatesToNewLocation()
    {
        $configPath = $this->is5() ? base_path('config/packages/way/generators/config.php') : app_path('config/packages/way/generators/config.php');
        $updated = str_replace('vendor/way/generators/src/Way/Generators/templates', $this->option('path'), File::get($configPath));

        File::put($configPath, $updated);
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['path', null, InputOption::VALUE_OPTIONAL, 'Which directory should the templates be copied to?', 'app/templates']
        ];
    }
    
    /**
     * Check laravel version
     * 
     * @return bool
     */
    protected function is5()
    {
        if ( ! isset($this->is5))
        {
            $laravel = get_class($this->laravel);
            $this->is5 = strpos($laravel::VERSION, '5') === 0;
        }

    }

}
