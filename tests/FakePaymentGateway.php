<?php


namespace Tests;


use App\Exceptions\PaymentFailedException;
use App\Models\Charge;
use Illuminate\Support\Collection;


class FakePaymentGateway
{
    private Collection $charges;

    public function __construct()
    {
        $this->charges = new Collection;
    }

    public function validTestToken(): string
    {
        return 'valid_test_token';
    }

    public function charge($email, $amount, $token, $description)
    {
        if ($token !== $this->validTestToken()) {
            throw new PaymentFailedException;
        }
        $this->charges->push(new Charge($email, $amount, $description));
    }

    public function charges(): Collection
    {
        return $this->charges;
    }
}
