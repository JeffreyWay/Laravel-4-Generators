<?php namespace Way\Generators\Commands;

use Way\Generators\Generators\MigrationGenerator;
use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Way\Generators\NameParser;

class MigrationGeneratorCommand extends BaseGeneratorCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'generate:migration';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate a new migration.';

    /**
     * Model generator instance.
     *
     * @var Way\Generators\Generators\MigrationGenerator
     */
    protected $generator;

    /**
     * Create a new command instance.
     *
     * @param MigrationGenerator $generator
     * @return void
     */
    public function __construct(MigrationGenerator $generator)
    {
        parent::__construct();

        $this->generator = $generator;
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function fire()
    {
        $nameparser = new NameParser($this->argument('name'));
        $path = $this->getPath($nameparser);
        $fields = $this->option('fields');

        $created = $this->generator
                        ->parse($nameparser->get('basename'), $fields)
                        ->make($path, null, $nameparser);

        $this->call('dump-autoload');

        $this->printResult($created, $path);
    }
    /**
     * Get the path to the file that should be generated.
     *
     * @param NameParser $nameparser
     * @return string
     */
    protected function getPath(NameParser $nameparser)
    {
        return $this->option('path') . '/' . ucfirst($nameparser->get('basename')) . '.php';
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return array(
            array('name', InputArgument::REQUIRED, 'Name of the migration to generate.'),
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
            array('path', null, InputOption::VALUE_OPTIONAL, 'The path to the migrations folder', app_path() . '/database/migrations'),
            array('fields', null, InputOption::VALUE_OPTIONAL, 'Table fields', null)
        );
    }

}
