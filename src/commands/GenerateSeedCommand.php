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
   * Execute the console command.
   *
   * @return void
   */
  public function fire()
  {
    parent::fire();

    $this->updateDatabaseSeederRunMethod();
  }

  /**
   * Compile a template or return a string
   * that should be inserted into the generated file.
   *
   * @return string
   */
  protected function applyDataToStub()
  {
    $camel = $this->argument('tableName');
    $studly = ucwords($camel);

    $stub = str_replace('{{TableName}}', $studly, $this->getStub());
    return str_replace('{{tableName}}', $camel, $stub);
  }

  /**
   * Updates the DatabaseSeeder file's run method to
   * call this new seed class
   * @return void
   */
  protected function updateDatabaseSeederRunMethod()
  {
    $databaseSeederPath = app_path() . '/database/seeds/DatabaseSeeder.php';
    $tableSeederClassName = ucwords($this->argument('tableName')) . 'TableSeeder';

    $content = \File::get($databaseSeederPath);
    $content = preg_replace("/(run\(\).+?)}/us", "$1\t\$this->call('{$tableSeederClassName}');\n\t}", $content);

    \File::put($databaseSeederPath, $content);
  }

  /**
   * Get the console command arguments.
   *
   * @return array
   */
  protected function getArguments()
  {
    return array(
      array('tableName', InputArgument::REQUIRED, 'Name of the table.'),
    );
  }

  /**
   * Get the path to the file that should be generated.
   *
   * @return string
   */
  protected function getNewFilePath()
  {
    return app_path() . '/' . $this->option('path') . '/' . ucwords($this->argument('tableName')) . 'TableSeeder.php';
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