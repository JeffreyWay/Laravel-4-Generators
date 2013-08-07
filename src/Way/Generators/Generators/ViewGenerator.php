<?php

namespace Way\Generators\Generators;

use Way\Generators\NameParser;

class ViewGenerator extends Generator {

    /**
     * Fetch the compiled template for a view
     *
     * @param  string $template Path to template
     * @param  NameParser $nameparser
     * @return string Compiled template
     */
    protected function getTemplate($template, NameParser $nameparser)
    {
        $this->template = $this->file->get($template);

        if ($this->needsScaffolding($template))
        {
            return $this->getScaffoldedTemplate();
        }

        // Otherwise, just set the file
        // contents to the file name
        return $nameparser->get('basename');
    }

    /**
     * Get the scaffolded template for a view
     *
     * @return string Compiled template
     */
    protected function getScaffoldedTemplate()
    {
        $nameparser = new NameParser($this->cache->getModelName());

        $model = strtolower($nameparser->get('model')); //post
        $pluralModel = strtolower($nameparser->get('controller')); // posts

        $className = $nameparser->get('model'); //Post
        $formalModel = $nameparser->get('controller'); // Posts

        // Create and Edit views require form elements
        if ($nameparser === 'create.blade' or $nameparser === 'edit.blade')
        {
            $formElements = $this->makeFormElements();

            $this->template = str_replace('{{formElements}}', $formElements, $this->template);
        }

        // Replace template vars in view
        foreach(array('model', 'pluralModel', 'formalModel', 'className') as $var)
        {
            $this->template = str_replace('{{'.$var.'}}', $$var, $this->template);
        }

        // And finally create the table rows
        list($headings, $fields, $editAndDeleteLinks) = $this->makeTableRows($nameparser);
        $this->template = str_replace('{{headings}}', implode(PHP_EOL."\t\t\t\t", $headings), $this->template);
        $this->template = str_replace('{{fields}}', implode(PHP_EOL."\t\t\t\t\t", $fields) . PHP_EOL . $editAndDeleteLinks, $this->template);

        return $this->template;
    }

    /**
     * Create the table rows
     *
     * @param NameParser $nameparser
     * @return array
     */
    protected function makeTableRows(NameParser $nameparser)
    {
        $pluralModel = $nameparser->get('controller'); // posts
        $model = strtolower($nameparser->get('model'));

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
                    <td>{{ link_to_route('{$pluralModel}.edit', 'Edit', array(\${$model}->id), array('class' => 'btn btn-info')) }}</td>
                    <td>
                        {{ Form::open(array('method' => 'DELETE', 'route' => array('{$pluralModel}.destroy', \${$model}->id))) }}
                            {{ Form::submit('Delete', array('class' => 'btn btn-danger')) }}
                        {{ Form::close() }}
                    </td>
EOT;

        return array($headings, $fields, $editAndDelete);
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
