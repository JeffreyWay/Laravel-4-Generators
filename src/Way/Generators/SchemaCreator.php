<?php namespace Way\Generators;

use Way\Generators\Filesystem\Filesystem;
use Way\Generators\Compilers\TemplateCompiler;

class SchemaCreator {

    /**
     * @var Filesystem\Filesystem
     */
    private $file;

    /**
     * @var Compilers\TemplateCompiler
     */
    private $compiler;

    /**
     * @param Filesystem $file
     * @param TemplateCompiler $compiler
     */
    function __construct(Filesystem $file, TemplateCompiler $compiler)
    {
        $this->file = $file;
        $this->compiler = $compiler;
    }

    /**
     * Build the string for the migration file "up" method
     *
     * @param array $migrationData
     * @param array $fields
     * @return mixed|string
     */
    public function up(array $migrationData, array $fields = [])
    {
        // If the client wants to delete the table,
        // then...let's delete it!
        if ($migrationData['action'] == 'delete')
        {
            return $this->dropTable($migrationData['table']);
        }

        $migrationData['method'] = $migrationData['action'] == 'create' ? 'create' : 'table';

        return $this->updateTable(
            $migrationData,
            $fields,
            $migrationData['action'] == 'remove' ? 'dropColumns' : 'addColumns'
        );
    }

    /**
     * Build the string for the migration file "down" method
     *
     * @param array $migrationData
     * @param array $fields
     * @return array|mixed|string
     */
    public function down(array $migrationData, $fields = [])
    {
        // If the user wanted to create a new table
        // Then we should drop it on the down
        if ($migrationData['action'] == 'create')
        {
            return $this->dropTable($migrationData['table']);
        }

        // If they wanted to drop the table, then
        // we should add it back on the down
        if ($migrationData['action'] == 'delete')
        {
           return $this->createTable($migrationData['table']);
        }

        $migrationData['method'] = 'table';

        return $this->updateTable(
            $migrationData,
            $fields,
            $migrationData['action'] == 'add' ? 'dropColumns' : 'addColumns'
        );
    }

    /**
     * Return string for creating a table
     *
     * @param $table
     * @return mixed
     */
    protected function createTable($table)
    {
        $migrationData = ['method' => 'create', 'table' => $table];

        $compiled = $this->compiler->compile($this->getTemplate(), $migrationData);

        // There's no way to know what the original fields were
        // So we'll apply no fields in this particular case...
        return $this->replaceFieldsWith([], $compiled);
    }


    /**
     * Return string for dropping a table
     *
     * @param $table
     * @return string
     */
    protected function dropTable($table)
    {
        return "Schema::drop('$table');";
    }

    /**
     * Create string to update table
     *
     * @param $migrationData
     * @param $fields
     * @param string $method
     * @internal param $table
     * @return mixed
     */
    protected function updateTable($migrationData, $fields, $method = 'addColumns')
    {
        $compiled = $this->compiler->compile($this->getTemplate(), $migrationData);

        return $this->replaceFieldsWith($this->$method($fields), $compiled);
    }

    /**
     * Replace $FIELDS$ in the given template
     * with the provided schema
     *
     * @param $schema
     * @param $template
     * @return mixed
     */
    protected function replaceFieldsWith($schema, $template)
    {
        return str_replace('$FIELDS$', implode(PHP_EOL."\t\t\t", $schema), $template);
    }

    /**
     * Fetch the template for a schema block
     *
     * @return string
     */
    protected function getTemplate()
    {
        return $this->file->get(__DIR__.'/templates/schema.txt');
    }

    /**
     * Return string for adding all columns
     *
     * @param $fields
     * @return array
     */
    protected function addColumns($fields)
    {
        $schema = [];

        foreach($fields as $property => $type)
        {
            $schema[] = $this->addColumn($property, $type);
        }

        return $schema;
    }

    /**
     * Return string for adding a column
     *
     * @param $property
     * @param $type
     * @return string
     */
    private function addColumn($property, $type)
    {
        return "\$table->$type('$property');";
    }

    /**
     * Return string for dropping all columns
     *
     * @param array $fields
     * @return array
     */
    protected function dropColumns(array $fields)
    {
        $schema = [];

        foreach($fields as $property => $type)
        {
            $schema[] = $this->dropColumn($property);
        }

        return $schema;
    }

    /**
     * Return string for dropping a column
     *
     * @param $property
     * @return string
     */
    private function dropColumn($property)
    {
        return "\$table->dropColumn('$property');";
    }

}