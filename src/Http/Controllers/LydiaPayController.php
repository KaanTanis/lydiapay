<?php

namespace DataGrade\LydiaPay\Http\Controllers;

use DataGrade\LydiaPay\Payment;
use Illuminate\Http\Request;

class LydiaPayController extends Controller
{
    public function verify(Request $request, $driver)
    {
        $payment = new Payment();

        return $payment->verify($request, $driver);
    }
}
