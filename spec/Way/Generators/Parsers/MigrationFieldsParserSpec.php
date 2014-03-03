<?php

namespace spec\Way\Generators\Parsers;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class MigrationFieldsParserSpec extends ObjectBehavior {

    function it_is_initializable()
    {
        $this->shouldHaveType('Way\Generators\Parsers\MigrationFieldsParser');
    }

    function it_parses_a_string_of_fields()
    {
        $this->parse('name:string')->shouldReturn([
            'name' => ['type' => 'string']
        ]);

        $this->parse('name:string, age:integer')->shouldReturn([
            'name' => ['type' => 'string'],
            'age'  => ['type' => 'integer']
        ]);

        $this->parse('name:string, age:integer')->shouldReturn([
            'name' => ['type' => 'string'],
            'age'  => ['type' => 'integer']
        ]);

        $this->parse('name:string:nullable, age:integer')->shouldReturn([
            'name' => ['type' => 'string', 'decorators' => ['nullable']],
            'age'  => ['type' => 'integer']
        ]);

        $this->parse('name:string(15):nullable')->shouldReturn([
            'name' => ['type' => 'string', 'args' => '15', 'decorators' => ['nullable']],
        ]);

        $this->parse('column:double(15,8):nullable:default(10), age:integer')->shouldReturn([
            'column' => ['type' => 'double', 'args' => '15,8', 'decorators' => ['nullable', 'default(10)']],
            'age'  => ['type' => 'integer']
        ]);
    }

}
