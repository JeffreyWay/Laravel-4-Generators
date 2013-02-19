<?php

namespace Way\Generators\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

abstract class Generate extends Command {

  /**
   * Execute the console command.
   *
   * @return void
   */
  public function fire()
  {
    $this->createFile();

    $this->info('File created at: ' . $this->getNewFilePath());
  }

  /**
   * Create any necessary directories
   * 
   * @return void
   */
  protected function generateDirectories($newFilePath)
  {
    if ( ! \File::exists(dirname($newFilePath)) )
    {
      \File::makeDirectory(dirname($newFilePath), 0777, true);
    }
  }

  /**
   * Create the new file
   * 
   * @return void
   */
  protected function createFile()
  {
    $newFilePath = $this->getNewFilePath();

    // Display an error if the file already exists.
    if ( \File::exists($newFilePath) )
    {
      return $this->error('The ' . $this->argument('fileName') . ' ' . $this->type . ' already exists!');
    }

    $this->generateDirectories($newFilePath);

    $stub = $this->applyDataToStub();

    \File::put($newFilePath, $stub);
  }

  /**
   * Grab the stub associated with the generate type
   * 
   * @return string
   */
  protected function getStub($fileName = null)
  {
    $fileName = $fileName ? $fileName : $this->type;

    return \File::get(__DIR__ . "/../stubs/{$fileName}.php");
  }

  /**
   * Get the path to the file that should be generated.
   * 
   * @return string
   */
  protected function getNewFilePath()
  {
    return app_path() . '/' . $this->option('path') . '/' . ucwords($this->argument('fileName')) . '.php';
  }

  /**
   * Get the console command options.
   *
   * @return array
   */
  protected function getOptions()
  {
    return array(
      array('path', null, InputOption::VALUE_OPTIONAL, 'Path to where the file should be created.', app_path() . '/')
    );
  }

  protected abstract function applyDataToStub();

}