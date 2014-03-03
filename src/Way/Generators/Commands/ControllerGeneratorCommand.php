<?php namespace Way\Generators\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class ControllerGeneratorCommand extends Command {

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
    protected $description = 'Generate a resourceful controller';

    /**
     * Generate the controller
     */
    public function fire()
    {
        // This command is nothing more than a helper,
        // that points directly to Laravel's
        // controller:make command
        $this->call('controller:make', [
            'name' => $this->argument('controllerName'),
            '--path' => $this->option('path')
        ]);
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return array(
            array('controllerName', InputArgument::REQUIRED, 'The name of the desired controller')
        );
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return array(
            array('path', null, InputOption::VALUE_OPTIONAL, 'Where should the file be created?'),
        );
    }

}
