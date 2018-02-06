<?php


namespace App\Brandshop\FSM;


use App\Brandshop\FSM\Contracts\StateInterface;
use App\Brandshop\FSM\Contracts\TransitionInterface;

class State implements StateInterface
{

    protected $name;

    protected $type;

    /**
     * @var array
     */
    protected $transitions;

    public function __construct($name, $type = self::TYPE_NORMAL, $transitions = [])
    {
        $this->name = $name;
        $this->type = $type;
        $this->transitions = $transitions;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return boolean
     */
    public function isInitial()
    {
        return $this->type === self::TYPE_INITIAL;
    }

    /**
     * @return boolean
     */
    public function isFinal()
    {
        return $this->type === self::TYPE_FINAL;
    }

    /**
     * @return boolean
     */
    public function isNormal()
    {
        return $this->type === self::TYPE_NORMAL;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Return the available transitions
     *
     * @return array
     */
    public function getTransitions()
    {
        return $this->transitions;
    }

    /**
     * @param string|TransitionInterface $transition
     */
    public function addTransition($transition)
    {
        if ($transition instanceof TransitionInterface) {
            $transition = $transition->getName();
        }

        $this->transitions[] = $transition;
    }

    /**
     * @param string|TransitionInterface $transition
     * @return boolean
     */
    public function can($transition)
    {
        if ($transition instanceof TransitionInterface) {
            $transition = $transition->getName();
        }

        return in_array($transition, $this->transitions);
    }
}