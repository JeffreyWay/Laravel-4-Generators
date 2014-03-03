<?php

namespace spec\Way\Generators;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Way\Generators\Compilers\TemplateCompiler;
use Way\Generators\Filesystem\Filesystem;

class SchemaCreatorSpec extends ObjectBehavior {

    public function let(Filesystem $file, TemplateCompiler $compiler)
    {
        $this->beConstructedWith($file, $compiler);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Way\Generators\SchemaCreator');
    }

    function it_creates_the_code_for_a_migration_up_method($file, $compiler)
    {
        $migrationData = [
            'action' => 'create',
            'table'  => 'orders',
            'method' => 'create'
        ];

        $file->get(Argument::any())->shouldBeCalled()->willReturn('foo template');
        $compiler->compile('foo template', $migrationData)->shouldBeCalled();

        $this->up($migrationData);
    }

}
