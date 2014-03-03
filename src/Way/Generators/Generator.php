<?php namespace Way\Generators;

use Way\Generators\Filesystem\Filesystem;
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
     * Run the generator
     *
     * @param $templatePath
     * @param $templateData
     * @param $filePathToGenerate
     */
    public function make($templatePath, $templateData, $filePathToGenerate)
    {
        // We'll begin by setting the location
        // of the template for this file generation
        $this->setTemplatePath($templatePath);

        // Next, we need to compile the template, according
        // to the data that we provide it with.
        $template = $this->compile($templateData, new TemplateCompiler);

        // Now that we have the compiled template,
        // we can actually generate the file
        $this->file->make($filePathToGenerate, $template);
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

}
