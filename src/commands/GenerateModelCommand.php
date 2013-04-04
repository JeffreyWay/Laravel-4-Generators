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
    $stub = $this->getStub();

    $stub = str_replace('{{name}}', ucwords($this->argument('fileName')), $this->getStub());

    $fields = $this->option('relationships') ? $this->setFields() : '';

    return str_replace('{{relationships}}', $fields, $stub);
  }

  /**
   * Create a string of the relationships that
   * should be inserted into the sub template.
   *
   * @param string $method (setRelationShip)
   * @return string
   */
  protected function setFields($method = 'setRelationShip')
  {
    $fields = $this->convertFieldsToArray();

    $template = array_map(array($this, $method), $fields);

    return implode("", $template);
  }

  /**
   * Return template string for relationship setup
   *
   * @param string $field
   * @return string
   */
  protected function setRelationShip($field)
  {
    $stub = \File::get(__DIR__ . '/../stubs/relationships.php');

    // Insert the schema into the down method
    $stub = str_replace('{{relationType}}', $field->type, $stub);
    $stub = str_replace('{{modelName}}', $field->model, $stub);
    $stub = str_replace('{{ucModelName}}', ucfirst($field->model), $stub);

    return $stub;
  }

  /**
   * If relationships are specified, parse
   * them into an array of objects.
   *
   * So: has_many:model, has_one:model
   * Becomes: [ ((object)['has_many' => 'model'], (object)['has_one' => 'model'] ]
   *
   * @returns mixed
   */
  protected function convertFieldsToArray()
  {
    $relationships = $this->option('relationships');

    if ( !$relationships ) return;

    $relationships = preg_split('/, ?/', $relationships);

    foreach($relationships as &$bit)
    {
      $columnInfo = preg_split('/ ?: ?/', $bit);

      $bit = new \StdClass;
      $bit->type = $columnInfo[0];
      $bit->model = $columnInfo[1];
    }

    return $relationships;
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
      array('relationships', null, InputOption::VALUE_OPTIONAL, 'Relationship options', null)
    );
  }

}