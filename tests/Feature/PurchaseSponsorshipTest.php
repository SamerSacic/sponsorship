<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Sponsorship;
use App\Models\Sponsorable;
use App\Models\SponsorableSlot;
use Tests\FakePaymentGateway;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PurchaseSponsorshipTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /** @test */
    function purchasing_available_sponsorships_slots()
    {
        $paymentGateway = $this->app->instance(PaymentGateway::class, new FakePaymentGateway);

        $sponsorable = Sponsorable::factory()->create(['slug' => 'full-stack-radio']);

        $slotA =SponsorableSlot::factory()->create(['price' => 500, 'sponsorable_id' => $sponsorable, 'publish_date' => now()->addMonths(1)]);
        $slotB =SponsorableSlot::factory()->create(['price' => 300, 'sponsorable_id' => $sponsorable, 'publish_date' => now()->addMonths(2)]);
        $slotC =SponsorableSlot::factory()->create(['price' => 250, 'sponsorable_id' => $sponsorable, 'publish_date' => now()->addMonths(3)]);

        $response = $this->postJson('/full-stack-radio/sponsorships', [
            'sponsorable_slots' => [
                $slotA->getKey(),
                $slotC->getKey(),
            ]
        ]);

        $response->assertStatus(201);

        $this->assertEquals(1, Sponsorship::count());
        $sponsorship = Sponsorship::first();

        $this->assertEquals($sponsorship->getKey() ,$slotA->fresh()->sponsorship_id);
        $this->assertEquals($sponsorship->getKey() ,$slotC->fresh()->sponsorship_id);

        $this->assertNull($slotB->fresh()->sponsorship_id);

        $this->assertCount(1, $paymentGateway->charges());
        $charge = $paymentGateway->charges()->first();
        $this->assertEquals(750, $charge->amount());
    }
}
