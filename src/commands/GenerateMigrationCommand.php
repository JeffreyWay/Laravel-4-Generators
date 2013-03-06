<?php

namespace Way\Generators\Commands;

use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class GenerateMigrationCommand extends Generate {

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
   * The type of file generation.
   *
   * @var string
   */
  protected $type = 'migration';

  /**
   * What are we doing to this table?
   *
   * @var string
   */
  protected $action;

  /**
   * Which table to work with
   *
   * @var string
   */
  protected $tableName;

  /**
   * Run the command. Executed immediately.
   *
   * @return void
   */
  public function fire()
  {
    list($action, $tableName) = $this->parseMigrationName();

    $this->action = $action;
    $this->tableName = $tableName;

    parent::fire();

    $this->dumpAutoloads();
  }

  /**
   * composer dump-autoload to recognize new migration file
   * @return void
   */
  protected function dumpAutoloads()
  {
    // TODO: Find better way to do this through IoC
    $composer = new \Illuminate\Foundation\Composer(
                  new \Illuminate\Filesystem\Filesystem,
                  base_path()
                );

    $composer->dumpAutoloads();
  }

  /**
   * Parse some_migration_name into array
   *
   * @return array
   */
  protected function parseMigrationName()
  {
    // create_users_table
    // add_user_id_to_posts_table
    $pieces = explode('_', $this->argument('fileName'));

    $action = $pieces[0];

    // If the migration name is create_users,
    // then we'll set the tableName to the last
    // item. But, if it's create_users_table,
    // then we have to compensate, accordingly.
    $tableName = end($pieces);
    if ( $tableName === 'table' )
    {
      $tableName = prev($pieces);
    }

    // For example: ['add', 'posts']
    return array($action, $tableName);
  }

  /**
   * Get the path to the file that should be generated.
   *
   * @return string
   */
  protected function getNewFilePath()
  {
    return app_path() . '/' . $this->option('path') . '/' . date('Y_m_d_His') . '_' . $this->argument('fileName') . '.php';
  }

  /**
   * Compile a template or return a string
   * that should be inserted into the generated file.
   *
   * @return string
   */
  protected function applyDataToStub()
  {
    $stub = $this->getStub();

    // Replace the name of the class
    $stub = str_replace('{{name}}', \Str::studly($this->argument('fileName')), $stub);

    $upMethod = $this->setUpMethod();
    $downMethod = $this->setDownMethod();

    // Replace the migration stub with the dynamic up and down methods
    $stub = str_replace('{{up}}', $upMethod, $stub);
    $stub = str_replace('{{down}}', $downMethod, $stub);

    return $stub;
  }

  /**
   * Grab up method stub and replace template vars
   *
   * @return string
   */
  protected function setUpMethod()
  {
    switch($this->action) {
      case 'add':
      case 'insert':
        $upMethod = \File::get(__DIR__ . '/../stubs/migrationUp.php');
        $fields = $this->option('fields') ? $this->setFields('addColumn') : '';
        break;

      case 'remove':
      case 'drop':
        $upMethod = \File::get(__DIR__ . '/../stubs/migrationUp.php');
        $fields = $this->option('fields') ? $this->setFields('dropColumn') : '';
        break;

      case 'create':
      case 'make':
      default:
        $upMethod = \File::get(__DIR__ . '/../stubs/migrationUpCreate.php');
        $fields = $this->option('fields') ? $this->setFields('addColumn') : '';
        break;
    }

    // Replace the tableName in the template
    $upMethod = $this->replaceTableNameInTemplate($upMethod);

    // Insert the schema into the up method
    return str_replace('{{methods}}', $fields, $upMethod);
  }

  /**
   * Grab down method stub and replace template vars
   *
   * @return string
   */
  protected function setDownMethod()
  {
    switch($this->action) {
      case 'add':
      case 'insert':
        // then we to remove columns in reverse
        $downMethod = \File::get(__DIR__ . '/../stubs/migrationDown.php');
        $fields = $this->option('fields') ? $this->setFields('dropColumn') : '';
        break;

      case 'remove':
      case 'drop':
        // then we need to add the columns in reverse
        $downMethod = \File::get(__DIR__ . '/../stubs/migrationDown.php');
        $fields = $this->option('fields') ? $this->setFields('addColumn') : '';
        break;

      case 'create':
      case 'make':
        // then we need to drop the table in reverse
        $downMethod = \File::get(__DIR__ . '/../stubs/migrationDownDrop.php');
        $fields = $this->option('fields') ? $this->setFields('dropColumn') : '';
      default:
    }

    $downMethod = $this->replaceTableNameInTemplate($downMethod);

    // Insert the schema into the down method
    return str_replace('{{methods}}', $fields, $downMethod);
  }

  /**
   * Create a string of the Schema fields that
   * should be inserted into the sub template.
   *
   * @param string $method (addColumn | dropColumn)
   * @return string
   */
  protected function setFields($method = 'addColumn')
  {
    $fields = $this->convertFieldsToArray();

    $template = array_map(array($this, $method), $fields);

    return implode("\n\t\t\t", $template);
  }

  /**
   * If Schema fields are specified, parse
   * them into an array of objects.
   *
   * So: name:string, age:integer
   * Becomes: [ ((object)['name' => 'string'], (object)['age' => 'integer'] ]
   *
   * @returns mixed
   */
  protected function convertFieldsToArray()
  {
    $fields = $this->option('fields');

    if ( !$fields ) return;

    $fields = preg_split('/, ?/', $fields);

    foreach($fields as &$bit)
    {
      $fieldAndType = preg_split('/ ?: ?/', $bit);

      $bit = new \StdClass;
      $bit->name = $fieldAndType[0];
      $bit->type = $fieldAndType[1];
    }

    return $fields;
  }

  /**
   * Searches for {{tableName}} and replaces it
   * with what the user specifies
   *
   * @param string $template
   * @return string
   */
  protected function replaceTableNameInTemplate($template)
  {
    return str_replace('{{tableName}}', $this->tableName, $template);
  }

  /**
   * Return template string for adding a column
   *
   * @param string $field
   * @return string
   */
  protected function addColumn($field)
  {
    return "\$table->{$field->type}('" . $field->name . "');";
  }

  /**
   * Return template string for dropping a column
   *
   * @param string $field
   * @return string
   */
  protected function dropColumn($field)
  {
    return "\$table->dropColumn('" . $field->name . "');";
  }

  /**
   * Get the console command arguments.
   *
   * @return array
   */
  protected function getArguments()
  {
    return array(
      array('fileName', InputArgument::REQUIRED, 'Name of the migration.'),
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
      array('path', null, InputOption::VALUE_OPTIONAL, 'The path to the migrations folder', 'database/migrations'),
      array('fields', null, InputOption::VALUE_OPTIONAL, 'Table fields', null)
    );
  }
}
