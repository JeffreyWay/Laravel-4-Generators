<?php

use Way\Generators\Generators\ModelGenerator;
use Mockery as m;

class ModelGeneratorTest extends PHPUnit_Framework_TestCase {
    protected static $templatesDir;

    public function __construct()
    {
        static::$templatesDir = __DIR__.'/../../src/Way/Generators/Generators/templates';
    }

    public function tearDown()
    {
        m::close();
    }

    public function testCanGenerateModelUsingTemplate()
    {
        $file = m::mock('Illuminate\Filesystem\Filesystem')->makePartial();

        $file->shouldReceive('put')
             ->once()
             ->with('app/models/Foo.php', file_get_contents(__DIR__.'/stubs/model.txt'));

        $generator = new ModelGenerator($file);
        $generator->make('app/models/Foo.php', static::$templatesDir.'/model.txt');
    }

    public function testCanGenerateModelUsingCustomTemplate()
    {
        $file = m::mock('Illuminate\Filesystem\Filesystem')->makePartial();

        $file->shouldReceive('put')
             ->once()
             ->with('app/models/Foo.php', file_get_contents(__DIR__.'/stubs/scaffold/model.txt'));

        $generator = new ModelGenerator($file);
        $generator->make('app/models/Foo.php', static::$templatesDir.'/scaffold/model.txt');
    }
}