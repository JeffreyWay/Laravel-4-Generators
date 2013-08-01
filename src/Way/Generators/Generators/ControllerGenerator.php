<?php

namespace Way\Generators\Generators;

use Illuminate\Filesystem\Filesystem as File;
use Illuminate\Support\Pluralizer;

class ControllerGenerator extends Generator {

    /**
     * What subfolder are templates save to
     * @var string
     */
    private $views_subfolder = '';
    public function setViewsSubfolder($path) {
        $this->views_subfolder = str_finish(preg_replace('#/|\\\\#', '.', $path), '.');
    }
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

        return str_replace('{{name}}', $name, $this->template);
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
        $folder = $this->views_subfolder; //anything before / translated to dot path (example.and)
        $collection = strtolower(str_replace('Controller', '', $name)); // dogs
        $modelInstance = Pluralizer::singular($collection); // dog
        $modelClass = ucwords($modelInstance); // Dog

        foreach(array('modelInstance', 'modelClass', 'collection', 'folder') as $var)
        {
            $this->template = str_replace('{{'.$var.'}}', $$var, $this->template);
        }

        return $this->template;
    }
}