<?php

namespace spec\Way\Generators;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Way\Generators\Filesystem\Filesystem;
use Way\Generators\Compilers\TemplateCompiler;

class ModelGeneratorSpec extends ObjectBehavior {

    function let(Filesystem $file)
    {
        $this->beConstructedWith($file);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Way\Generators\ModelGenerator');
    }

    function it_compiles_a_template(Filesystem $file, TemplateCompiler $compiler)
    {
        $template = 'class $NAME$ {}';
        $data = ['NAME' => 'Bar'];
        $compiledTemplate = 'class Bar {}';

        $file->get('template.txt')->shouldBeCalled()->willReturn($template);
        $compiler->compile($template, $data)->shouldBeCalled()->willReturn($compiledTemplate);

        // When we call compile, we expect the method to
        // fetch the given template, compile it down,
        // and return the results
        $this->setTemplatePath('template.txt');
        $this->compile($data, $compiler)->shouldBe($compiledTemplate);
    }

    function it_must_receive_a_template_path_in_order_to_compile(TemplateCompiler $compiler)
    {
        $this->shouldThrow('Way\Generators\UndefinedTemplate')->duringCompile([], $compiler);
    }
}
