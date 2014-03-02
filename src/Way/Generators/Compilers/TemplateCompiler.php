<?php

namespace Way\Generators\Compilers;

class TemplateCompiler implements Compiler {

    public function compile($template, $data)
    {
        foreach($data as $key => $value)
        {
            $template = str_replace("\$$key\$", $value, $template);
        }

        return $template;
    }

}
