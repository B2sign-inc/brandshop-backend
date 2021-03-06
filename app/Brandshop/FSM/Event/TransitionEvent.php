<?php


namespace App\Brandshop\FSM\Event;


use App\Brandshop\FSM\Contracts\StateInterface;
use App\Brandshop\FSM\Contracts\StateMachineInterface;
use App\Brandshop\FSM\Contracts\TransitionInterface;

class TransitionEvent
{
    const PRE_TRANSITION = 'FSM.PRE_TRANSITION';
    const POST_TRANSITION = 'FSM.POST_TRANSITION';

    /**
     * @var TransitionInterface
     */
    protected $transition;

    /**
     * @var StateInterface
     */
    protected $fromState;

    /**
     * @var StateMachineInterface
     */
    protected $stateMachine;

    public function __construct(TransitionInterface $transition, StateInterface $fromState, StateMachineInterface $stateMachine)
    {
       $this->transition = $transition;
       $this->fromState = $fromState;
       $this->stateMachine = $stateMachine;
    }

    /**
     * @return TransitionInterface
     */
    public function getTransition()
    {
        return $this->transition;
    }

    /**
     * @return StateInterface
     */
    public function getFromState()
    {
        return $this->fromState;
    }

    /**
     * @return StateMachineInterface
     */
    public function getStateMachine()
    {
        return $this->stateMachine;
    }
}