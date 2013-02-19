<?php

namespace Way\Generators\Commands;

use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class GenerateSeedCommand extends Generate {

  /**
   * The console command name.
   *
   * @var string
   */
  protected $name = 'generate:seed';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Generate a DB seed class.';

  /**
   * The type of file generation.
   * 
   * @var string
   */
  protected $type = 'seed';

  /**
   * Compile a template or return a string
   * that should be inserted into the generated file.
   * 
   * @return string
   */
  protected function applyDataToStub()
  {
    return str_replace('{{tableName}}', ucwords($this->argument('fileName')), $this->getStub());
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
   * Get the path to the file that should be generated.
   * 
   * @return string
   */
  protected function getNewFilePath()
  {
    return app_path() . '/' . $this->option('path') . '/' . ucwords($this->argument('fileName')) . 'TableSeeder.php';
  }

  /**
   * Get the console command options.
   *
   * @return array
   */
  protected function getOptions()
  {
    return array(
      array('path', null, InputOption::VALUE_OPTIONAL, 'The path to where the seed will be stored.', 'database/seeds'),
    );
  }

}