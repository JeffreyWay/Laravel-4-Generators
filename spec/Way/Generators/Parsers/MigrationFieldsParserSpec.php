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
            'name' => 'string'
        ]);

        $this->parse('name:string, age:integer')->shouldReturn([
            'name' => 'string',
            'age'  => 'integer'
        ]);

        $this->parse('name:string,age : integer')->shouldReturn([
            'name' => 'string',
            'age'  => 'integer'
        ]);
    }

}
