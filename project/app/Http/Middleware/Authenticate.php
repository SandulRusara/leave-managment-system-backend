<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{

    protected function redirectTo(Request $request): ?string
    {
        if ($request->expectsJson()) {
            return null;
        }

        return $request->is('api/*') ? null : route('login');
    }

    protected function unauthenticated($request, array $guards)
    {
        if ($request->expectsJson() || $request->is('api/*')) {
            abort(response()->json([
                'success' => false,
                'message' => 'Unauthenticated. Please log in to access this resource.'
            ], 401));
        }

        parent::unauthenticated($request, $guards);
    }
}