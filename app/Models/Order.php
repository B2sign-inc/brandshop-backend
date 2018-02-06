<?php

namespace App\Models;

use App\Brandshop\FSM\Contracts\StatableInterface;
use App\Brandshop\FSM\Traits\Statable;
use Illuminate\Database\Eloquent\Model;

class Order extends Model implements StatableInterface
{
    use Statable;

    protected $states = [
        'created',
        'processed',
        'cancelled',
        'shipped',
        'delivered',
        'completed',
        'returned',
    ];

    protected $transitions = [
        'process' => [
            'from' => ['created'],
            'to' => 'processed',
        ],
        'cancel' => [
            'from' => ['created', 'processed'],
            'to' => ['cancelled'],
        ],
        'ship' => [
            'from' => ['processed'],
            'to' => 'shipped',
        ],
        'deliver' => [
            'from' => ['shipped'],
            'to' => 'delivered',
        ],
        'return' => [
            'from' => ['delivered'],
            'to' => 'returned',
        ],
    ];

    public function getState()
    {
        return $this->status;
    }

    public function getStates()
    {
        return $this->states;
    }

    public function getTransitions()
    {
        return $this->transitions;
    }

    /**
     * Get state property name in database
     *
     * @return string
     */
    public function getStatePropertyName()
    {
        return 'status';
    }
}
