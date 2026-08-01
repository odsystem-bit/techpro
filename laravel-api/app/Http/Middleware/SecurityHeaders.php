<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Headers de Sécurité - Protection contre XSS, Clickjacking, etc.
 * 
 * Implémente les meilleures pratiques OWASP 2024
 */
class SecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);
        
        // Empêche le site d'être intégré dans un iframe (protection clickjacking)
        $response->headers->set('X-Frame-Options', 'DENY');
        
        // Protection XSS
        $response->headers->set('X-XSS-Protection', '1; mode=block');
        
        // Empêche le MIME sniffing
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        
        // Referrer Policy
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        
        // Content Security Policy (CSP) - Très restrictif
        $csp = "default-src 'self'; ";
        $csp .= "script-src 'self' 'unsafe-inline' 'unsafe-eval' https://cdn.tailwindcss.com https://unpkg.com https://cdn.jsdelivr.net; ";
        $csp .= "style-src 'self' 'unsafe-inline' https://cdn.tailwindcss.com https://fonts.googleapis.com https://unpkg.com; ";
        $csp .= "img-src 'self' data: https: blob:; ";
        $csp .= "font-src 'self' https://fonts.gstatic.com; ";
        $csp .= "connect-src 'self' https://api.moneroo.io https://api.feexpay.me; ";
        $csp .= "frame-ancestors 'none'; ";
        $csp .= "base-uri 'self'; ";
        $csp .= "form-action 'self';";
        
        $response->headers->set('Content-Security-Policy', $csp);
        
        // Permissions Policy
        $response->headers->set('Permissions-Policy', 
            'accelerometer=(), camera=(), geolocation=(), gyroscope=(), magnetometer=(), microphone=(), payment=(), usb=()');
        
        // HSTS (forcer HTTPS) - Uniquement en production
        if (env('APP_ENV') === 'production') {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains; preload');
        }
        
        // Cache Control pour pages sensibles
        if ($request->is('admin/*') || $request->is('*/login') || $request->is('*/register')) {
            $response->headers->set('Cache-Control', 'no-store, no-cache, must-revalidate, proxy-revalidate');
            $response->headers->set('Pragma', 'no-cache');
            $response->headers->set('Expires', '0');
        }
        
        return $response;
    }
}
