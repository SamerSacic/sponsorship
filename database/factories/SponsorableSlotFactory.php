<?php

namespace Database\Factories;

use App\Models\Sponsorable;
use App\Models\SponsorableSlot;
use Illuminate\Database\Eloquent\Factories\Factory;

class SponsorableSlotFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = SponsorableSlot::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'price' => 10000,
            'publish_date' => now()->addMonths(),
        ];
    }
}
