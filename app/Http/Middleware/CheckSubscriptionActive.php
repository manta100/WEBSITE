<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckSubscriptionActive
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!tenancy()->initialized) {
            return $next($request);
        }

        $tenant = tenant();
        
        if (!$tenant) {
            return $next($request);
        }

        if (!$tenant->hasActiveSubscription() && !$tenant->isOnTrial()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'error' => 'Subscription required',
                    'message' => 'An active subscription is required to access this feature.',
                    'redirect' => route('tenant.subscription')
                ], 403);
            }

            return redirect()->route('tenant.subscription')
                ->with('error', 'An active subscription is required.');
        }

        return $next($request);
    }
}
