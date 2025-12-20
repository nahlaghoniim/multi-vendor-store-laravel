<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Product;
use App\Models\Category;
use App\Models\Store;
use Bezhanov\Faker\Provider\Commerce;
use Illuminate\Support\Str;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition()
    {
        // Add the Commerce provider to Faker
        $this->faker->addProvider(new Commerce($this->faker));

        $category = Category::inRandomOrder()->first();
        $store    = Store::inRandomOrder()->first();

        $name = $this->faker->unique()->productName();

        return [
            'name'        => $name,
            'slug'        => Str::slug($name),
            'description' => $this->faker->realText(100),
            'price'       => $this->faker->randomFloat(2, 10, 200),
            'store_id'    => $store?->id,
            'category_id' => $category?->id,
        ];
    }
}

