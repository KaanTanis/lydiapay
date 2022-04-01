<?php

namespace DataGrade\LydiaPay\Drivers;

use DataGrade\LydiaPay\Abstracts\DriverAbstract;
use DataGrade\LydiaPay\Actions;
use DataGrade\LydiaPay\Models\LydiaPay;
use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Notlar: Iframe öderken kart bilgileri istenmez. User, sepet ve bakiye bilgileri iletilir.
 */
class Paytr extends DriverAbstract
{
    protected string $url;
    protected string $merchant_id; // Mağaza id
    protected string $merchant_key;
    protected string $merchant_salt;
    protected string $merchant_ok_url;
    protected string $merchant_fail_url;

    public function __construct()
    {
        $this->url = config('lydiapay.drivers.paytr.iframe_pay_url');
        $this->merchant_id = config('lydiapay.drivers.paytr.merchant_id');
        $this->merchant_key = config('lydiapay.drivers.paytr.merchant_key');
        $this->merchant_salt = config('lydiapay.drivers.paytr.merchant_salt');
        $this->merchant_ok_url = config('lydiapay.drivers.paytr.merchant_ok_url');
        $this->merchant_fail_url = config('lydiapay.drivers.paytr.merchant_fail_url');
    }

    /**
     * @throws Exception
     */
    public function pay($invoice)
    {
        $user_basket = base64_encode(json_encode(array(
            // todo: satın alınan ürünün adı ve fiyatını gönder
            array("Voiceover Service"), // 1. ürün (Ürün Ad - Birim Fiyat - Adet )
        )));

        $forToken = [
            'merchant_id' => $this->merchant_id,
            'user_ip' => $invoice->ip,
            'merchant_oid' => $invoice->uuid,
            'email' => $invoice->email,
            'payment_amount' => (int)$invoice->amount * 100,
            'user_basket' => $user_basket,
            'no_installment' => 1, // iframe Taksit görüntülenmesin
            'max_installment' => 0, // iframe En fazla taksit sayısı
            'currency' => 'TL',
            'test_mode' => (int)env('APP_DEBUG'),
        ];

        $token = base64_encode(hash_hmac(
            'sha256',
            implode('', $forToken) . $this->merchant_salt, $this->merchant_key,
            true
        ));

        $post_data = [
            'merchant_key' => $this->merchant_key,
            'merchant_salt' => $this->merchant_salt,
            'paytr_token' => $token,
            'user_name' => $invoice->name,
            'user_address' => $invoice->address ?? 'No address',
            'user_phone' => $invoice->phone ?? 'No phone',
            'merchant_ok_url' => $this->merchant_ok_url,
            'merchant_fail_url' => $this->merchant_fail_url,
            'debug_on' => (bool)env('APP_DEBUG'),
            'lang' => 'tr', // iframe
        ];

        $post_data = array_merge($post_data, $forToken);

        $response = Http::asForm()->post($this->url, $post_data)->json();

        if ($response['status'] != 'success') {
            throw new Exception("PAYTR IFRAME failed. reason:".$response['reason']);
        }

        $model = LydiaPay::create([
            'driver' => 'paytr',
            'order_id' => $post_data['merchant_oid'],
            'foreign_id' => $invoice->foreign_id,
            'user_id' => auth()->id() ?? null,
            'price' => $invoice->amount,
        ]);

        return $response['token'];
    }

    /**
     * @throws Exception
     */
    public function verify($request)
    {
        $hash = base64_encode( hash_hmac(
            'sha256',
            $request['merchant_oid'].$this->merchant_salt.$request['status'].$request['total_amount'], $this->merchant_key,
            true
        ));

        if($hash != $request->hash)
            die('PAYTR notification failed: bad hash');

        $order = $this->availableForApprove($request->merchant_oid);

        if ($order == 'is_approved' || $order == 'is_canceled') {
            echo 'OK';
            exit();
        }

        if ($request->status == 'success') {
            $action = new Actions();
            $action->addBalance($order);

            // Todo: confirmOrder action
        } else {
            $order->setCancel();
        }

        echo "OK";
        exit;
    }
}
