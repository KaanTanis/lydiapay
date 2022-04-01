<?php

namespace DataGrade\LydiaPay;

use Exception;

class Payment
{
    /**
     * @throws Exception
     */
    public function pay($invoice)
    {
        $driver = $this->checkDriver($invoice->driver);
        $driverClass = new $driver();

        $defaultFields = [
            'name' => auth()->user()->getFullName ?? auth()->user()->name,
            'email' => auth()->user()->email ?? null,
            'phone' => auth()->user()->phone ?? '111 222 33 44',
            'address' => auth()->user()->address ?? null,
            'ip' => request()->ip() ?? null,
        ];

        foreach ($defaultFields as $k => $v) {
            $invoice->$k = $v;
        }

        return $driverClass->pay($invoice);
    }

    /**
     * @throws Exception
     */
    public function verify($request, $driver)
    {
        $driver = $this->checkDriver($driver);
        $driverClass = new $driver();
        return $driverClass->verify($request);
    }

    /**
     * @throws Exception
     */
    public function checkDriver($driver)
    {
        if (! array_key_exists($driver, config('lydiapay.map'))) {
            throw new Exception('Driver not found');
        }

        return config('lydiapay.map.' . $driver);
    }
}
