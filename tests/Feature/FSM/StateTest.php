<?php


namespace Tests\Feature\FSM;


use App\Brandshop\FSM\State;
use Tests\TestCase;

class StateTest extends TestCase
{
    public function testAddTransition()
    {
        $state = new State('test');
        $this->assertEquals([], $state->getTransitions());

        $state->addTransition('cancel');
        $this->assertEquals('cancel', $state->getTransitions()[0]);

        $state->addTransition('ship');
        $this->assertEquals('ship', $state->getTransitions()[1]);
    }

    public function testCan()
    {
        $state = new State('test');
        $state->addTransition('ship');
        $this->assertTrue($state->can('ship'));
        $this->assertFalse($state->can('test'));
    }
}