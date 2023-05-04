<?php

namespace HenryAvila\LaravelNovaMultitenancy\Http\Middleware;

use Closure;
use HenryAvila\LaravelNovaMultitenancy\LaravelNovaMultitenancy;
use HenryAvila\LaravelNovaMultitenancy\Models\Tenant;
use Illuminate\Support\Facades\Auth;

class SetTenantMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $route = $request->route();
        $routeName = $route ? $route->getName() : null;

        // Don't run middleware if in any route marked to be ignored
        if (in_array($routeName, config('nova-multitenancy.routes_to_ignore'))) {
            return $next($request);
        }

        $skipRoute = LaravelNovaMultitenancy::SKIP_ROUTE;
        if (isset($route->defaults[$skipRoute]) && $route->defaults[$skipRoute] === true) {
            return $next($request);
        }

        // If tenant is already defined don't run this middleware
        if (Tenant::current() !== null) {
            return $next($request);
        }

        // Set tenant based on Domain
        $domainClass = config('nova-multitenancy.domain_class');
        if (! empty($domainClass)) {
            $host = $request->getHost();
            $fqdn = config('nova-multitenancy.fqdn_column') ?? 'fqdn';

            $tenantByDomain = $domainClass::where($fqdn, $host)->first()?->tenant;

            if (! empty($tenantByDomain)) {
                $this->selectTenant($tenantByDomain);

                return $next($request);
            }
        }

        // If the user came from Tenant Selection, let's get data from session and save the tenant
        $tenantId = session()->get(Tenant::TENANT_SELECTOR_SESSION_ID);

        if (! empty($tenantId)) {
            /** @var Tenant $tenant */
            $tenant = LaravelNovaMultitenancy::getTenantModel($tenantId);
            if (empty($tenant)) {
                abort(404);
            }

            $this->selectTenant($tenant);

            return $next($request);
        }

        // From this point, we have sure that: we have no tenant selected and we are not in login or select tenant routes
        // (and not in any other route marked to be ignored)

        // Allow The super Admin to enter without a Tenant Selected
        $user = Auth::user();

        // The Tenant must be set just to users that can't manage Tenants
        $totalTenants = $user?->tenants->count() ?? 0;

        if ($totalTenants === 1) {
            $this->selectTenant($user->tenants->first());

            return $next($request);
        }

        if ($user?->isSuperAdmin() || $totalTenants > 1) {
            return redirect()->route('select-tenant');
        }

        Auth::logout();
        abort(403, config('nova-multitenancy.tenant_not_found_message'));
    }

    private function selectTenant(Tenant $tenant): void
    {
        $tenant->makeCurrent();

        try {
            $invokable = config('nova-multitenancy.invoke_after_tenant_selection');

            if (class_exists($invokable)) {
                (new $invokable)();
            }

        } catch (\Exception) {
        }

    }
}
