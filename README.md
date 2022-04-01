# LydiaPay

Virtual Pos Service Package

Documentation is being preparing

# Under Development
```php
    $invoice = new Invoice();

    $invoice->amount(1)
        ->card_name("PAYTR TEST")
        ->card_number("9792030394440796")
        ->card_cv2("000")
        ->card_year("24")
        ->card_month("12")
        ->foreign_id("1123")
        ->driver('paytr_direct');

    $payment = new Payment();

    // direct api için test
    return $payment->pay($invoice);

    // iframe için test
    /*return view('test', [
        'token' => $payment->pay($invoice)
    ]);*/
```
