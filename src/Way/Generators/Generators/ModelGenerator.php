<?php

namespace Way\Generators\Generators;

class ModelGenerator extends Generator {


	/**
     * Parse the relation model file
     *
     * @param  string $name
     * @param  string $relname
	 * @param  string $type
     * @param  string $separator
	 * @param  string $container
     */
	protected function relText($name, $relname, $type, $separator, $container)
	{
		
		$upperCase = ucfirst($relname);
		$relname = substr($relname, 2, 0);
		$container = str_replace('{{relname}}', $relname, $container);
		$container = str_replace('{{reltype}}', $type, $container);
		$container = str_replace('{{relnameUpperCase}}', $upperCase, $container);
		
		return str_replace('{{name}}', strstr($name, $separator, false), $container);
	}

    /**
     * Fetch the compiled template for a model
     *
     * @param  string $template Path to template
     * @param  string $name
     * @return string Compiled template
     */
    protected function getTemplate($template, $name)
    {
		$hasManySign = '->';
		$belongsToSign = '<-';

        
		
		if($relname = strstr($name, $hasManySign))
		{
			$this->template = $this->file->get($relTemplate);
			return relText($name, $relname, 'hasMany', $hasManySign, $this->template);
			
		}
		elseif($relname = strstr($name, $belongsToSign))
		{
			$this->template = $this->file->get($relTemplate);
			return relText($name, $relname, 'belongsTo', $belongsToSign, $this->template);
			
		}
		else
		{
			if ($this->needsScaffolding($template))
        	{
        	    $this->template = $this->getScaffoldedModel($name);
        	}
			else
			{
				$this->template = $this->file->get($template);
			}
			return str_replace('{{name}}', $name, $this->template);
		}
        
    }

    /**
     * Get template for a scaffold
     *
     * @param  string $template Path to template
     * @param  string $name
     * @return string
     */
    protected function getScaffoldedModel($name)
    {
        if (! $fields = $this->cache->getFields())
        {
            return str_replace('{{rules}}', '', $this->template);
        }

        $rules = array_map(function($field) {
            return "'$field' => 'required'";
        }, array_flip($fields));

        return str_replace('{{rules}}', PHP_EOL."\t\t".implode(','.PHP_EOL."\t\t", $rules) . PHP_EOL."\t", $this->template);
    }
}