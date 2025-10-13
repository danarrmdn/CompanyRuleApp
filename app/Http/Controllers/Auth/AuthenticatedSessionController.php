<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\View\View;

use Illuminate\Support\Str;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(Request $request): View
    {
        $rememberedEmpId = $request->cookie('remembered_emp_id');

        return view('auth.login', ['remembered_emp_id' => $rememberedEmpId]);
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        if ($request->boolean('remember')) {
            Cookie::queue('remembered_emp_id', $request->emp_id, 60 * 24 * 365 * 5);
        } else {
            Cookie::queue(Cookie::forget('remembered_emp_id'));
        }

        $intendedUrl = session('url.intended');

        if ($intendedUrl && Str::contains($intendedUrl, 'approvals')) {
            return redirect($intendedUrl);
        }

        return redirect(route('dashboard', absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
