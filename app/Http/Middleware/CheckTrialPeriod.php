<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Stancl\Tenancy\Tenancy;

class CheckTrialPeriod
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

        if ($tenant->hasActiveSubscription()) {
            return $next($request);
        }

        if ($tenant->isOnTrial()) {
            $daysLeft = now()->diffInDays($tenant->trial_ends_at, false);
            
            if ($daysLeft <= 1) {
                session()->flash('warning', 'Your trial ends in ' . round($daysLeft) . ' day(s). Please subscribe to continue.');
            }
            
            return $next($request);
        }

        if ($request->expectsJson()) {
            return response()->json([
                'error' => 'Trial expired',
                'message' => 'Your trial period has ended. Please subscribe to continue using the POS system.',
                'redirect' => route('tenant.subscription')
            ], 403);
        }

        return redirect()->route('tenant.subscription')
            ->with('error', 'Your trial period has ended. Please subscribe to continue.');
    }
}
