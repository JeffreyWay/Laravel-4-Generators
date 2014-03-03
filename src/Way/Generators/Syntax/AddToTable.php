<?php namespace Way\Generators\Syntax;

class AddToTable extends Table {

    /**
     * Add syntax for table addition
     *
     * @param $migrationData
     * @param array $fields
     * @return mixed
     */
    public function add($migrationData, array $fields)
    {
        if ( ! isset($migrationData['method']))
        {
            $migrationData['method'] = 'table';
        }

        $compiled = $this->compiler->compile($this->getTemplate(), $migrationData);

        return $this->replaceFieldsWith($this->addColumns($fields), $compiled);
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

} 