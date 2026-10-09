<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class EnsureRole
{
    public function handle(Request $request, Closure $next, string $role): Response
    {
        // Kullanıcı giriş yapmamışsa veya rolü eşleşmiyorsa engelle
        if (!Auth::check() || Auth::user()->role !== $role) {
            abort(403, 'Bu sayfaya erişim yetkiniz yok.');
        }

        return $next($request);
    }
}
