<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Sponsorship;
use App\Models\Sponsorable;
use App\Models\SponsorableSlot;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;

class ViewNewSponsorshipPageTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /** @test */
    function viewing_the_new_sponsorship_page()
    {
        $sponsorable = Sponsorable::factory()->create(['slug' => 'full-stack-radio']);

        $sponsorableSlots = new EloquentCollection([
            SponsorableSlot::factory()->create(['sponsorable_id' => $sponsorable, 'publish_date' => now()->addMonths(1)]),
            SponsorableSlot::factory()->create(['sponsorable_id' => $sponsorable, 'publish_date' => now()->addMonths(2)]),
            SponsorableSlot::factory()->create(['sponsorable_id' => $sponsorable, 'publish_date' => now()->addMonths(3)]),
        ]);

        $response = $this->get('/full-stack-radio/sponsorships/new');

        $response->assertSuccessful();
        $this->assertTrue($response->data('sponsorable')->is($sponsorable));
        $sponsorableSlots->assertEquals($response->data('sponsorableSlots'));
    }

    /** @test */
    function sponsorable_slots_are_listed_in_chronological_order()
    {
        $sponsorable = Sponsorable::factory()->create(['slug' => 'full-stack-radio']);

        $slotA =SponsorableSlot::factory()->create(['publish_date' => now()->addDays(10), 'sponsorable_id' => $sponsorable]);
        $slotB =SponsorableSlot::factory()->create(['publish_date' => now()->addDays(30), 'sponsorable_id' => $sponsorable]);
        $slotC =SponsorableSlot::factory()->create(['publish_date' => now()->addDays(3), 'sponsorable_id' => $sponsorable]);

        $response = $this->get('/full-stack-radio/sponsorships/new');

        $response->assertSuccessful();
        $this->assertTrue($response->data('sponsorable')->is($sponsorable));
        $this->assertCount(3, $response->data('sponsorableSlots'));
        $this->assertTrue($response->data('sponsorableSlots')[0]->is($slotC));
        $this->assertTrue($response->data('sponsorableSlots')[1]->is($slotA));
        $this->assertTrue($response->data('sponsorableSlots')[2]->is($slotB));
    }

    /** @test */
    function only_upcoming_sponsorable_slots_are_listed()
    {
        $sponsorable = Sponsorable::factory()->create(['slug' => 'full-stack-radio']);

        $slotA =SponsorableSlot::factory()->create(['publish_date' => now()->subDays(2), 'sponsorable_id' => $sponsorable]);
        $slotB =SponsorableSlot::factory()->create(['publish_date' => now()->subDays(10), 'sponsorable_id' => $sponsorable]);
        $slotC =SponsorableSlot::factory()->create(['publish_date' => now()->addDays(2), 'sponsorable_id' => $sponsorable]);
        $slotD =SponsorableSlot::factory()->create(['publish_date' => now()->addDays(10), 'sponsorable_id' => $sponsorable]);

        $response = $this->get('/full-stack-radio/sponsorships/new');

        $response->assertSuccessful();
        $this->assertTrue($response->data('sponsorable')->is($sponsorable));
        $this->assertCount(2, $response->data('sponsorableSlots'));
        $this->assertTrue($response->data('sponsorableSlots')[0]->is($slotC));
        $this->assertTrue($response->data('sponsorableSlots')[1]->is($slotD));
    }

    /** @test */
    function only_purchasable_sponsorable_slots_are_listed()
    {
        $sponsorable = Sponsorable::factory()->create(['slug' => 'full-stack-radio']);

        $sponsorship = Sponsorship::factory()->create();

        $slotA =SponsorableSlot::factory()->create(['sponsorable_id' => $sponsorable, 'publish_date' => now()->addMonths(1)]);
        $slotB =SponsorableSlot::factory()->create(['sponsorable_id' => $sponsorable, 'publish_date' => now()->addMonths(2), 'sponsorship_id' => $sponsorship]);
        $slotC =SponsorableSlot::factory()->create(['sponsorable_id' => $sponsorable, 'publish_date' => now()->addMonths(3), 'sponsorship_id' => $sponsorship]);
        $slotD =SponsorableSlot::factory()->create(['sponsorable_id' => $sponsorable, 'publish_date' => now()->addMonths(4)]);

        $response = $this->get('/full-stack-radio/sponsorships/new');

        $response->assertSuccessful();
        $this->assertTrue($response->data('sponsorable')->is($sponsorable));
        $this->assertCount(2, $response->data('sponsorableSlots'));
        $this->assertTrue($response->data('sponsorableSlots')[0]->is($slotA));
        $this->assertTrue($response->data('sponsorableSlots')[1]->is($slotD));
    }
}
