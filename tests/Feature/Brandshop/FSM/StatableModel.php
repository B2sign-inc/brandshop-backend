<?php


namespace Tests\Feature\Brandshop\FSM;


use App\Brandshop\FSM\Contracts\StatableInterface;
use Illuminate\Database\Eloquent\Model;

class StatableModel extends Model implements StatableInterface
{

    /**
     * @return array
     */
    public function getStates()
    {
        // TODO: Implement getStates() method.
    }

    /**
     * @return array
     */
    public function getTransitions()
    {
        // TODO: Implement getTransitions() method.
    }

    /**
     * @return string
     */
    public function getState()
    {
        // TODO: Implement getCurrentState() method.
    }

    /**
     * Get state property name in database
     *
     * @return string
     */
    public function getStatePropertyName()
    {
        // TODO: Implement getStatePropertyName() method.
    }
}