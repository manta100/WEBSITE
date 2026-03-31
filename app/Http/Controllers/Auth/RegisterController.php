<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Tenant;
use App\Services\SubscriptionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;

class RegisterController extends Controller
{
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'business_name' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => ['required', 'confirmed', Password::min(8)],
            'domain' => 'nullable|string|max:100|unique:tenants,domain',
        ]);

        $slug = Str::slug($request->business_name);
        
        $tenant = Tenant::create([
            'name' => $request->business_name,
            'slug' => $slug,
            'domain' => $request->domain ? Str::slug($request->domain) : null,
            'trial_ends_at' => now()->addDays(config('subscription.trial_days', 3)),
            'is_active' => true,
        ]);

        $user = User::create([
            'tenant_id' => $tenant->id,
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'owner',
            'is_active' => true,
        ]);

        $subscriptionService = app(SubscriptionService::class);
        $subscriptionService->createTrialSubscription($tenant);

        return redirect()->route('login')->with('success', 'Registration successful! You have a 3-day free trial.');
    }
}
