<?php

namespace Way\Generators\Generators;

class TestGenerator extends Generator {

    /**
     * Fetch the compiled template for a test
     *
     * @param  string $template Path to template
     * @param  string $className
     * @return string Compiled template
     */
    protected function getTemplate($template, $className)
    {
        $pluralModel = strtolower(str_replace('Test', '', $className)); //  dogs
        $model = str_singular($pluralModel); // dog
        $Model = ucwords($model); // Dog

        $template = $this->file->get($template);

        foreach(array('pluralModel', 'model', 'Model', 'className') as $var)
        {
            $template = str_replace('{{'.$var.'}}', $$var, $template);
        }

        return $template;
    }

}