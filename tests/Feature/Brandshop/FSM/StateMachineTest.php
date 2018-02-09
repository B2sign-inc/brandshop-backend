<?php


namespace Tests\Feature\Brandshop\FSM;


use App\Brandshop\FSM\Event\TransitionEvent;
use App\Brandshop\FSM\Exceptions\DenyTransitionException;
use App\Brandshop\FSM\State;
use App\Brandshop\FSM\StateMachine;
use App\Brandshop\FSM\Transition;
use Illuminate\Database\Eloquent\Model;
use Tests\TestCase;

class StateMachineTest extends TestCase
{
    public function testModel()
    {
        $this->expectException(\InvalidArgumentException::class);
        new StateMachine($this->createMock(Model::class));
    }

    public function testInitializeWithInvalidStateName()
    {
        $this->expectException(\UnexpectedValueException::class);
        $stateMachine = new StateMachine(new StatableModel());
        $stateMachine->initialize('what');
    }

    public function testInitialize()
    {
        $state = new State('created');
        $stateMachine = new StateMachine(new StatableModel());
        $stateMachine->addState($state);
        $stateMachine->initialize('created');
        $this->assertEquals($state, $stateMachine->getCurrentState());
    }

    public function testAddTransition()
    {
        $state = new State('created');
        $transition = new Transition('process', ['created'], 'processed');


        $stateMachine = new StateMachine(new StatableModel());
        $stateMachine->addState($state);
        $stateMachine->addTransition($transition);

        $this->assertEquals($transition, $stateMachine->getTransitions()['process']);
        $this->assertEquals('process', $state->getTransitions()[0]);
    }

    public function testCan()
    {
        $state = new State('created');
        $transition = new Transition('process', ['created'], 'processed');
        $stateMachine = new StateMachine(new StatableModel());
        $stateMachine->addState($state);
        $stateMachine->addTransition($transition);
        $stateMachine->initialize('created');
        $this->assertTrue($stateMachine->can('process'));
        $this->assertFalse($stateMachine->can('hi'));
    }

    public function testDenyApply()
    {
        $this->expectException(DenyTransitionException::class);

        $stateMachine = new StateMachine(new StatableModel());
        $stateMachine->addState(new State('created'));
        $stateMachine->initialize('created');

        $stateMachine->apply('hello');
    }

    public function testApply()
    {
        $statableModel = $this->getMockBuilder(StatableModel::class)
            ->setMethods(['getStatePropertyName'])
            ->getMock();

        $statableModel->expects($this->once())
            ->method('getStatePropertyName')
            ->willReturn('status');

        $stateMachine = new StateMachine($statableModel);
        $stateMachine->addState(new State('created'));
        $stateMachine->addState(new State('done'));
        $stateMachine->addTransition(new Transition('process', ['created'], 'done'));
        $stateMachine->initialize('created');

        $this->expectsEvents([TransitionEvent::PRE_TRANSITION, TransitionEvent::POST_TRANSITION]);

        $this->assertEquals('created', $stateMachine->getCurrentState()->getName());

        $stateMachine->apply('process');

        $this->assertEquals('done', $stateMachine->getCurrentState()->getName());
        $this->assertEquals($statableModel->status, 'done');
    }

}