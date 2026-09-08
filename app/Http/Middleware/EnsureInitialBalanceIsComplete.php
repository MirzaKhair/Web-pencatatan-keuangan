<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureInitialBalanceIsComplete
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user || is_null($user->initial_balance_setup_at)) {
            return redirect()->route('setup-initial-balance');
        }

        return $next($request);
    }
}