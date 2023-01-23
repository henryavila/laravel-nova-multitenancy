<?php

namespace HenryAvila\LaravelNovaMultitenancy\Models;

use Exception;
use Illuminate\Database\Eloquent\Collection;
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

	/**
	 * @return Tenant|null
	 */
	public function getActualUserTenant()
	{
		$tenant = Tenant::current();

		if ($this->userBelongsToTenant($tenant)) {
			return $tenant;
		}

		return null;
	}

	public function userBelongsToTenant(Tenant $tenant): bool
	{
		return $this->userBelongsToTenantId($tenant->id);
	}

	public function userBelongsToTenantId(int $tenantId): bool
	{
		return $this->tenants()->pluck('id')->contains($tenantId);
	}

	public function belongsToCurrentTenant(): bool
	{
		return $this->tenants()->pluck('id')->contains(Tenant::current()->id);
	}
}
