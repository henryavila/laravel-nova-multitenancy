<?php

namespace HenryAvila\LaravelNovaMultitenancy\Http\Middleware;

use Closure;
use HenryAvila\LaravelNovaMultitenancy\LaravelNovaMultitenancy;
use HenryAvila\LaravelNovaMultitenancy\Models\Tenant;
use Illuminate\Support\Facades\Auth;

class SetTenantMiddleware
{
    private $routesToIgnore = [
        'nova.pages.login',
        'nova.login',
        'nova.logout',
        'select-tenant',
        'select-tenant-store',
        'cant-select-tenant',
    ];

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $route = $request->route();
        $routeName = $route ? $route->getName() : null;

        // Don't run middleware if in any route marked to be ignored
        if (in_array($routeName, $this->routesToIgnore)) {
            return $next($request);
        }

        // If tenant is already defined don't run this middleware
        if (Tenant::current()) {
            return $next($request);
        }

        // Allow The super Admin to enter without a Tenant Selected
        $user = Auth::user();
        //		if ($user?->isSuperAdmin() || $request->getRequestUri() === '/select-tenant') {
        //			return $next($request);
        //		}

        // If the user came from Tenant Selection, let's get data from session and save the tenant
        $tenantId = session()->get(Tenant::TENANT_SELECTOR_SESSION_ID);

        if (! empty($tenantId)) {
            /** @var Tenant $tenant */
            $tenant = LaravelNovaMultitenancy::getTenantModel($tenantId);
            if (empty($tenant)) {
                abort(404);
            }

            $tenant->makeCurrent();

            return $next($request);
        }

        // From this point, we have sure that: we have no tenant selected and we are not in login or select tenant routes
        // (and not in any other route marked to be ignored)

        // The Tenant must be set just to users that can't manage Tenants
        $totalTenants = $user->tenants->count();

        if ($totalTenants === 1) {
            $user->tenants->first()->makeCurrent();

            return $next($request);
        }

        if ($totalTenants > 1) {
            return redirect()->route('select-tenant');
        }

        // In last case, lets Allow the superAdmin to login
        if ($user->isSuperAdmin()) {
            return $next($request);
        }

        Auth::logout();
        abort(403, __('Você precisa estar vinculado a alguma igreja.'));
    }
}
