<?php

namespace Way\Generators\Commands;

use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class GenerateTestCommand extends Generate {

  /**
   * The console command name.
   *
   * @var string
   */
  protected $name = 'generate:test';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Generate a PHPUnit Test.';

  /**
   * The type of file generation.
   * 
   * @var string
   */
  protected $type = 'test';

  /**
   * Compile a template or return a string
   * that should be inserted into the generated file.
   * 
   * @return string
   */
  protected function applyDataToStub()
  {
    if ( !! $this->option('controller') )
    {
      $stub = preg_replace('/{{resource}}/', $this->option('controller'), $this->getStub('controllerTest'));
      return str_replace('{{name}}', $this->argument('fileName'), $stub);
    }

    return str_replace('{{name}}', $this->argument('fileName'), $this->getStub());
  }

  /**
   * Get the console command arguments.
   *
   * @return array
   */
  protected function getArguments()
  {
    return array(
      array('fileName', InputArgument::REQUIRED, 'Name of the test file to generate.'),
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
      array('path', null, InputOption::VALUE_OPTIONAL, 'Path to where the test file should be created.', 'tests'),
      array('controller', null, InputOption::VALUE_OPTIONAL, 'Specifies whether controller tests should be generated and populated.')
    );
  }

  

}