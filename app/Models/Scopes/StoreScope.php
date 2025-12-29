<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Auth;

class StoreScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $builder
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return void
     */
    public function apply(Builder $builder, Model $model): void
    {
        $user = Auth::user();

        // No authenticated user → do nothing (CLI, jobs, seeds)
        if (!$user) {
            return;
        }

        // Super admin should see all stores
        if ($user->type === 'super-admin') {
            return;
        }

        // Vendor/admin MUST have store_id
        if (!$user->store_id) {
            abort(403, 'User is not assigned to any store.');
        }

        $builder->where(
            $model->getTable() . '.store_id',
            $user->store_id
        );
    }

}
