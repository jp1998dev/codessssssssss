<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Support\Facades\Log;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        // Handle role-based redirection
        if (Auth::user()->role === 'vp_admin') {
            return redirect()->route('vpadmin.vpadmin_db');
        }

        if (Auth::user()->role === 'vp_academic') {
            return redirect()->route('vp_academic.vpacademic_db');
        }

        if (Auth::user()->role === 'registrar') {
            return redirect()->route('registrar.dashboard');
        }

        if (Auth::user()->role === 'cashier') {
            $currentHour = now()->format('H:i');

            // if ($currentHour < '07:00' || $currentHour >= '20:00') {
            //     Auth::logout();
            //     $request->session()->invalidate();
            //     $request->session()->regenerateToken();

            //     return redirect('/')->with('error', 'Cashier login is allowed only from 7:00 AM to 8:00 PM.');
            // }

            return redirect()->route('cashier.dashboard');
        }
        if (Auth::user()->role === 'manual_cashier') {
            return redirect()->route('manual_cashier.dashboard');
        }
        if (Auth::user()->role === 'accounting') {
            return redirect()->route('accountant.accountant_db');
        }
        if (Auth::user()->role === 'president') {
            return redirect()->route('president.dashboard');
        }

        if (Auth::user()->role === 'all') {
            return redirect()->route('all.dashboard');
        }
        if (Auth::user()->role === 'super') {
            return redirect()->route('super.dashboard');
        }


        // If no matching role, logout user and clear session
        $this->destroy($request);

        return redirect('/')->with('message', 'Error 404. Please try again.');
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
