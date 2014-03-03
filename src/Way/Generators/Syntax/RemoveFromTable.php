<?php namespace Way\Generators\Syntax;

class RemoveFromTable extends Table {

    /**
     * Compile and return string for removing columns
     *
     * @param $migrationData
     * @param array $fields
     * @return mixed
     */
    public function remove($migrationData, array $fields)
    {
        $migrationData['method'] = 'table';

        $compiled = $this->compiler->compile($this->getTemplate(), $migrationData);

        return $this->replaceFieldsWith($this->dropColumns($fields), $compiled);
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