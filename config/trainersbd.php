<?php

return [
    /*
    | Checkout: require the sender number and transaction ID?
    | Set PAYMENT_FIELDS_REQUIRED=false in .env only while testing orders.
    */
    'payment_fields_required' => env('PAYMENT_FIELDS_REQUIRED', true),
];
