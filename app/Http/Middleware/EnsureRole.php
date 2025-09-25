<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureRole
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        $user = $request->user();
        if (!$user) {
            abort(401);
        }

        // bisa terima id (1,2,3) atau slug ('admin', 'petugas-data', ...)
        foreach ($roles as $r) {
            if ($user->hasRole(is_numeric($r) ? (int)$r : $r)) {
                return $next($request);
            }
        }

        abort(403, 'Anda tidak memiliki akses.');
    }
}
