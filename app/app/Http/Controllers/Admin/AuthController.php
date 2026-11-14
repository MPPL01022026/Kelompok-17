<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LoginAttempt;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function login(): View|RedirectResponse
    {
        return session()->has('admin') ? redirect('/admin') : view('admin.login');
    }

    public function authenticate(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $identifier = $data['username'];
        $attempt = LoginAttempt::find($identifier);

        if ($attempt?->locked_until && now()->lt($attempt->locked_until)) {
            $message = 'Terlalu banyak percobaan. Coba lagi beberapa menit.';

            return back()
                ->withInput($request->only('username'))
                ->with('error', $message)
                ->withErrors(['login' => $message]);
        }

        $expectedUsername = (string) config('services.ayamo.admin_username', 'admin');
        $passwordHash = (string) config('services.ayamo.admin_password_hash', '');

        $usernameMatches = $expectedUsername !== '' && hash_equals((string) $expectedUsername, (string) $identifier);
        if (!$usernameMatches || $passwordHash === '' || !Hash::check($data['password'], $passwordHash)) {
            $attempts = ($attempt->attempts ?? 0) + 1;
            LoginAttempt::updateOrCreate(
                ['identifier' => $identifier],
                [
                    'attempts' => $attempts,
                    'locked_until' => $attempts >= 5 ? now()->addMinutes(15) : null,
                ]
            );

            $message = 'Username atau password salah.';

            return back()
                ->withInput($request->only('username'))
                ->with('error', $message)
                ->withErrors(['login' => $message]);
        }

        LoginAttempt::where('identifier', $identifier)->delete();
        $request->session()->regenerate();
        $request->session()->put('admin', $identifier);

        return redirect('/admin');
    }

    public function logout(Request $request): RedirectResponse
    {
        $request->session()->forget('admin');
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/admin/login');
    }
}
