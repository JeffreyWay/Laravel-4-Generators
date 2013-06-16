<?php

namespace Way\Generators\Generators;

use Illuminate\Support\Pluralizer;
use Illuminate\Support\Str;

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
        $collection = Str::snake(str_replace('Test', '', $className)); //  dog_kinds
        $modelInstance = Pluralizer::singular($collection); // dog_kind
        $modelClass = Str::studly($modelInstance); // DogKind

        $template = $this->file->get($template);

        foreach(array('collection', 'modelInstance', 'modelClass', 'className') as $var)
        {
            $template = str_replace('{{'.$var.'}}', $$var, $template);
        }

        return $template;
    }

}