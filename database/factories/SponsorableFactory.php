<?php

namespace Database\Factories;

use App\Models\Sponsorable;
use Illuminate\Database\Eloquent\Factories\Factory;

class SponsorableFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Sponsorable::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => 'Example podcast name',
        ];
    }
}
