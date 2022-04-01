<?php

namespace DataGrade\LydiaPay\Abstracts;

use DataGrade\LydiaPay\Models\LydiaPay;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

abstract class DriverAbstract
{
    abstract public function pay($invoice);
    abstract public function verify($request);

    /**
     * @throws Exception
     */
    public function availableForApprove($order_id): mixed
    {
        $model = new LydiaPay();
        $order = $model->findByOrderId($order_id);

        if (! $order) {
            throw new Exception('No data for this order_number');
        }

        if ($order->status == LydiaPay::status_approved) {
            return 'is_approved';
        }

        if ($order->status == LydiaPay::status_canceled) {
            return 'is_canceled';
        }

        return $order;
    }
}
