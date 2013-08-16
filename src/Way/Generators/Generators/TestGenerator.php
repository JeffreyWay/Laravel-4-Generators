<?php

namespace Way\Generators\Generators;

use Way\Generators\NameParser;

class TestGenerator extends Generator {

    /**
     * Fetch the compiled template for a test
     *
     * @param  string $template Path to template
     * @param  NameParser $classNameparser
     * @return string Compiled template
     */
    protected function getTemplate($template, NameParser $nameparser)
    {
        $pluralModel = strtolower($nameparser->get('controller')); // dogs
        $model = str_singular($nameparser->get('model')); // dog
        $Model = $nameparser->get('model'); // Dog
        $className = $nameparser->get('controller') . 'Test'; // DogsTest

        $this->template = $this->file->get($template);

        $this->getNamespaced($nameparser);

        foreach(array('pluralModel', 'model', 'Model', 'className') as $var)
        {
            $this->template = str_replace('{{'.$var.'}}', $$var, $this->template);
        }

        return $this->template;
    }

}