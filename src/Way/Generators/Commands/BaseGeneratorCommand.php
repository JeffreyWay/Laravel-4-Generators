<?php namespace Way\Generators\Commands;

use Doctrine\Tests\DBAL\Functional\Ticket\NamedParametersTest;
use Way\Generators\Generators\Generator;
use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Way\Generators\NameParser;

class BaseGeneratorCommand extends Command {

    /**
     * @var \Way\Generators\Generators\Generator
     */
    protected $generator;
    /**
     * Execute the console command.
     *
     * @return void
     */
    public function fire()
    {
        $nameparser = new NameParser($this->argument('name'));
        $path = $this->getPath($nameparser);
        $template = $this->option('template');

        $this->printResult($this->generator->make($path, $template, $nameparser), $path);
    }

    /**
     * Provide user feedback, based on success or not.
     *
     * @param  boolean $successful
     * @param  string $path
     * @return void
     */
    protected function printResult($successful, $path)
    {
        if ($successful)
        {
            return $this->info("Created {$path}");
        }

        $this->error("Could not create {$path}");
    }
    /**
     * Get the path to the file that should be generated.
     *
     * @param NameParser $nameparser
     * @return string
     */
    protected function getPath(NameParser $nameparser)
    {
        return $this->option('path') . '/' . $nameparser->get('dirname') . '/' . strtolower($nameparser->get('basename')) . '.blade.php';
    }

}