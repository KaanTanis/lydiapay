<?php

namespace DataGrade\LydiaPay\Drivers;

use DataGrade\LydiaPay\Abstracts\DriverAbstract;
use DataGrade\LydiaPay\Actions;
use DataGrade\LydiaPay\Models\LydiaPay;
use Exception;
use Illuminate\Support\Facades\Http;

class Ziraat3D extends DriverAbstract
{
    protected string $clientid;
    protected string $ok_url;
    protected string $fail_url;
    protected string $storetype;
    protected string $storekey;
    protected string $pay_url;

    public function __construct()
    {
        $this->clientid = config('lydiapay.drivers.ziraat_3d.clientid');
        $this->ok_url = config('lydiapay.drivers.ziraat_3d.ok_url');
        $this->fail_url = config('lydiapay.drivers.ziraat_3d.fail_url');
        $this->storetype = config('lydiapay.drivers.ziraat_3d.storetype');
        $this->storekey = config('lydiapay.drivers.ziraat_3d.storekey');
        $this->pay_url = config('lydiapay.drivers.ziraat_3d.pay_url');
    }

    public function pay($invoice)
    {
        $forToken = [
            'clientid' => $this->clientid,
            'oid' => $invoice->uuid,
            'amount' => (int)$invoice->amount,
            'okUrl' => $this->ok_url,
            'failUrl' => $this->fail_url,
            'islemtipi' => 'Auth',
            'taksit' => null,
            'rnd' => microtime(),
            'store_key' => $this->storekey,
        ];

        $hash = base64_encode(pack('H*', sha1(implode('', $forToken))));

        $post_data = [
            'pan' => $invoice->card_number,
            'cv2' => $invoice->card_cv2,
            'Ecom_Payment_Card_ExpDate_Year' => $invoice->card_year,
            'Ecom_Payment_Card_ExpDate_Month' => $invoice->card_month,
            'cardType' => '2',
            'storetype' => $this->storetype,
            'lang' => 'tr',
            'hash' => $hash,
            'currency' => 949,
        ];

        $post_data = array_merge($post_data, $forToken);

        $response = Http::asForm()->post($this->pay_url, $post_data);

        $model = LydiaPay::query()->create([
            'driver' => 'ziraat',
            'price' => $invoice->amount,
            'order_id' => $post_data['oid'],
            'foreign_id' => $invoice->foreign_id,
            'user_id' => auth()->id(),
        ]);

        return $response->body();
    }

    /**
     * @throws Exception
     */
    public function verify($request): string
    {
        $hash = base64_encode(pack(
            'H*',
            sha1($this->clientid.$request->oid.$request->AuthCode.$request->ProcReturnCode.
                $request->Response.$request->rnd.$this->storekey)
        ));

        if ($hash != $request->HASH)
            die('Bad hash');

        $order = $this->availableForApprove($request->oid);

        if ($order == 'is_approved') {
            return __('Already approved');
        }

        if ($order == 'is_canceled') {
            return __('Already canceled');
        }

        if ($request->Response == 'Approved') {
            $action = new Actions();
            $action->addBalance($order);
            return 'ok';
        } else {
            $order->setCancel();
            return 'no';
        }
    }
}
