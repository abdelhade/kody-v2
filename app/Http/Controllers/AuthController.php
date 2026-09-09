<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\LandlordUser;
use Inertia\Inertia;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (\App\Models\Tenant::current()) {
            if (Auth::guard('web')->check()) {
                return redirect()->route('dashboard');
            }
        } else {
            if (Auth::guard('landlord')->check()) {
                return redirect()->route('workspaces.create');
            }
        }
        return Inertia::render('Auth/Login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $tenantResolved = \App\Models\Tenant::current();

        if ($tenantResolved) {
            // Tenant Login (Employees & Company Owners on Subdomain)
            if (Auth::guard('web')->attempt($credentials, $request->boolean('remember'))) {
                $request->session()->regenerate();
                return redirect()->route('dashboard');
            }
        } else {
            // Landlord Login (Company Owners on Main Domain)
            if (Auth::guard('landlord')->attempt($credentials, $request->boolean('remember'))) {
                $request->session()->regenerate();
                return redirect()->route('workspaces.create');
            }
        }

        return back()->withErrors([
            'error' => 'بيانات الدخول غير صحيحة.'
        ]);
    }

    public function showRegister()
    {
        if (!\App\Models\Tenant::current() && Auth::guard('landlord')->check()) {
            return redirect()->route('workspaces.create');
        }
        return Inertia::render('Auth/Register');
    }

    public function register(Request $request)
    {
        if ($request->has('company_code')) {
            $request->merge([
                'company_code' => Str::slug($request->company_code)
            ]);
        }

        $validated = $request->validate([
            'uname' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:landlord.landlord_users,email',
            'phone' => 'required|string|max:20',
            'password' => 'required|string|min:4|confirmed',
            'company_name' => 'required|string|max:255',
            'company_code' => 'required|string|alpha_dash|max:50|unique:landlord.tenants,code',
        ]);

        $user = LandlordUser::create([
            'name' => $validated['uname'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'password' => Hash::make($validated['password']),
        ]);

        Auth::guard('landlord')->login($user);
        $request->session()->regenerate();

        // Store company details in session for the next step
        $request->session()->put('pending_workspace', [
            'company_name' => $validated['company_name'],
            'company_code' => $validated['company_code'],
        ]);

        return redirect()->route('workspaces.create');
    }

    public function logout(Request $request)
    {
        if (Auth::guard('landlord')->check()) {
            Auth::guard('landlord')->logout();
        }
        
        if (Auth::guard('web')->check()) {
            Auth::guard('web')->logout();
        }

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
