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
        'paid',
        'processed',
        'cancelled',
        'shipped',
        'delivered',
        'completed',
        'refunded',
    ];

    protected $transitions = [
        'process' => [
            'from' => ['created'],
            'to' => 'processed',
        ],
        'pay' => [
            'from' => ['created'],
            'to' => 'paid',
        ],
        'cancel' => [
            'from' => ['created', 'paid', 'processed'],
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
        'refund' => [
            'from' => ['delivered'],
            'to' => 'refunded',
        ],
    ];

    public function getState()
    {
        return $this->state;
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
        return 'state';
    }
}
