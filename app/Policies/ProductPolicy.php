<?php

namespace App\Policies;

use App\Models\Product;
use App\Models\User;

class ProductPolicy
{
    public function viewAny(User $user)
    {
        // Example: check if user has 'products.view' ability
        return $user->hasAbility('products.view');
    }

    public function view(User $user, Product $product)
    {
        return $user->hasAbility('products.view');
    }

    public function create(User $user)
    {
        return $user->hasAbility('products.create');
    }

    public function update(User $user, Product $product)
    {
        return $user->hasAbility('products.update');
    }

    public function delete(User $user, Product $product)
    {
        return $user->hasAbility('products.delete');
    }
}
