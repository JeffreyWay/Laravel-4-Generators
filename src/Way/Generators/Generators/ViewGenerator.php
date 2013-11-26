<?php

namespace Way\Generators\Generators;

use Illuminate\Support\Pluralizer;

class ViewGenerator extends Generator {

    /**
     * Fetch the compiled template for a view
     *
     * @param  string $template Path to template
     * @param  string $name
     * @return string Compiled template
     */
    protected function getTemplate($template, $name)
    {
        $this->template = $this->file->get($template);

        if ($this->needsScaffolding($template))
        {
            return $this->getScaffoldedTemplate($name);
        }

        // Otherwise, just set the file
        // contents to the file name
        return $name;
    }

    /**
     * Get the scaffolded template for a view
     *
     * @param  string $name
     * @return string Compiled template
     */
    protected function getScaffoldedTemplate($name)
    {
        $model = $this->cache->getModelName();  // post
        $models = Pluralizer::plural($model);   // posts
        $Models = ucwords($models);             // Posts
        $Model = Pluralizer::singular($Models); // Post

        // Create and Edit views require form elements
        if ($name === 'create.blade' or $name === 'edit.blade')
        {
            $formElements = $this->makeFormElements();

            $this->template = str_replace('{{formElements}}', $formElements, $this->template);
        }

        // Replace template vars in view
        foreach(array('model', 'models', 'Models', 'Model') as $var)
        {
            $this->template = str_replace('{{'.$var.'}}', $$var, $this->template);
        }

        // And finally create the table rows
        list($headings, $fields, $editAndDeleteLinks) = $this->makeTableRows($model);
        $this->template = str_replace('{{headings}}', implode(PHP_EOL."\t\t\t\t", $headings), $this->template);
        $this->template = str_replace('{{fields}}', implode(PHP_EOL."\t\t\t\t\t", $fields) . PHP_EOL . $editAndDeleteLinks, $this->template);

        return $this->template;
    }

    /**
     * Create the table rows
     *
     * @param  string $model
     * @return Array
     */
    protected function makeTableRows($model)
    {
        $models = Pluralizer::plural($model); // posts

        $fields = $this->cache->getFields();

        // First, we build the table headings
        $headings = array_map(function($field) {
            return '<th>' . ucwords($field) . '</th>';
        }, array_keys($fields));

        // And then the rows, themselves
        $fields = array_map(function($field) use ($model) {
            return "<td>{{{ \$$model->$field }}}</td>";
        }, array_keys($fields));

        // Now, we'll add the edit and delete buttons.
        $editAndDelete = <<<EOT
                    <td>{{ link_to_route('{$models}.edit', 'Edit', array(\${$model}->id), array('class' => 'btn btn-info')) }}</td>
                    <td>
                        {{ Form::open(array('method' => 'DELETE', 'route' => array('{$models}.destroy', \${$model}->id))) }}
                            {{ Form::submit('Delete', array('class' => 'btn btn-danger')) }}
                        {{ Form::close() }}
                    </td>
EOT;

        return array($headings, $fields, $editAndDelete);
    }

    /**
     * Convert array parameter into an array string for creating a select in a form:
     * array('lab' | 'poodle '|' pit bull')
     * ->
     * array('lab' => 'Lab', 'poodle' => 'Poodle', 'pit bull' => 'Pit Bull')
     * 
     * @param string $parameter
     *
     * @return string
     */
    private function enumSelect($parameter)
    {
        $arrayInternals = '/
            array\(  # literal: array(
            ([^)]+)  # capture anything except literal: )
            \)       # closing )
        /x';
        preg_match($arrayInternals, $parameter, $matches);

        //remove leading quote
        $options = preg_replace('/^\s*[\'"]\s*/', '', $matches[1]);
        //remove final quote
        $options = preg_replace('/\s*[\'"]\s*$/', '', $options);

        $inBetweenArrayItems = '/
            \s*["\']\s*  # Optionally some spaces, a quote, optionally mores paces
            \|           # Literal pipe as separator
            \s*["\']\s*  # Same as above ^^
        /x';
        $options = preg_split($inBetweenArrayItems, $options);

        $keyValues = array();
        foreach($options as $option)
        {
            $ucOption = ucwords($option);
            $keyValues[] = "'$option' => '$ucOption'";
        }

        return 'array(' . implode(", ", $keyValues) . ')';
    }

    /**
     * Extract a type and optionally a parameter into an array of [type, parameter]
     * @param $type
     *
     * @return array
     */
    private function extractParameterFromType($type)
    {
        //no parameter present
        if(strpos($type, '[') === FALSE)
        {
            return [$type, ""];
        }

        // "enum[array('new'|'approved'|'denied')]"
        // ->
        // [ "enum", "array('new'|'approved'|'denied')]" ]
        list($type, $parameter) = explode('[', $type);

        // remove trailing ']' and anything that follows.
        $parameter = substr($parameter, 0, strpos($parameter, ']'));

        return [$type, $parameter];
    }

    /**
     * Add Laravel methods, as string,
     * for the fields
     *
     * @return string
     */
    public function makeFormElements()
    {
        $formMethods = array();

        foreach($this->cache->getFields() as $name => $type)
        {
            list($type, $parameter) = $this->extractParameterFromType($type);
            $formalName = ucwords($name);

            // TODO: add remaining types
            switch($type)
            {
                case 'integer':
                   $element = "{{ Form::input('number', '$name') }}";
                    break;

                case 'text':
                    $element = "{{ Form::textarea('$name') }}";
                    break;

                case 'boolean':
                    $element = "{{ Form::checkbox('$name') }}";
                    break;

                case 'enum':
                    $select = $this->enumSelect($parameter);
                    $element = "{{ Form::select('$name', $select) }}";
                    break;

                default:
                    $element = "{{ Form::text('$name') }}";
                    break;
            }

            // Now that we have the correct $element,
            // We can build up the HTML fragment
            $frag = <<<EOT
        <li>
            {{ Form::label('$name', '$formalName:') }}
            $element
        </li>

EOT;

            $formMethods[] = $frag;
        }

        return implode(PHP_EOL, $formMethods);
    }

}
