<?php

namespace Way\Generators\Generators;

class ModelGenerator extends Generator {

    /**
     * Fetch the compiled template for a model
     *
     * @param  string $template Path to template
     * @param  string $className
     * @return string Compiled template
     */
    protected function getTemplate($template, $className)
    {
        $this->template = $this->file->get($template);

        if ($this->needsScaffolding($template))
        {
            $this->template = $this->getScaffoldedModel($className);
        }

        return str_replace('{{className}}', $className, $this->template);
    }

    /**
     * Get template for a scaffold
     *
     * @param  string $template Path to template
     * @param  string $name
     * @return string
     */
    protected function getScaffoldedModel($className)
    {
        if (! $fields = $this->cache->getFields())
        {
            return str_replace('{{rules}}', '', $this->template);
        }

        $rules = array();
        array_walk($fields, function($type, $field) use (&$rules) {
            $rule = 'required';
            switch($type) {
                case 'integer':
                case 'bigInteger':
                case 'smallInteger':
                case 'boolean':
                    $rule .= '|integer';
                    break;
                case 'float':
                    $rule .= '|numeric';
                    break;
                case 'date':
                case 'time':
                case 'datetime':
                case 'timestamp':
                    $rule .= '|date';
                    break;
            }
            $rules[] = "'$field' => '$rule'";
        });

        return str_replace('{{rules}}', PHP_EOL."\t\t".implode(','.PHP_EOL."\t\t", $rules) . PHP_EOL."\t", $this->template);
    }

}
