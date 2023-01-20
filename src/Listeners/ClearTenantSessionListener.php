<?php

namespace HenryAvila\LaravelNovaMultitenancy\Listeners;

use HenryAvila\LaravelNovaMultitenancy\Models\Tenant;

class ClearTenantSessionListener
{
    public function handle($event)
    {
        session()->remove(Tenant::TENANT_SELECTOR_SESSION_ID);
    }
}
