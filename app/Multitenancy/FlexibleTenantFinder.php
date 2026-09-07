<?php

namespace App\Multitenancy;

use App\Models\Tenant;
use Illuminate\Http\Request;
use Spatie\Multitenancy\TenantFinder\TenantFinder;

class FlexibleTenantFinder extends TenantFinder
{
    public function findForRequest(Request $request): ?Tenant
    {
        // 1. Check by X-Tenant header or query parameter
        $tenantId = $request->header('X-Tenant') ?? $request->query('tenant_id');
        if ($tenantId) {
            $tenant = Tenant::where('id', $tenantId)->first();
            if ($tenant) {
                return $tenant;
            }
        }

        // 2. Check by X-Tenant-Code header
        $tenantCode = $request->header('X-Tenant-Code') ?? $request->query('tenant_code');
        if ($tenantCode) {
            $tenant = Tenant::where('code', $tenantCode)->first();
            if ($tenant) {
                return $tenant;
            }
        }

        // 3. Check by Domain / Subdomain
        $host = $request->getHost();
        $tenant = Tenant::where('domain', $host)->first();
        if ($tenant) {
            return $tenant;
        }

        // 4. Check if authenticated user belongs to a tenant
        if ($user = $request->user()) {
            if (!empty($user->tenant)) {
                return Tenant::find($user->tenant);
            }
        }

        // 5. Fallback: Return default/first tenant if available (e.g. single tenant or development mode)
        return Tenant::where('is_active', true)->first();
    }
}
