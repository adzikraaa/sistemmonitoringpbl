<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckRole
{
    public function handle(Request $request, Closure $next, string $role)
    {
        // TODO: implementasi pengecekan role di sini (dikerjakan terpisah)
        return $next($request);
    }
}
