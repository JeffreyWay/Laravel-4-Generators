<?php namespace Way\Generators\Parsers;

class MigrationFieldsParser {

    /**
     * Parse a string of fields, like
     * name:string, age:integer
     *
     * @param string $fields
     * @return array
     */
    public function parse($fields)
    {
        if ( ! $fields) return [];

        $fields = preg_split('/\s?,\s?/', $fields);
        $parsed = [];
        foreach($fields as $field)
        {
            list($property, $type) = preg_split('/\s?:\s?/', $field, null);
            $parsed[$property] = $type;
        }

        return $parsed;
    }

}
