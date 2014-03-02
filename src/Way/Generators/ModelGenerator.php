<?php namespace Way\Generators;

use Way\Generators\Compilers\TemplateCompiler;
use Way\Generators\UndefinedTemplate;

class ModelGenerator extends Generator {

    protected $templatePath;

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

}
