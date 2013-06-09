<?php

namespace Way\Generators\Generators;

use Illuminate\Filesystem\Filesystem as File;
use Illuminate\Support\Pluralizer;
use Illuminate\Support\Str;

class ControllerGenerator extends Generator {

    /**
     * Fetch the compiled template for a controller
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
            $this->template = $this->getScaffoldedController($template, $name);
        }

        return str_replace('{{name}}', Str::studly($name), $this->template);
    }

    /**
     * Get template for a scaffold
     *
     * @param  string $template Path to template
     * @param  string $name
     * @return string
     */
    protected function getScaffoldedController($template, $name)
    {
        $collection = Str::snake(str_replace('Controller', '', $name)); // dog_kinds
        $modelInstance = Pluralizer::singular($collection); // dog_kind
        $modelClass = Str::studly($modelInstance); // DogKind

        foreach(array('modelInstance', 'modelClass', 'collection') as $var)
        {
            $this->template = str_replace('{{'.$var.'}}', $$var, $this->template);
        }

        return $this->template;
    }
}