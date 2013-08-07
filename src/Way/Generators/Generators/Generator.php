<?php

namespace Way\Generators\Generators;

use Way\Generators\Cache;
use Illuminate\Filesystem\Filesystem as File;
use Way\Generators\NameParser;

class RequestedCacheNotFound extends \Exception {}

abstract class Generator {

    /**
     * File path to generate
     *
     * @var string
     */
    public $path;

    /**
     * File system instance
     * @var File
     */
    protected $file;

    /**
     * Cache
     * @var Cache
     */
    protected $cache;

    /**
     * File name
     * @var string
     */
    protected $name;

    /**
     * Parsed template
     * @var string
     */
    protected $template;

    /**
     * Constructor
     *
     * @param $file
     */
    public function __construct(File $file, Cache $cache)
    {
        $this->file = $file;
        $this->cache = $cache;
    }

    /**
     * Compile template and generate
     *
     * @param string $path Path to file
     * @param string $template Path to template
     * @param NameParser $mameparser parsed name
     * @return bool
     */
    public function make($path, $template, NameParser $mameparser)
    {
        $this->name = basename($path, '.php');
        $this->path = $this->getPath($path);
        $template = $this->getTemplate($template, $mameparser);

        if (! $this->file->exists($this->path))
        {
            return $this->file->put($this->path, $template) !== false;
        }

        return false;
    }

    /**
     * Adds namespace and use if needed
     *
     * @param NameParser $nameparser
     * @return mixed
     */
    protected function getNamespaced(NameParser $nameparser) {

        $namespace = '';

        if($nameparser->get('has_namespace')) {
            $namespace = ' namespace ' . $nameparser->get('namespace') . ';';
            //Remove {use} and {/use} tags
            $this->template = preg_replace('#\n?{/?use}#', '', $this->template);
        }else {
            //remove {use}{/use} content
            $this->template = preg_replace('#\n?{use}.*{/use}#', '', $this->template);
        }
        $this->template = str_replace('{{namespace}}', $namespace, $this->template);
    }

    /**
     * Get the path to the file
     * that should be generated
     *
     * @param  string $path
     * @return string
     */
    protected function getPath($path)
    {
        // By default, we won't do anything, but
        // it can be overridden from a child class
        return $path;
    }

    /**
     * Determines whether the specified template
     * points to the scaffolds directory
     *
     * @param  string $template
     * @return boolean
     */
    protected function needsScaffolding($template)
    {
        return str_contains($template, 'scaffold');
    }

    /**
     * Get compiled template
     *
     * @param  string $template
     * @param NameParser $nameparser Parsed name
     * @return string
     */
    abstract protected function getTemplate($template, NameParser $nameparser);
}