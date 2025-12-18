<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AdminOnly
{
    public function handle($request, Closure $next)
{
    if (!auth()->check() || auth()->user()->role !== 'admin') {
        return redirect()->route('store.index');
    }

    return $next($request);
}
}
