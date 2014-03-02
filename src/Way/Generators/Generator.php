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

    protected $templatePath;

    public function __construct(Filesystem $file)
    {
        $this->file = $file;
    }

    public function setTemplatePath($templatePath)
    {
        $this->templatePath = $templatePath;
    }

    public function getTemplatePath()
    {
        return $this->templatePath;
    }

    public function compile(array $data, TemplateCompiler $compiler)
    {
        if ( ! $this->templatePath) throw new UndefinedTemplate;

        $template = $this->file->get($this->templatePath);

        return $compiler->compile($template, $data);
    }

    public function generate($file, $content)
    {
        if ( $this->file->exists($file))
        {
            throw new FileAlreadyExists;
        }

        $this->file->make($file, $content);
    }
}
