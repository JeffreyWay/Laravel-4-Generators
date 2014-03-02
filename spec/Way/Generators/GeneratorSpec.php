<?php

namespace spec\Way\Generators;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Way\Generators\Filesystem\Filesystem;

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

}
