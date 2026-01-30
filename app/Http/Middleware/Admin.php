<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class Admin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Si l'utilisateur est authentifié et n'est pas un client , proprietaire ou acheteur
        if (Auth::check() && !in_array(Auth::user()->role, ['client', 'proprietaire', 'acheteur', 'locataire'])) {
            return $next($request);
        }

        // Si l'utilisateur est un client ou n'est pas authentifié
        return redirect()->route('admin.login')->withError('Session expirée ou accès non autorisé, veuillez vous connecter');
    }
}
