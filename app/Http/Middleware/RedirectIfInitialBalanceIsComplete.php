<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfInitialBalanceIsComplete
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && ! is_null($user->initial_balance_setup_at)) {
            return redirect()->route('dashboard');
        }

        return $next($request);
    }
}