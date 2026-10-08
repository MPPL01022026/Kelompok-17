<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminSession
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->session()->has('admin')) {
            return redirect('/admin/login');
        }

        return $next($request);
    }
}
