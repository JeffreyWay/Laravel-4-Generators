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

        // All new tables should have an identifier
        // Let's add that for the user automatically
        $primaryKey['id'] = ['type' => 'increments'];
        $fields = $primaryKey + $fields;

        // We'll also add timestamps to new tables for convenience
        $fields[''] = ['type' => 'timestamps'];

        return (new AddToTable($this->file, $this->compiler))->add($migrationData, $fields);
    }

} 