<?php

namespace HenryAvila\LaravelNovaMultitenancy;

use HenryAvila\LaravelNovaMultitenancy\Models\Tenant;
use Illuminate\Database\Eloquent\Model;

class LaravelNovaMultitenancy
{
	const SKIP_ROUTE = 'skipTenantSelection';

	/**
	 * @return Tenant
	 */
	public static function getTenantModel(int $id): Model
	{
		$tenantModelClass = config('nova-multitenancy.tenant_model');

		return $tenantModelClass::find($id);
	}
}
