<?php


namespace App\Brandshop\FSM;


use App\Brandshop\FSM\Contracts\StatableInterface;
use App\Brandshop\FSM\Contracts\StateInterface;
use App\Brandshop\FSM\Contracts\StateMachineInterface;
use App\Brandshop\FSM\Contracts\TransitionInterface;
use App\Brandshop\FSM\Event\TransitionEvent;
use App\Brandshop\FSM\Exceptions\DenyTransitionException;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Database\Eloquent\Model;

class StateMachine implements StateMachineInterface
{

    /**
     * @var Model
     */
    protected $model;

    /**
     * @var StateInterface
     */
    protected $currentState;

    /**
     * @var array
     */
    protected $states;

    /**
     * @var array
     */
    protected $transitions = [];


    public function __construct(Model $model)
    {
        if (!$model instanceof StatableInterface) {
            throw new \InvalidArgumentException(sprintf(
                    'Model %s must implement %s',
                    get_class($model),
                    StatableInterface::class)
            );
        }

        $this->model = $model;
    }


    /**
     * @param string $initialState
     */
    public function initialize($initialStateName)
    {
        if (!$state = $this->getState($initialStateName)) {
            throw new \UnexpectedValueException("can't find {$initialStateName} in states");
        }

        $this->currentState = $state;
    }

    /**
     * @param TransitionInterface $transition
     * @return mixed
     */
    public function addTransition(TransitionInterface $transition)
    {
        $this->transitions[$transition->getName()] = $transition;

        foreach ($transition->getFromStates() as $stateName) {
            if ($state = $this->getState($stateName)) {
                $state->addTransition($transition);
            }
        }

        return $this;
    }

    /**
     * @return array
     */
    public function getTransitions()
    {
        return $this->transitions;
    }

    /**
     * @param $stateName
     * @return State|null
     */
    public function getState($stateName)
    {
        return $this->states[$stateName] ?? null;
    }

    /**
     * @param StateInterface $state
     * @return mixed
     */
    public function addState(StateInterface $state)
    {
        $this->states[$state->getName()] = $state;
        return $this;
    }

    /**
     * @return Model
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * @param string|TransitionInterface $transition
     */
    public function apply($transition)
    {
        if (!$this->can($transition)) {
            throw new DenyTransitionException(sprintf(
                "Current State %s can't make Transition %s",
                $this->currentState->getName(),
                $transition instanceof TransitionInterface ? $transition->getName() : $transition
            ));
        }

        if (is_string($transition)) {
            $transition = $this->transitions[$transition];
        }

        $transitionEvent = new TransitionEvent($transition, $this->currentState, $this);

        event(TransitionEvent::PRE_TRANSITION, $transitionEvent);

        $this->setCurrentState($transition->getToState());
        $this->model->setAttribute($this->model->getStatePropertyName(), $transition->getToState());

        event(TransitionEvent::POST_TRANSITION, $transitionEvent);
    }

    /**
     * @param string|TransitionInterface $transition
     * @return boolean
     */
    public function can($transition)
    {
        return $this->currentState->can($transition);
    }

    /**
     * @return StateInterface
     */
    public function getCurrentState()
    {
        return $this->currentState;
    }

    public function setCurrentState($state)
    {
        if (!$state instanceof StateInterface) {
            $state = $this->states[$state];
        }

        $this->currentState = $state;

        return $this;
    }

    /**
     * @return array
     */
    public function getAvailableTransitions()
    {
        return $this->currentState->getTransitions();
    }

}