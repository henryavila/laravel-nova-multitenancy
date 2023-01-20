<?php

namespace HenryAvila\LaravelNovaMultitenancy\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @property array<User>|Collection $tenants
 */
class Tenant extends \Spatie\Multitenancy\Models\Tenant
{
	const TENANT_SELECTOR_SESSION_ID = 'tenant-id-selector';

	/**
	 * Is there any Tenant selected
	 */
	public static function hasSelected(): bool
	{
		return Tenant::checkCurrent();
	}

	public function users(): BelongsToMany
	{
		return $this->belongsToMany(User::class)
			->withPivot('primary');
	}

}
