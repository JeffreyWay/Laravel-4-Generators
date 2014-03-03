<?php namespace Way\Generators\Syntax;

class CreateTable extends Table {

    /**
     * Build string for creating a
     * table and columns
     *
     * @param $migrationData
     * @param $fields
     * @return mixed
     */
    public function create($migrationData, $fields)
    {
        $migrationData = ['method' => 'create', 'table' => $migrationData['table']];

        return (new AddToTable($this->file, $this->compiler))->add($migrationData, $fields);
    }

} 