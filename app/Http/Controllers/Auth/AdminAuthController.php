<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\AdminAuthService;
use Illuminate\Http\Request;

class AdminAuthController extends Controller
{
    public function __construct(private AdminAuthService $authService) {}


    public function showLoginForm()
    {
        return view('admin.login');
    }


    public function login(Request $request)
    {
        $request->validate([
            'username'  => 'required',
            'password'  => 'required',
        ]);

        if ($this->authService->login($request->username, $request->password)) {
            return redirect()->route('admin.dashboard');
        }

        return back()->with('error', 'Username atau password salah.');
    }


    public function logout(Request $request)
    {
        $this->authService->logout();
        return redirect()->route('admin.login');
    }
}
