<?php

namespace Way\Generators\Commands;

use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class GenerateViewCommand extends Generate {

  /**
   * The console command name.
   *
   * @var string
   */
  protected $name = 'generate:view';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Generate a new view';

  /**
   * The type of file generation.
   * 
   * @var string
   */
  protected $type = 'view';

  /**
   * Get the path to the file that should be generated.
   * 
   * @return string
   */
  protected function getNewFilePath()
  {
    return app_path() . '/' . $this->option('path') . '/' . $this->argument('fileName') . '.blade.php';
  }

  /**
   * Compile a template or return a string
   * that should be inserted into the generated file.
   * 
   * @return string
   */
  protected function applyDataToStub()
  {
    return 'The ' . $this->argument('fileName') . '.blade.php view.';
  }

  /**
   * Get the console command arguments.
   *
   * @return array
   */
  protected function getArguments()
  {
    return array(
      array('fileName', InputArgument::REQUIRED, 'Name of the view.'),
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
      array('path', null, InputOption::VALUE_OPTIONAL, 'Path to where the view should be created', 'views'),
    );
  }

}