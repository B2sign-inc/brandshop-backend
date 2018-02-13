<?php


namespace App\Brandshop\FSM\Traits;


use App\Brandshop\FSM\State;
use App\Brandshop\FSM\StateMachine;
use App\Brandshop\FSM\Transition;

trait Statable
{
    /**
     * @var StateMachine
     */
    protected $stateMachine;

    public function stateMachine()
    {
        if (!$this->stateMachine) {
            $this->stateMachine = new StateMachine($this);

            foreach ($this->getStates() as $state) {
                $this->stateMachine->addState(new State($state));
            }

            foreach ($this->getTransitions() as $transitionName => $transition) {
                $this->stateMachine->addTransition(new Transition(
                    $transitionName,
                    $transition['from'],
                    $transition['to'],
                    $transition['callbacks'] ?? []
                ));
            }

            // State is null while first time to use state machine
            $this->getState() && $this->stateMachine->initialize($this->getState());
        }

        return $this->stateMachine;
    }

    public function canTransition($transition)
    {
        return $this->stateMachine()->can($transition);
    }

    public function transition($transition)
    {
        $this->stateMachine()->apply($transition);
    }

    public function getCurrentState()
    {
        return $this->stateMachine()->getCurrentState();
    }
}