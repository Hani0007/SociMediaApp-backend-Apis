<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;

class Authenticate extends Middleware
{
    /**
     * Handle unauthenticated users.
     */
    protected function redirectTo($request)
    {
        if (! $request->expectsJson()) {
            // Instead of redirecting to "login", return nothing
            // because API should return JSON error
            abort(response()->json([
                'message' => 'Unauthenticated.'
            ], 401));
        }
    }
}
