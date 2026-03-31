<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function pos(): \Illuminate\View\View
    {
        return view('pos.index');
    }

    public function dashboard(): \Illuminate\View\View
    {
        return view('pos.dashboard');
    }

    public function subscription(): \Illuminate\View\View
    {
        $plans = \App\Models\Plan::active()->ordered()->get();
        $tenant = tenant();
        
        return view('pos.subscription', compact('plans', 'tenant'));
    }

    public function subscribe(Request $request): \Illuminate\Http\RedirectResponse
    {
        $request->validate([
            'plan_id' => 'required|exists:plans,id',
        ]);

        $plan = \App\Models\Plan::find($request->plan_id);
        $subscriptionService = app(\App\Services\SubscriptionService::class);
        
        $subscription = $subscriptionService->subscribe(tenant(), $plan);
        
        return redirect()->back()->with('success', 'Successfully subscribed to ' . $plan->name);
    }
}
