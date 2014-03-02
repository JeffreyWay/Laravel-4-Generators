<?php

namespace spec\Way\Generators;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Way\Generators\Filesystem\Filesystem;
use Way\Generators\Compilers\TemplateCompiler;

class GeneratorSpec extends ObjectBehavior {

    function let(Filesystem $file)
    {
        // By default, we'll set the file to not exist
        // This may be overridden, though
        $file->exists('foo.txt')->willReturn(false);

        $this->beConstructedWith($file);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Way\Generators\Generator');
    }

    function it_creates_a_file_with_given_text($file)
    {
        $file->make('foo.txt', 'bar')->shouldBeCalled();

        $this->generate('foo.txt', 'bar');
    }

    function it_will_not_overwrite_existing_files($file)
    {
        $file->exists('foo.txt')->willReturn(true);
        $file->make()->shouldNotBeCalled();

        $this->shouldThrow('Way\Generators\Filesystem\FileAlreadyExists')->duringGenerate('foo.txt', 'bar');
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
