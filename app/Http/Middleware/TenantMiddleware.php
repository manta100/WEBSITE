<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Tenant;
use Stancl\Tenancy\Tenancy;

class TenantMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $host = $request->getHost();
        
        if ($host === config('app.url') || $host === 'www.' . parse_url(config('app.url'), PHP_URL_HOST)) {
            return $next($request);
        }

        $domain = str_replace('www.', '', $host);
        
        $tenant = Tenant::where('domain', $domain)
            ->orWhere('domain', $host)
            ->first();

        if (!$tenant) {
            return response()->json([
                'error' => 'Tenant not found',
                'message' => 'No business is registered with this domain.'
            ], 404);
        }

        if (!$tenant->is_active) {
            return response()->json([
                'error' => 'Tenant inactive',
                'message' => 'This business account has been suspended.'
            ], 403);
        }

        tenancy()->initialize($tenant);

        return $next($request);
    }
}
