<?php


namespace App\Brandshop\FSM\Callbacks;


use App\Brandshop\FSM\Contracts\CallbackInterface;
use App\Models\Order;

class AfterCancel implements CallbackInterface
{
    protected $order;

    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    public function handle()
    {

    }
}