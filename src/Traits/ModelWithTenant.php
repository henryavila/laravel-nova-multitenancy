<?php

namespace HenryAvila\LaravelNovaMultitenancy\Traits;

use HenryAvila\LaravelNovaMultitenancy\Observers\ModelWithTenantObserver;
use HenryAvila\LaravelNovaMultitenancy\Scopes\TenantScope;

/**
 * Trait ModelWithTenant
 *
 * Used to manage the Tenant Specific content
 *
 * @property int tenant_id
 */
trait ModelWithTenant
{
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->setConnection(config('database.tenant_connection'));
    }

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope(new TenantScope());
        static::observe(new ModelWithTenantObserver());
    }

    public function tenant()
    {
        return $this->belongsTo(config('nova-multitenancy.tenant_model'));
    }
}
