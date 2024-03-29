<?php

namespace HenryAvila\LaravelNovaMultitenancy\Models;

use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @property array<Tenant>|Collection $tenants
 */
class User extends \Illuminate\Foundation\Auth\User
{
    public function canSelectTenant(): bool
    {
        return $this->isSuperAdmin() || $this->tenants()->count() > 1;
    }

    public function isSuperAdmin(): bool
    {
        throw new Exception('You must implement this method.');
    }

    public function tenants(): BelongsToMany
    {
        return $this->belongsToMany(Tenant::class)
            ->withPivot('primary');
    }

    public function getActualUserTenant(): ?Tenant
    {
        $currentTenant = Tenant::current();

        foreach ($this->tenants as $tenant) {
            if ($tenant->id === $currentTenant->id && $this->userBelongsToTenant($tenant)) {
                return $tenant;
            }
        }

        return null;
    }

    public function userBelongsToTenant(Tenant $tenant): bool
    {
        return $this->belongsToTenantId($tenant->id);
    }

    public function belongsToTenantId(?int $tenantId): bool
    {
        if ($tenantId === null) {
            return false;
        }

        return $this->tenants()->pluck('id')->contains($tenantId);
    }

    public function belongsToCurrentTenant(): bool
    {
        return $this->belongsToTenantId(Tenant::current()->id);
    }

    public function userAndModelBelongsToActiveTenant(Model $model): bool
    {
        $belongsToCurrentTenant = $this->belongsToCurrentTenant();

        if (method_exists($model, 'tenants')) {
            return $belongsToCurrentTenant && $model->tenants()->pluck('id')->contains(Tenant::current()->id);
        }
        if (method_exists($model, 'tenant')) {
            return $belongsToCurrentTenant && $model->tenant?->id === Tenant::current()->id;
        }

        return $belongsToCurrentTenant;
    }
}
