<?php

namespace HenryAvila\LaravelNovaMultitenancy;

use Illuminate\Database\Eloquent\Model;

class LaravelNovaMultitenancy
{
	const SKIP_ROUTE = 'skipTenantSelection';

	public static function getTenantModel(int $id): ?Model
	{
		$tenantModelClass = config('nova-multitenancy.tenant_model');

		return $tenantModelClass::find($id);
	}
}
