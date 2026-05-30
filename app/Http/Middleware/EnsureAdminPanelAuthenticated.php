<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAdminPanelAuthenticated
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user()?->is_admin) {
            return redirect()->route('admin.login');
        }

        $timeoutMinutes = max((int) env('ADMIN_PANEL_IDLE_TIMEOUT', 30), 1);
        $lastActivity = (int) $request->session()->get('admin_panel_last_activity', now()->timestamp);
        $isExpired = (time() - $lastActivity) > ($timeoutMinutes * 60);

        if ($isExpired) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()
                ->route('admin.login')
                ->with('status', 'Tu sesion admin expiro por inactividad. Vuelve a ingresar.');
        }

        $request->session()->put('admin_panel_last_activity', time());

        return $next($request);
    }
}
