<?php namespace Way\Generators;

use Way\Generators\Filesystem\Filesystem;
use Way\Generators\Filesystem\FileAlreadyExists;
use Way\Generators\Compilers\TemplateCompiler;
use Way\Generators\UndefinedTemplate;

class Generator {

    /**
     * @var Filesystem
     */
    protected $file;

    /**
     * @var string
     */
    protected $templatePath;

    /**
     * @param Filesystem $file
     */
    public function __construct(Filesystem $file)
    {
        $this->file = $file;
    }

    /**
     * Set the path to the template
     *
     * @param $templatePath
     */
    public function setTemplatePath($templatePath)
    {
        $this->templatePath = $templatePath;
    }

    /**
     * Get the path to the template
     *
     * @return mixed
     */
    public function getTemplatePath()
    {
        return $this->templatePath;
    }

    /**
     * Compile the file
     *
     * @param array $data
     * @param TemplateCompiler $compiler
     * @return mixed
     * @throws UndefinedTemplate
     */
    public function compile(array $data, TemplateCompiler $compiler)
    {
        if ( ! $this->templatePath) throw new UndefinedTemplate;

        $template = $this->file->get($this->templatePath);

        return $compiler->compile($template, $data);
    }

    /**
     * Generate the file
     *
     * @param $file
     * @param $content
     * @throws FileAlreadyExists
     */
    public function generate($file, $content)
    {
        $this->file->make($file, $content);
    }

}
