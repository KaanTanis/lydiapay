<?php

namespace DataGrade\LydiaPay\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LydiaPay extends Model
{
    use HasFactory;

    protected $guarded = [];
    protected $casts = [
        'response' => 'array'
    ];

    const status_waiting = 1; // bekliyor
    const status_approved = 2; // onaylandı
    const status_canceled = 3; // iptal edildi
    const status_refund = 4; // geri iade edildi (işlem iptal)

    public function approve($order_id)
    {
        $this->where('order_id', $order_id)->first()
            ->update(['status' => self::status_approved]);
    }

    public function setApprove()
    {
        $this->update(['status' => self::status_approved]);
    }

    public function cancel($order_id)
    {
        $this->where('order_id', $order_id)->first()
            ->update(['status' => self::status_canceled]);
    }

    public function setCancel()
    {
        $this->update(['status' => self::status_canceled]);
    }

    public function findByOrderId($order_id)
    {
        return $this->where('order_id', $order_id)->first();
    }
}

