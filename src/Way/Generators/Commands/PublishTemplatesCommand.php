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
     * Execute the command
     */
    public function fire()
    {
        // We'll copy the generator templates
        // to a place where the user can edit
        // them how they wish.
        File::copyDirectory(
            __DIR__.'/../templates',
            $this->option('path')
        );

        // We also will publish the configuration
        $this->call('config:publish', ['package' => 'way/generators']);

        $this->info(
            "The templates have been copied to '{$this->option('path')}'. Modify templates " .
            "however you wish. Don't forget to also update the template paths within " .
            "'app/config/packages/way/generators/config.php'"
        );
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['path', null, InputOption::VALUE_OPTIONAL, 'Which directory should the templates be copied to?', app_path('templates')]
        ];
    }

}
