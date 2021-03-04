<?php

namespace Tests\Unit;

use Exception;
use Tests\TestCase;
use App\Models\Sponsorable;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class SponsorableTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /** @test */
    function finding_a_sponsorable_by_slug()
    {
        $sponsorable = Sponsorable::factory()->create(['slug' => 'full-stack-radio']);

        $foundSponsorable = Sponsorable::findOrFailBySlug('full-stack-radio');

        $this->assertTrue($foundSponsorable->is($sponsorable));
    }

    /** @test */
    function an_exception_is_thrown_if_a_sponsorable_cannot_be_found_by_slug()
    {
        $this->expectException(ModelNotFoundException::class);
        Sponsorable::findOrFailBySlug('slug-that-does-not-exist');
    }
}
