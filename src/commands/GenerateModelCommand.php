<?php

namespace Way\Generators\Commands;

use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class GenerateModelCommand extends Generate {

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
  protected $description = 'Generate an Eloquent model.';

  /**
   * The type of file generation.
   * 
   * @var string
   */
  protected $type = 'model';

  /**
   * Compile a template or return a string
   * that should be inserted into the generated file.
   * 
   * @return string
   */
  protected function applyDataToStub()
  {
    return str_replace('{{name}}', ucwords($this->argument('fileName')), $this->getStub());
  }

  /**
   * Get the console command arguments.
   *
   * @return array
   */
  protected function getArguments()
  {
    return array(
      array('fileName', InputArgument::REQUIRED, 'Name of the model.'),
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
      array('path', null, InputOption::VALUE_OPTIONAL, 'An example option.', 'models'),
    );
  }

}