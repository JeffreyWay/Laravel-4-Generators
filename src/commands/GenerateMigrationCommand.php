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

    end($pieces);
    $tableName = prev($pieces);

    return [$action, $tableName];
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

    $stub = str_replace('{{name}}', \Str::camel($this->argument('fileName')), $stub);

    // The migration's Up method.
    $upMethod = $this->setUpMethod();
    $stub = str_replace('{{up}}', $upMethod, $stub);


    // The migration's Down method.
    $downMethod = $this->setDownMethod();
    $stub = str_replace('{{down}}', $downMethod, $stub);


    // Do we need to set any fields?
    $fields = $this->option('fields') ? $this->setFields() : '';
    $stub = str_replace('{{methods}}', $fields, $stub);

    return $stub;
  }

  /**
   * Grab up method stub and replace template vars
   * 
   * @return string
   */
  protected function setUpMethod()
  {
    // Are we creating a table?
    if ( $this->action === 'create' )
    {
      $upMethod = \File::get(__DIR__ . '/../stubs/migrationUpCreate.php');
    }

    $upMethod = str_replace('{{tableName}}', $this->tableName, $upMethod);


    return $upMethod;
  }

  /**
   * Grab down method stub and replace template vars
   * 
   * @return string
   */
  protected function setDownMethod()
  {
    // Are we creating a table?
    if ( $this->action === 'create' )
    {
      // then we need to drop the table
      $downMethod = \File::get(__DIR__ . '/../stubs/migrationDownDrop.php');
      $downMethod = str_replace('{{tableName}}', $this->tableName, $downMethod);
    }

    return $downMethod;
  }

  /**
   * Create a string of the Schema fields that
   * should be inserted into the sub template.
   * 
   * @return string
   */
  protected function setFields()
  {
    $fields = $this->convertFieldsToArray();

    $template = array_map(function($field) {
      return "\$table->{$field->type}('" . $field->name . "');";
    }, $fields);

    return implode("\n\t\t\t", $template);
  }

  /**
   * If Schema fields are specified, parse
   * them into an array of objects.
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
      $fieldAndType = explode(':', $bit);

      $bit = new \StdClass;
      $bit->name = $fieldAndType[0];
      $bit->type = $fieldAndType[1];
    }

    return $fields;
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