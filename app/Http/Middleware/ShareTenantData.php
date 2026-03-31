<?php

namespace App\Http\Middleware;

use Illuminate\View\View;
use Illuminate\Http\Request;
use Stancl\Tenancy\Tenancy;

class ShareTenantData
{
    public function handle(Request $request, \Closure $next)
    {
        if (tenancy()->initialized) {
            $tenant = tenant();
            
            view()->share('tenant', $tenant);
            
            if (auth()->check()) {
                view()->share('currentUser', auth()->user());
            }
        }

        return $next($request);
    }
}
