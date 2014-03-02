<?php namespace Way\Generators\Compilers;

interface Compiler {
    public function compile($template, $data);
} 