<?php


namespace Tests\Fakes;

use Tests\TestCase;
use Tests\FakePaymentGateway;
use App\Exceptions\PaymentFailedException;

class FakePaymentGatewayTest extends TestCase
{
    /** @test */
    public function retrieving_charges()
    {
        $paymentGateway = new FakePaymentGateway;

        $paymentGateway->charge('john@example.org', 25000, $paymentGateway->validTestToken(), 'Example description A');
        $paymentGateway->charge('jane@example.com', 50000, $paymentGateway->validTestToken(), 'Example description B');
        $paymentGateway->charge('jeff@example.net', 75000, $paymentGateway->validTestToken(), 'Example description C');

        $charges = $paymentGateway->charges();

        $this->assertCount(3, $charges);

        $this->assertEquals('john@example.org', $charges[0]->email());
        $this->assertEquals(25000, $charges[0]->amount());
        $this->assertEquals('Example description A', $charges[0]->description());

        $this->assertEquals('jane@example.com', $charges[1]->email());
        $this->assertEquals(50000, $charges[1]->amount());
        $this->assertEquals('Example description B', $charges[1]->description());

        $this->assertEquals('jeff@example.net', $charges[2]->email());
        $this->assertEquals(75000, $charges[2]->amount());
        $this->assertEquals('Example description C', $charges[2]->description());
    }

    /** @test */
    public function charging_requires_a_valid_payment_token()
    {
        $paymentGateway = new FakePaymentGateway;

        try {
            $paymentGateway->charge('john@example.org', 25000, 'invalid-payment-token', 'Example description');
            $this->fail("The charge succeeded even though the payment token was invalid");
        } catch (PaymentFailedException $e) {
            $charges = $paymentGateway->charges();
            $this->assertCount(0, $charges);
        }
    }
}
