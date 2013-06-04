<?php

namespace Way\Generators\Generators;

class MigrationGenerator extends Generator {

    /**
     * Fetch the compiled template for a migration
     *
     * @param  string $template Path to template
     * @param  string $name
     * @return string Compiled template
     */
    protected function getTemplate($template, $name)
    {
        // We begin by fetching the master migration stub.
        $stub = $this->file->get(__DIR__.'/templates/migration/migration.txt');

        // Next, set the migration class name
        $stub = str_replace('{{name}}', \Str::studly($name), $stub);

        // Now, we're going to handle the tricky
        // work of creating the Schema
        $upMethod = $this->getUpStub();
        $downMethod = $this->getDownStub();

        // Finally, replace the migration stub with the dynamic up and down methods
        $stub = str_replace('{{up}}', $upMethod, $stub);
        $stub = str_replace('{{down}}', $downMethod, $stub);

        return $stub;
    }

    /**
     * Parse the migration name
     *
     * @param  string $name
     * @param  array $fields
     * @return MigrationGenerator
     */
    public function parse($name, $fields)
    {
        list($action, $tableName) = $this->parseMigrationName($name);

        $this->action = $action;
        $this->tableName = $tableName;
        $this->fields = $fields;

        return $this;
    }

    /**
    * Parse some_migration_name into array
    *
    * @param string $name
    * @return array
    */
    protected function parseMigrationName($name)
    {
        // create_users_table
        // add_user_id_to_posts_table
        $pieces = explode('_', $name);

        $action = $pieces[0];

        // If the migration name is create_users,
        // then we'll set the tableName to the last
        // item. But, if it's create_users_table,
        // then we have to compensate, accordingly.
        $tableName = end($pieces);
        if ( $tableName === 'table' )
        {
            $tableName = prev($pieces);
        }

        // For example: ['add', 'posts']
        return array($action, $tableName);
    }

    /**
    * Grab up method stub and replace template vars
    *
    * @return string
    */
    protected function getUpStub()
    {
        switch($this->action) {
            case 'add':
            case 'insert':
                $upMethod = $this->file->get(__DIR__ . '/templates/migration/migration-up.txt');
                $fields = $this->fields ? $this->setFields('addColumn') : '';
                break;

            case 'remove':
            case 'drop':
                $upMethod = $this->file->get(__DIR__ . '/templates/migration/migration-up.txt');
                $fields = $this->fields ? $this->setFields('dropColumn') : '';
                break;

            case 'create':
            case 'make':
            default:
                $upMethod = $this->file->get(__DIR__ . '/templates/migration/migration-up-create.txt');
                $fields = $this->fields ? $this->setFields('addColumn') : '';
                break;
        }

        // Replace the tableName in the template
        $upMethod = str_replace('{{tableName}}', $this->tableName, $upMethod);

        // Insert the schema into the up method
        return str_replace('{{methods}}', $fields, $upMethod);
    }

    /**
    * Grab down method stub and replace template vars
    *
    * @return string
    */
    protected function getDownStub()
    {
        switch($this->action) {
          case 'add':
          case 'insert':
            // then we to remove columns in reverse
            $downMethod = $this->file->get(__DIR__ . '/templates/migration/migration-down.txt');
            $fields = $this->fields ? $this->setFields('dropColumn') : '';
            break;

          case 'remove':
          case 'drop':
            // then we need to add the columns in reverse
            $downMethod = $this->file->get(__DIR__ . '/templates/migration/migration-down.txt');
            $fields = $this->fields ? $this->setFields('addColumn') : '';
            break;

          case 'create':
          case 'make':
          default:
            // then we need to drop the table in reverse
            $downMethod = $this->file->get(__DIR__ . '/templates/migration/migration-down-drop.txt');
            $fields = $this->fields ? $this->setFields('dropColumn') : '';
            break;
        }

        // Replace the tableName in the template
        $downMethod = str_replace('{{tableName}}', $this->tableName, $downMethod);

        // Insert the schema into the down method
        return str_replace('{{methods}}', $fields, $downMethod);
    }

    /**
    * Create a string of the Schema fields that
    * should be inserted into the sub template.
    *
    * @param string $method (addColumn | dropColumn)
    * @return string
    */
    protected function setFields($method = 'addColumn')
    {
        $fields = $this->convertFieldsToArray();

        $template = array_map(array($this, $method), $fields);

        return implode("\n\t\t\t", $template);
    }

    /**
    * If Schema fields are specified, parse
    * them into an array of objects.
    *
    * So: name:string, age:integer
    * Becomes: [ ((object)['name' => 'string'], (object)['age' => 'integer'] ]
    *
    * @returns mixed
    */
    protected function convertFieldsToArray()
    {
        // TODO this needs to be injected
        $fields = $this->fields;

        if ( !$fields ) return;

        /**
         * Black Magic Regex-Fu! Let me explain...
         *
         * After this beast of a regular expression,
         * we have 4 capturing cases
         *
         *   $matches[0] : The whole shebang
         *       Gives an array of complete field descriptions
         *
         *   $matches[1] : ([a-z]+:[a-z]+)
         *       returns <name>:<type> descriptions
         *
         *   $matches[2] : (\[(?:[\d,]+|'[[:print:]]+',?)+\])?
         *       I know this looks bad at first, but basically it looks for
         *           integer and decimal options via [\d,]+
         *               [50] or [10,2]
         *           and
         *           lists via '[[:print:]]+',?
         *               ['this', 'is', 'a list.', '123', 'comma, see']
         *
         *      Note: This group is completely optional
         *              ( '?' at the end of the pattern )
         *
         *   $matches[3] : :?([a-z]+)?
         *       Returns the 'index' options, if any are set
         *
         *
         * See this, for reference: http://d.pr/i/CSOH
         *
         */

        $pattern = "/([a-z_][a-z0-9_]*:[a-z]+)(\[(?:[\d,]+|'[[:print:]]+',?)+\])?:?([a-z:\(\))]+)?/i";

        preg_match_all($pattern, $fields, $matches);

        // Re-Format the matches
        $fields     = array();
        $fieldCount = count($matches[0]);

        // Sanitizer for 3rd-nth params
        $sanitize_index = function($val) { return strpos($val, '(') !== FALSE ? $val : $val . '()'; };

        while($fieldCount--)
        {
            $columnInfo = explode(':', $matches[1][$fieldCount]);

            $field = new \StdClass;
            $field->name = $columnInfo[0];
            $field->type = $columnInfo[1];

            // Did the user set any data-type related options?
            if($matches[2][$fieldCount])
            {
                // Turn the options string into an array:
                // Well, we know the option string pretty much looks like PHP's array shorthand notation [1,2,3,4]
                // So, with a little modification to the string and the help of json_decode(); we can easily
                // get this job done.
                $field->options = json_decode(str_replace("'", '"', $matches[2][$fieldCount]));
            }

            // If there is a third key, then
            // the user is setting an index/option.
            if($matches[3][$fieldCount])
            {
                $indexes = explode(':', $matches[3][$fieldCount]);
                $field->index = array_map($sanitize_index, $indexes);
            }

            array_push($fields, $field);
        }

        // Flip to preserve the field order
        $fields = array_reverse($fields);

        return $fields;
    }

    /**
    * Return template string for adding a column
    *
    * @param string $field
    * @return string
    */
    protected function addColumn($field)
    {

        // We'll start building the appropriate Schema method
        $html = "\$table->{$field->type}";

        // Some type specific alterations
        $html .= "('{$field->name}'";
        if($field->type === 'enum' && isset($field->options))
        {
            // Re-wrap the enum values in quotes
            $list = array_map('json_encode', $field->options);
            // and generate a nice and safe array syntax
            $html .= ', array(' . implode(', ', $list) . ')';
        }
        else
        {
            $html .= isset($field->options)
                ? ', ' . implode(', ', $field->options)
                : '';
        }
        $html .= ')';

        // Take care of any potential indexes or options
        if ( isset($field->index) )
        {
            $html .= '->' . implode('->', $field->index);
        }

        return $html.';';
    }

    /**
    * Return template string for dropping a column
    *
    * @param string $field
    * @return string
    */
    protected function dropColumn($field)
    {
        return "\$table->dropColumn('" . $field->name . "');";
    }

    protected function getPath($path)
    {
        $migrationFile = strtolower(basename($path));

        return dirname($path).'/'.date('Y_m_d_His').'_'.$migrationFile;
    }

}
