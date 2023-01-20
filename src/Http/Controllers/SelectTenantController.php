<?php

namespace HenryAvila\LaravelNovaMultitenancy\Http\Controllers;

use HenryAvila\LaravelNovaMultitenancy\Models\Tenant;
use HenryAvila\LaravelNovaMultitenancy\Models\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class SelectTenantController extends Controller
{
    public static function store(Request $request)
    {
        $tenantId = $request->get('tenant');
        if (empty($tenantId) || (int) $tenantId === 0) {
            abort(404);
        }

        session([Tenant::TENANT_SELECTOR_SESSION_ID => $tenantId]);

        return redirect('/');
    }

    /**
     * Show the selection screen
     *
     * @param  Request  $request
     */
    public function index(Request $request)
    {
        /** @var User $user */
        $user = auth()->user();

        if ($user->isSuperAdmin()) {
            $build = Tenant::orderBy('name');
        } else {
            $build = $user->tenants()->orderBy('name');
        }

        $tenants = $build->pluck('name', 'id');

        return view(
            'nova-multitenancy::select-tenant',
            [
                'tenants' => $tenants,
            ]
        );
    }
}
