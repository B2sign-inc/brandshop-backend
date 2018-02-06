<?php


namespace Tests\Feature\FSM\Traits;


use App\Brandshop\FSM\StateMachine;
use App\Brandshop\FSM\Traits\Statable;
use Tests\TestCase;

class StatableTest extends TestCase
{
    public function testTrue()
    {
        $this->assertTrue(true);
    }
}