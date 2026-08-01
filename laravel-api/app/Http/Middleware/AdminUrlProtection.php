<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Protection URL Admin - Masque l'URL d'administration
 * 
 * L'URL /admin retourne 404 intentionnellement
 * L'URL secrète (configurée dans .env) est la seule qui fonctionne
 */
class AdminUrlProtection
{
    /**
     * URL secrète configurée dans .env (ADMIN_SECRET_URL)
     * Exemple: /canevas-regarde-rien/login
     */
    public function handle(Request $request, Closure $next): Response
    {
        $secretUrl = env('ADMIN_SECRET_URL', '/canevas-regarde-rien/login');
        $currentPath = $request->path();
        
        // Si l'URL contient /admin mais n'est pas l'URL secrète → 404
        if (str_contains($currentPath, 'admin') && !str_contains($currentPath, ltrim($secretUrl, '/'))) {
            abort(404, 'Page non trouvée');
        }
        
        return $next($request);
    }
}
