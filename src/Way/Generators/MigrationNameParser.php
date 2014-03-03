<?php namespace Way\Generators;

class MigrationNameParser {

    /**
     * Recognized CRUD types
     *
     * @var array
     */
    public static $acceptableTypes = [
        'create', 'add', 'update', 'delete', 'destroy'
    ];

    /**
     * Parse a migration name, like:
     * create_orders_table
     * add_last_name_to_recent_orders_table
     *
     * @param $migrationName
     * @throws InvalidActionType
     * @return array
     */
    public function parse($migrationName)
    {
        // Split the migration name into pieces
        // create_orders_table => ['create', 'orders', 'table']
        $pieces = explode('_', $migrationName);

        // We'll start by fetching the CRUD action type
        $action = array_shift($pieces);

        // This action type must be something we understand
        if ( ! in_array($action, self::$acceptableTypes))
        {
            throw new InvalidActionType;
        }

        // Next, we can remove any "table" string at
        // the end of the migration name, like:
        // create_orders_table
        if (end($pieces) == 'table') array_pop($pieces);

        // Now, we need to figure out the table name
        $tableName = $this->getTableName($pieces);

        return compact('action', 'tableName');
    }

    /**
     * Determine what the table name should be
     *
     * @param array $pieces
     * @return string
     */
    protected function getTableName(array $pieces)
    {
        $tableName = [];

        // This is deceptively complex, because
        // there are a number of ways to write
        // these migration names. We'll work backwards
        // to figure out the name.
        foreach(array_reverse($pieces) as $piece)
        {
            // Once we get to a connecting word (if any), this
            // will signal the end of our search. So, for
            // add_name_to_archived_lessons, "archived_lessons"
            // would be the table name
            if (in_array($piece, ['to', 'for', 'on'])) break;

            $tableName[] = $piece;
        }

        // We can't forget to reverse it back again!
        return implode('_', array_reverse($tableName));
    }

}