<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class DenyStaff
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        if ($user && $user->hasRole('staff')) {
            Log::warning('Unauthorized access attempt by Staff', [
                'user_id'    => $user->id,
                'name'       => $user->name ?? 'unknown',
                'route'      => $request->path(),
                'method'     => $request->method(),
                'ip'         => $request->ip(),
                'user_agent' => $request->userAgent(),
                'time'       => now()->toISOString(),
            ]);

            abort(404);
        }

        return $next($request);
    }
}
