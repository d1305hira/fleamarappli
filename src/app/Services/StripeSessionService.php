<?php

namespace App\Services;

use Stripe\Checkout\Session;

class StripeSessionService
{
    public function create(array $params)
    {
        return Session::create($params);
    }
}
