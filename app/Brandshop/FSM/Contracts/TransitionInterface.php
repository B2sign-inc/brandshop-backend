<?php


namespace App\Brandshop\FSM\Contracts;


interface TransitionInterface
{
    /**
     * @return string
     */
    public function getName();

    /**
     * @return array
     */
    public function getFromStates();

    /**
     * Get State Name
     *
     * @return string
     */
    public function getToState();
}