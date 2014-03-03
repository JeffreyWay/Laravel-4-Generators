<?php

namespace spec\Way\Generators;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class MigrationNameParserSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Way\Generators\MigrationNameParser');
    }

    function it_parses_a_basic_migration_name()
    {
        $this->parse('create_orders_table')->shouldBe([
            'action' => 'create',
            'tableName' => 'orders'
        ]);
    }

    function it_parses_a_complex_migration_name()
    {
        $this->parse('add_first_name_and_last_name_to_recent_orders_table')->shouldBe([
            'action' => 'add',
            'tableName' => 'recent_orders'
        ]);
    }

    function it_requires_a_valid_crud_action_name()
    {
        $this->shouldThrow('Way\Generators\InvalidActionType')->duringParse('foo_orders_table');
    }

}
