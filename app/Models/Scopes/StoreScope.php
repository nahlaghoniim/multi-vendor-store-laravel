<?php
namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class StoreScope implements Scope
{
    public function apply(Builder $builder, Model $model)
    {
        if (auth('admin')->check()) {
            return;
        }

        if (auth()->check() && auth()->user()->store_id) {
            $builder->where(
                $model->getTable() . '.store_id',
                auth()->user()->store_id
            );
            return;
        }

        if (auth()->check()) {
            abort(403, 'User is not assigned to any store.');
        }
    }
}
