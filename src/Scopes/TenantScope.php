<?php

namespace HenryAvila\LaravelNovaMultitenancy\Scopes;

use HenryAvila\LaravelNovaMultitenancy\Models\Tenant;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Auth;

class TenantScope implements Scope
{
    public function __construct(
        private readonly bool $restrictContentOwnedByUser = false,
        private readonly string $userFk = 'user_id'
    ) {
    }

    /**
     * Apply the scope to a given Eloquent query builder.
     */
    public function apply(Builder $builder, Model $model): void
    {
        $tenant = Tenant::current();
        $user = Auth::user();

        if ($tenant) {
            $builder->where('tenant_id', $tenant->id);
        }

        if ($this->restrictContentOwnedByUser && $user) {
            $builder->where($this->userFk, $user->id);
        }
    }
}
