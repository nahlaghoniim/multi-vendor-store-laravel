<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Bezhanov\Faker\Provider\Commerce;
use Illuminate\Support\Str;
class CategoryFactory extends Factory
{
    public function definition(): array
    {
        // Register Commerce provider
        $this->faker->addProvider(new Commerce($this->faker));

        $name = $this->faker->unique()->department;

        return [
            'name' => ucfirst($name),
            'slug' => Str::slug($name),
            'description' => $this->faker->sentence(15),
            'image' => $this->faker->imageUrl(640, 480, 'business', true),
        ];
    }
}
