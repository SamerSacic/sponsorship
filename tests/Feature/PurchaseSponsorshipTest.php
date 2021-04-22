<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Sponsorship;
use App\Models\Sponsorable;
use Tests\FakePaymentGateway;
use App\Models\SponsorableSlot;
use App\Contracts\PaymentGateway;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PurchaseSponsorshipTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /** @test */
    public function purchasing_available_sponsorships_slots()
    {
        $paymentGateway = $this->app->instance(PaymentGateway::class, new FakePaymentGateway);

        $sponsorable = Sponsorable::factory()->create(['slug' => 'full-stack-radio', 'name' => 'Full stack radio']);

        $slotA =SponsorableSlot::factory()->create(['price' => 50000, 'sponsorable_id' => $sponsorable, 'publish_date' => now()->addMonths(1)]);
        $slotB =SponsorableSlot::factory()->create(['price' => 30000, 'sponsorable_id' => $sponsorable, 'publish_date' => now()->addMonths(2)]);
        $slotC =SponsorableSlot::factory()->create(['price' => 25000, 'sponsorable_id' => $sponsorable, 'publish_date' => now()->addMonths(3)]);

        $response = $this->postJson('/full-stack-radio/sponsorships', [
            'email' => 'john@example.org',
            'company_name' => 'DigitalTechnoSoft Inc.',
            'payment_token' => $paymentGateway->validTestToken(),
            'sponsorable_slots' => [
                $slotA->getKey(),
                $slotC->getKey(),
            ]
        ]);

        $response->assertStatus(201);

        $this->assertEquals(1, Sponsorship::count());
        $sponsorship = Sponsorship::first();

        $this->assertEquals('john@example.org', $sponsorship->email);
        $this->assertEquals('DigitalTechnoSoft Inc.', $sponsorship->company_name);
        $this->assertEquals(75000, $sponsorship->amount);

        $this->assertEquals($sponsorship->getKey() ,$slotA->fresh()->sponsorship_id);
        $this->assertEquals($sponsorship->getKey() ,$slotC->fresh()->sponsorship_id);

        $this->assertNull($slotB->fresh()->sponsorship_id);

        $this->assertCount(1, $paymentGateway->charges());
        $charge = $paymentGateway->charges()->first();
        $this->assertEquals('john@example.org', $charge->email());
        $this->assertEquals(75000, $charge->amount());
        $this->assertEquals('Full stack radio sponsorship', $charge->description());
    }

    /** @test */
    public function sponsorship_is_not_created_if_payment_token_cannot_be_charged()
    {
        $paymentGateway = $this->app->instance(PaymentGateway::class, new FakePaymentGateway);

        $sponsorable = Sponsorable::factory()->create(['slug' => 'full-stack-radio']);

        $slot =SponsorableSlot::factory()->create(['price' => 50000, 'sponsorable_id' => $sponsorable, 'publish_date' => now()->addMonths(1)]);

        $response = $this->postJson('/full-stack-radio/sponsorships', [
            'email' => 'john@example.org',
            'company_name' => 'DigitalTechnoSoft Inc.',
            'payment_token' => 'not-a-valid-token',
            'sponsorable_slots' => [
                $slot->getKey(),
            ]
        ]);

        $response->assertStatus(422);

        $this->assertEquals(0, Sponsorship::count());
        $this->assertNull($slot->fresh()->sponsorship_id);
        $this->assertCount(0, $paymentGateway->charges());
    }

    /** @test */
    public function company_name_is_required()
    {
        $paymentGateway = $this->app->instance(PaymentGateway::class, new FakePaymentGateway);

        $sponsorable = Sponsorable::factory()->create(['slug' => 'full-stack-radio']);

        $slot =SponsorableSlot::factory()->create(['price' => 50000, 'sponsorable_id' => $sponsorable, 'publish_date' => now()->addMonths(1)]);

        $response = $this->withExceptionHandling()->postJson('/full-stack-radio/sponsorships', [
            'email' => 'john@example.org',
            'company_name' => '',
            'payment_token' => $paymentGateway->validTestToken(),
            'sponsorable_slots' => [
                $slot->getKey(),
            ]
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('company_name');

        $this->assertEquals(0, Sponsorship::count());
        $this->assertNull($slot->fresh()->sponsorship_id);
        $this->assertCount(0, $paymentGateway->charges());
    }

    /** @test */
    public function email_is_required()
    {
        $paymentGateway = $this->app->instance(PaymentGateway::class, new FakePaymentGateway);

        $sponsorable = Sponsorable::factory()->create(['slug' => 'full-stack-radio']);

        $slot =SponsorableSlot::factory()->create(['price' => 50000, 'sponsorable_id' => $sponsorable, 'publish_date' => now()->addMonths(1)]);

        $response = $this->withExceptionHandling()->postJson('/full-stack-radio/sponsorships', [
            'email' => '',
            'company_name' => 'DigitalSoftTech Inc.',
            'payment_token' => $paymentGateway->validTestToken(),
            'sponsorable_slots' => [
                $slot->getKey(),
            ]
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('email');

        $this->assertEquals(0, Sponsorship::count());
        $this->assertNull($slot->fresh()->sponsorship_id);
        $this->assertCount(0, $paymentGateway->charges());
    }

    /** @test */
    public function email_must_look_like_an_email()
    {
        $paymentGateway = $this->app->instance(PaymentGateway::class, new FakePaymentGateway);

        $sponsorable = Sponsorable::factory()->create(['slug' => 'full-stack-radio']);

        $slot =SponsorableSlot::factory()->create(['price' => 50000, 'sponsorable_id' => $sponsorable, 'publish_date' => now()->addMonths(1)]);

        $response = $this->withExceptionHandling()->postJson('/full-stack-radio/sponsorships', [
            'email' => 'not-a-valid-email',
            'company_name' => 'DigitalSoftTech Inc.',
            'payment_token' => $paymentGateway->validTestToken(),
            'sponsorable_slots' => [
                $slot->getKey(),
            ]
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('email');

        $this->assertEquals(0, Sponsorship::count());
        $this->assertNull($slot->fresh()->sponsorship_id);
        $this->assertCount(0, $paymentGateway->charges());
    }

    /** @test */
    public function payment_token_is_required()
    {
        $paymentGateway = $this->app->instance(PaymentGateway::class, new FakePaymentGateway);

        $sponsorable = Sponsorable::factory()->create(['slug' => 'full-stack-radio']);

        $slot =SponsorableSlot::factory()->create(['price' => 50000, 'sponsorable_id' => $sponsorable, 'publish_date' => now()->addMonths(1)]);

        $response = $this->withExceptionHandling()->postJson('/full-stack-radio/sponsorships', [
            'email' => 'jhon.doe@example.org',
            'company_name' => 'DigitalSoftTech Inc.',
            'payment_token' => null,
            'sponsorable_slots' => [
                $slot->getKey(),
            ]
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('payment_token');

        $this->assertEquals(0, Sponsorship::count());
        $this->assertNull($slot->fresh()->sponsorship_id);
        $this->assertCount(0, $paymentGateway->charges());
    }

    /** @test */
    public function sponsorable_slots_are_required()
    {
        $paymentGateway = $this->app->instance(PaymentGateway::class, new FakePaymentGateway);

        $sponsorable = Sponsorable::factory()->create(['slug' => 'full-stack-radio']);

        $slot =SponsorableSlot::factory()->create(['price' => 50000, 'sponsorable_id' => $sponsorable, 'publish_date' => now()->addMonths(1)]);

        $response = $this->withExceptionHandling()->postJson('/full-stack-radio/sponsorships', [
            'email' => 'jhon@example.org',
            'company_name' => 'DigitalSoftTech Inc.',
            'payment_token' => $paymentGateway->validTestToken(),
            'sponsorable_slots' => null,
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('sponsorable_slots');

        $this->assertEquals(0, Sponsorship::count());
        $this->assertNull($slot->fresh()->sponsorship_id);
        $this->assertCount(0, $paymentGateway->charges());
    }

    /** @test */
    public function sponsorable_slots_must_be_an_array()
    {
        $paymentGateway = $this->app->instance(PaymentGateway::class, new FakePaymentGateway);

        $sponsorable = Sponsorable::factory()->create(['slug' => 'full-stack-radio']);

        $slot =SponsorableSlot::factory()->create(['price' => 50000, 'sponsorable_id' => $sponsorable, 'publish_date' => now()->addMonths(1)]);

        $response = $this->withExceptionHandling()->postJson('/full-stack-radio/sponsorships', [
            'email' => 'jhon@example.org',
            'company_name' => 'DigitalSoftTech Inc.',
            'payment_token' => $paymentGateway->validTestToken(),
            'sponsorable_slots' => 'not-an-array',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('sponsorable_slots');

        $this->assertEquals(0, Sponsorship::count());
        $this->assertNull($slot->fresh()->sponsorship_id);
        $this->assertCount(0, $paymentGateway->charges());
    }

    /** @test */
    public function at_least_one_sponsorable_slot_must_be_provided()
    {
        $paymentGateway = $this->app->instance(PaymentGateway::class, new FakePaymentGateway);

        $sponsorable = Sponsorable::factory()->create(['slug' => 'full-stack-radio']);

        $slot =SponsorableSlot::factory()->create(['price' => 50000, 'sponsorable_id' => $sponsorable, 'publish_date' => now()->addMonths(1)]);

        $response = $this->withExceptionHandling()->postJson('/full-stack-radio/sponsorships', [
            'email' => 'jhon@example.org',
            'company_name' => 'DigitalSoftTech Inc.',
            'payment_token' => $paymentGateway->validTestToken(),
            'sponsorable_slots' => [],
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('sponsorable_slots');

        $this->assertEquals(0, Sponsorship::count());
        $this->assertNull($slot->fresh()->sponsorship_id);
        $this->assertCount(0, $paymentGateway->charges());
    }

    /** @test */
    public function sponsorable_slots_must_be_unique()
    {
        $paymentGateway = $this->app->instance(PaymentGateway::class, new FakePaymentGateway);

        $sponsorable = Sponsorable::factory()->create(['slug' => 'full-stack-radio']);

        $slot =SponsorableSlot::factory()->create(['price' => 50000, 'sponsorable_id' => $sponsorable, 'publish_date' => now()->addMonths(1)]);

        $response = $this->withExceptionHandling()->postJson('/full-stack-radio/sponsorships', [
            'email' => 'jhon@example.org',
            'company_name' => 'DigitalSoftTech Inc.',
            'payment_token' => $paymentGateway->validTestToken(),
            'sponsorable_slots' => [
                $slot->getKey(),
                $slot->getKey(),
            ],
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('sponsorable_slots');

        $this->assertEquals(0, Sponsorship::count());
        $this->assertNull($slot->fresh()->sponsorship_id);
        $this->assertCount(0, $paymentGateway->charges());
    }

    /** @test */
    public function cannot_sponsor_another_sponsorables_slots()
    {
        $paymentGateway = $this->app->instance(PaymentGateway::class, new FakePaymentGateway);

        $sponsorable = Sponsorable::factory()->create(['slug' => 'full-stack-radio']);
        $otherSponsorable = Sponsorable::factory()->create(['slug' => 'laravel-news']);

        $slotA =SponsorableSlot::factory()->create(['price' => 50000, 'sponsorable_id' => $sponsorable, 'publish_date' => now()->addMonths(1)]);
        $slotB =SponsorableSlot::factory()->create(['price' => 30000, 'sponsorable_id' => $sponsorable, 'publish_date' => now()->addMonths(2)]);
        $otherSlot =SponsorableSlot::factory()->create(['price' => 25000, 'sponsorable_id' => $otherSponsorable, 'publish_date' => now()->addMonths(3)]);

        $response = $this->withExceptionHandling()->postJson('/full-stack-radio/sponsorships', [
            'email' => 'jhon.doe@example.org',
            'company_name' => 'DigitalSoftTech Inc.',
            'payment_token' => $paymentGateway->validTestToken(),
            'sponsorable_slots' => [
                $slotA->getKey(),
                $slotB->getKey(),
                $otherSlot->getKey(),
            ]
        ]);

        $response->assertStatus(404);

        $this->assertEquals(0, Sponsorship::count());
        $this->assertNull($slotA->fresh()->sponsorship_id);
        $this->assertNull($slotB->fresh()->sponsorship_id);
        $this->assertNull($otherSlot->fresh()->sponsorship_id);
        $this->assertCount(0, $paymentGateway->charges());
    }

    /** @test */
    public function cannot_sponsor_slots_that_already_sponsored()
    {
        $paymentGateway = $this->app->instance(PaymentGateway::class, new FakePaymentGateway);

        $sponsorable = Sponsorable::factory()->create(['slug' => 'full-stack-radio']);
        $sponsorship = Sponsorship::factory()->create();

        $slotA =SponsorableSlot::factory()->create(['sponsorship_id' => null, 'price' => 50000, 'sponsorable_id' => $sponsorable, 'publish_date' => now()->addMonths(1)]);
        $slotB =SponsorableSlot::factory()->create(['sponsorship_id' => $sponsorship, 'price' => 30000, 'sponsorable_id' => $sponsorable, 'publish_date' => now()->addMonths(2)]);

        $response = $this->withExceptionHandling()->postJson('/full-stack-radio/sponsorships', [
            'email' => 'jhon.doe@example.org',
            'company_name' => 'DigitalSoftTech Inc.',
            'payment_token' => $paymentGateway->validTestToken(),
            'sponsorable_slots' => [
                $slotA->getKey(),
                $slotB->getKey(),
            ]
        ]);

        $response->assertStatus(404);

        $this->assertEquals(1, Sponsorship::count());
        $this->assertNull($slotA->fresh()->sponsorship_id);
        $this->assertEquals($sponsorship->getKey(), $slotB->fresh()->sponsorship_id);
        $this->assertCount(0, $paymentGateway->charges());
    }
}
