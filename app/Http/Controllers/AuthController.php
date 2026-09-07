<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class AuthController extends Controller
{
    /**
     * Show the login page.
     */
    public function showLogin(): Response
    {
        return Inertia::render('Auth/Login');
    }

    /**
     * Handle login authentication.
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'uname' => 'required|string',
            'password' => 'required|string',
        ]);

        // Attempt login using 'uname'
        if (Auth::attempt(['uname' => $credentials['uname'], 'password' => $credentials['password']])) {
            $request->session()->regenerate();
            return redirect()->intended('/dashboard');
        }

        return back()->withErrors([
            'uname' => 'البيانات المدخلة غير صحيحة.',
        ])->onlyInput('uname');
    }

    /**
     * Show the registration page.
     */
    public function showRegister(): Response
    {
        return Inertia::render('Auth/Register');
    }

    /**
     * Handle tenant registration and user creation.
     */
    public function register(Request $request)
    {
        $validated = $request->validate([
            'company_name' => 'required|string|max:255',
            'uname' => 'required|string|max:255|unique:users,uname',
            'password' => 'required|string|min:4|confirmed',
        ]);

        DB::beginTransaction();

        try {
            // 1. Create Tenant
            $tenantCode = Str::slug($validated['company_name']) . '-' . rand(1000, 9999);
            $tenant = Tenant::create([
                'name' => $validated['company_name'],
                'code' => $tenantCode,
                'is_active' => true,
            ]);

            // 2. Create User assigned to the new Tenant
            $user = User::create([
                'uname' => $validated['uname'],
                'password' => Hash::make($validated['password']),
                'tenant' => $tenant->id,
                'userrole' => 1, // 1 could be Admin role, based on existing system
                'is_waiter' => 0,
            ]);

            DB::commit();

            // 3. Login the user
            Auth::login($user);
            $request->session()->regenerate();

            return redirect()->route('dashboard');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'حدث خطأ أثناء التسجيل: ' . $e->getMessage()]);
        }
    }

    /**
     * Handle logout.
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
