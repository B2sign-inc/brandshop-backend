<?php


namespace App\Brandshop\FSM\Contracts;


interface StatableInterface
{
    /**
     * @return array
     */
    public function getStates();

    /**
     * @return array
     */
    public function getTransitions();

    /**
     * Get current state
     *
     * @return string
     */
    public function getState();

    /**
     * Get state property name in database
     *
     * @return string
     */
    public function getStatePropertyName();
}