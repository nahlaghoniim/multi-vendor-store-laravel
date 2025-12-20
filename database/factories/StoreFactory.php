<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class StoreFactory extends Factory
{
    public function definition()
    {
        $name = $this->faker->unique()->company();

        return [
            'name'        => $name,
            'slug'        => Str::slug($name),
            'description' => $this->faker->sentence(15),
            'logo_image'  => $this->faker->imageUrl(300, 300, 'business', true),
            'cover_image' => $this->faker->imageUrl(800, 600, 'business', true),
        ];
    }
}
