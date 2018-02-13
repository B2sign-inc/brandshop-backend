<?php

namespace App\Models;

use App\Brandshop\FSM\Callbacks\AfterCancel;
use App\Brandshop\FSM\Contracts\StatableInterface;
use App\Brandshop\FSM\Traits\Statable;
use App\Events\FSM\Order\AfterCancelEvent;
use Illuminate\Database\Eloquent\Model;

class Order extends Model implements StatableInterface
{
    use Statable;

    protected $casts = [
        'user_id' => 'int',
    ];

    protected $fillable = ['state', 'shipping_address_id', 'billing_address_id', 'user_id', 'shipping_method_id', 'payment_id', 'amount'];

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
            'callbacks' => [
                'after' => AfterCancel::class,
            ]
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

    public function orderProducts()
    {
        return $this->hasMany(OrderProduct::class);
    }

    /**
     * @return array
     */
    public function syncToOrderProduct($carts)
    {
        $orderProducts = $carts->map(function($item) {
            /** @var $item Cart */
            return OrderProduct::create([
                'order_id' => $this->id,
                'product_id' => $item->product_id,
                'quantity' => $item->quantity,
            ]);
        });

        Cart::destroy($carts->pluck('id')->all());

        return $orderProducts;
    }
}
