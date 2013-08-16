<?php

namespace Way\Generators\Generators;

use Way\Generators\NameParser;

class ModelGenerator extends Generator {

    /**
     * Fetch the compiled template for a model
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
            $this->template = $this->getScaffoldedModel($nameparser);
        }

        $this->getNamespaced($nameparser);

        return str_replace('{{name}}', $nameparser->get('model'), $this->template);
    }

    /**
     * Get template for a scaffold
     *
     * @param  string $template Path to template
     * @param  NameParser $name
     * @return string
     */
    protected function getScaffoldedModel(NameParser $nameparser)
    {
        if (! $fields = $this->cache->getFields())
        {
            return str_replace('{{rules}}', '', $this->template);
        }

        $rules = array_map(function($field) {
            return "'$field' => 'required'";
        }, array_keys($fields));

        return str_replace('{{rules}}', PHP_EOL."\t\t".implode(','.PHP_EOL."\t\t", $rules) . PHP_EOL."\t", $this->template);
    }

}