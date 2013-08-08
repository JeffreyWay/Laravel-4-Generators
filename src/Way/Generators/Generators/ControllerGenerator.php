<?php

namespace Way\Generators\Generators;

use Illuminate\Filesystem\Filesystem as File;
use Way\Generators\NameParser;

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
     * @param  NameParser $nameparser
     * @return string Compiled template
     */
    protected function getTemplate($template, NameParser $nameparser)
    {
        $this->template = $this->file->get($template);

        if ($this->needsScaffolding($template))
        {
            $this->template = $this->getScaffoldedController($template, $nameparser);
        }

        $this->getNamespaced($nameparser);

        return str_replace('{{name}}', $nameparser->get('controller'), $this->template);
    }

    /**
     * Get template for a scaffold
     *
     * @param  string $template Path to template
     * @param  NameParser $name
     * @return string
     */
    protected function getScaffoldedController($template, NameParser $name)
    {
        $folder = $this->views_subfolder; //anything before / translated to dot path (example.and)
        $collection = strtolower($name->get('controller')); // dogs
        $modelInstance = strtolower($name->get('model')); // dog
        $modelClass = $name->get('model'); // Dog

        foreach(array('modelInstance', 'modelClass', 'collection', 'folder') as $var)
        {
            $this->template = str_replace('{{'.$var.'}}', $$var, $this->template);
        }

        return $this->template;
    }
}