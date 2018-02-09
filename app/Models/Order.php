<?php

namespace App\Models;

use App\Brandshop\FSM\Contracts\StatableInterface;
use App\Brandshop\FSM\Traits\Statable;
use Illuminate\Database\Eloquent\Model;

class Order extends Model implements StatableInterface
{
    use Statable;

    protected $fillable = ['state', 'shipping_address_id', 'billing_address_id', 'user_id', 'shipping_method_id', 'payment_id'];

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

    /**
     * @param User $user
     * @return array
     */
    public function syncToOrderProduct(User $user)
    {
        return array_map(function($item) use ($user) {
            /** @var $item Cart */
            return OrderProduct::create([
                'order_id' => $this->id,
                'product_id' => $item->product_id,
                'quantity' => $item->quantity,
                'user_id' => $user->id
            ]);

        }, $user->carts);
    }
}
