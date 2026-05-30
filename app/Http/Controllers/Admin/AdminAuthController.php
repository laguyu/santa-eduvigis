<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AdminAuthController extends Controller
{
    public function showLoginForm(Request $request): View|RedirectResponse
    {
        if ($request->user()?->is_admin) {
            return redirect()->route('admin.contents.index');
        }

        return view('admin.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $throttleKey = $this->throttleKey($request);
        $maxAttempts = 5;
        $decaySeconds = 60;

        if (RateLimiter::tooManyAttempts($throttleKey, $maxAttempts)) {
            $seconds = RateLimiter::availableIn($throttleKey);

            throw ValidationException::withMessages([
                'email' => "Demasiados intentos. Intenta de nuevo en {$seconds} segundos.",
            ]);
        }

        if (! Auth::attempt([
            'email' => $credentials['email'],
            'password' => $credentials['password'],
            'is_admin' => true,
        ])) {
            RateLimiter::hit($throttleKey, $decaySeconds);

            return back()->withErrors(['email' => 'Credenciales invalidas.'])->onlyInput('email');
        }

        RateLimiter::clear($throttleKey);
        $request->session()->regenerate();
        $request->session()->put('admin_panel_last_activity', now()->timestamp);

        return redirect()->route('admin.contents.index');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }

    public function showForgotPasswordForm(): View|RedirectResponse
    {
        if (Auth::user()?->is_admin) {
            return redirect()->route('admin.contents.index');
        }

        return view('admin.passwords.forgot');
    }

    public function sendResetLink(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => [
                'required',
                'email',
                Rule::exists('users', 'email')->where(fn ($query) => $query->where('is_admin', true)),
            ],
        ], [
            'email.exists' => 'No existe un usuario admin con ese correo.',
        ]);

        $status = Password::sendResetLink($request->only('email'));

        if ($status === Password::RESET_LINK_SENT) {
            return back()->with('status', __($status));
        }

        throw ValidationException::withMessages([
            'email' => [__($status)],
        ]);
    }

    public function showResetPasswordForm(Request $request, string $token): View
    {
        return view('admin.passwords.reset', [
            'token' => $token,
            'email' => (string) $request->query('email', ''),
        ]);
    }

    public function resetPassword(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'token' => ['required', 'string'],
            'email' => [
                'required',
                'email',
                Rule::exists('users', 'email')->where(fn ($query) => $query->where('is_admin', true)),
            ],
            'password' => ['required', 'confirmed', 'min:8'],
        ], [
            'email.exists' => 'No existe un usuario admin con ese correo.',
        ]);

        $status = Password::reset(
            $credentials,
            function (User $user, string $password): void {
                $user->forceFill([
                    'password' => Hash::make($password),
                ])->save();
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return redirect()
                ->route('admin.login')
                ->with('status', 'Contrasena actualizada correctamente. Ya puedes iniciar sesion.');
        }

        throw ValidationException::withMessages([
            'email' => [__($status)],
        ]);
    }

    private function throttleKey(Request $request): string
    {
        return strtolower((string) $request->input('email')).'|'.$request->ip();
    }
}
