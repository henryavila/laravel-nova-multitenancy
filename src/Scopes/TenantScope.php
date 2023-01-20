<?php

namespace HenryAvila\LaravelNovaMultitenancy\Scopes;

use HenryAvila\LaravelNovaMultitenancy\Models\Tenant;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class TenantScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     *
     * @param Builder $builder
     * @param Model $model
     * @return void
     */
    public function apply(Builder $builder, Model $model)
    {
        $tenant = Tenant::current();

        if ($tenant) {
            $builder->where('tenant_id', $tenant->id);
        }
    }
}