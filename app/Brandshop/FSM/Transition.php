<?php


namespace App\Brandshop\FSM;


use App\Brandshop\FSM\Contracts\StateInterface;
use App\Brandshop\FSM\Contracts\StateMachineInterface;
use App\Brandshop\FSM\Contracts\TransitionInterface;

class Transition implements TransitionInterface
{

    /**
     * @var array
     */
    protected $from;

    /**
     * @var string
     */
    protected $to;

    /**
     * @var string
     */
    protected $name;

    public function __construct($name, $from, $to)
    {
        $this->name = $name;
        $this->from = $from;
        $this->to = $to;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return array
     */
    public function getFromStates()
    {
        return $this->from;
    }

    /**
     * Get State Name
     *
     * @return string
     */
    public function getToState()
    {
        return $this->to;
    }
}