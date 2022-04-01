<?php

namespace DataGrade\LydiaPay;

use DataGrade\LydiaPay\Models\LydiaPay;
use Exception;

class Actions
{
    protected mixed $balance;
    // confirmOrder

    public function __construct()
    {
        $this->balance = config('lydiapay.balance');
    }

    /**
     * @throws Exception
     */
    public function addBalance($order)
    {
        $user = new $this->balance['model'];
        $user = $user->find($order->user_id);

        if (! $user) {
            throw new Exception('User not found');
            // Log tut ve admini bilgilendir
        }

        $order->setApprove(); // siparişi onayla
        if ($order->status == LydiaPay::status_approved) { // Son kontrol sonrası bakiyeyi ekle
            $user->increment('balance', $order->price);
        }
    }

    // confirmOrder
}
