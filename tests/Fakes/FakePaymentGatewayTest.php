<?php


namespace Tests\Fakes;

use Tests\TestCase;
use Tests\FakePaymentGateway;

class FakePaymentGatewayTest extends TestCase
{
    /** @test */
    function retrieving_charges()
    {
        $paymentGateway = new FakePaymentGateway;

        $paymentGateway->??;

        $charges = $paymentGateway->charges();
        $this->assertCount(3, $charges);
        $this->assertEquals(250, $charges->amount());
        $this->assertEquals(500, $charges->amount());
        $this->assertEquals(750, $charges->amount());
    }
}
