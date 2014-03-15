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

        $fields = $this->separate_into_fields($fields);

        $parsed = [];

        foreach($fields as $index => $field)
        {
            // Example:
            // name:string:nullable => ['name', 'string', 'nullable']
            // name:string(15):nullable
            $chunks = preg_split('/\s?:\s?/', $field, null);

            // The first item will be our property
            $property = array_shift($chunks);

            // The next will be the schema type
            $type = array_shift($chunks);

            $args = null;

            // See if args were provided, like:
            // name:string(10)
            if (preg_match('/(.+?)\(([^)]+)\)/', $type, $matches))
            {
                $type = $matches[1];
                $args = $matches[2];
            }

            // Finally, anything that remains will
            // be our decorators
            $decorators = $chunks;

            $parsed[$index] = ['field' => $property, 'type' => $type];

            if (isset($args)) $parsed[$index]['args'] = $args;
            if ($decorators) $parsed[$index]['decorators'] = $decorators;
        }

        return $parsed;
    }

    private function separate_into_fields($fields) {
        // name:string, age:integer
        // name:string(10,2), age:integer
        // name:string(10, 2), age:integer
        // name:string(10), age:integer
        $firstFields = preg_split('/\s?,\s(\D)/', $fields, null, PREG_SPLIT_DELIM_CAPTURE);
        $singleChars = [];
        $fields = [];

        foreach($firstFields as $key => $value) {
            if (strlen($value) == 1) {
                $singleChars[] = $key + 1;
            } elseif(in_array($key, $singleChars)) {
                $fields[] = $firstFields[$key - 1] . $value;
            } else {
                $fields[] = $value;
            }
        }

        return $fields;
    }

}
