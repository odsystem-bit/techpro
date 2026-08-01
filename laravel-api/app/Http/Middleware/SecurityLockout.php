<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

/**
 * Protection Anti-Brute Force
 * 
 * - Bloque après 3 tentatives échouées
 * - Durée du blocage : 15 minutes
 * - Log toutes les tentatives suspectes
 * - Notification après blocage
 */
class SecurityLockout
{
    private const MAX_ATTEMPTS = 3;
    private const LOCKOUT_DURATION = 15; // minutes
    
    public function handle(Request $request, Closure $next): Response
    {
        // Ne s'applique qu'aux routes de login
        if (!$this->isLoginRoute($request)) {
            return $next($request);
        }
        
        $key = $this->getLockoutKey($request);
        $ipKey = $this->getIpKey($request);
        
        // Vérifier si l'IP est déjà bloquée
        if (Cache::has($ipKey)) {
            $remaining = Cache::get($ipKey . '_time');
            Log::warning('Tentative de connexion depuis IP bloquée', [
                'ip' => $request->ip(),
                'email' => $request->input('email'),
                'user_agent' => $request->userAgent(),
                'remaining_lockout' => $remaining
            ]);
            
            return response()->json([
                'error' => 'Trop de tentatives. Réessayez dans ' . $remaining . ' minutes.',
                'lockout' => true
            ], 429);
        }
        
        // Vérifier si l'utilisateur spécifique est bloqué
        if (Cache::has($key)) {
            $remaining = Cache::get($key . '_time');
            
            return response()->json([
                'error' => 'Compte temporairement verrouillé. Réessayez dans ' . $remaining . ' minutes.',
                'lockout' => true
            ], 429);
        }
        
        $response = $next($request);
        
        // Si échec de connexion
        if ($response->getStatusCode() === 401 || 
            ($response->getStatusCode() === 200 && $this->isFailedLogin($response))) {
            $this->recordFailedAttempt($request, $key, $ipKey);
        }
        
        return $response;
    }
    
    private function isLoginRoute(Request $request): bool
    {
        return $request->isMethod('post') && 
               (str_contains($request->path(), 'login') || 
                str_contains($request->path(), 'auth'));
    }
    
    private function getLockoutKey(Request $request): string
    {
        $email = $request->input('email', 'unknown');
        return 'login_attempts:' . md5($email);
    }
    
    private function getIpKey(Request $request): string
    {
        return 'login_ip_lockout:' . md5($request->ip());
    }
    
    private function recordFailedAttempt(Request $request, string $key, string $ipKey): void
    {
        $attempts = Cache::get($key . '_count', 0) + 1;
        Cache::put($key . '_count', $attempts, now()->addHours(1));
        
        // Logger la tentative échouée
        Log::warning('Tentative de connexion échouée', [
            'ip' => $request->ip(),
            'email' => $request->input('email'),
            'attempts' => $attempts,
            'user_agent' => $request->userAgent(),
            'timestamp' => now()->toIso8601String()
        ]);
        
        // Bloquer après 3 tentatives
        if ($attempts >= self::MAX_ATTEMPTS) {
            Cache::put($key, true, now()->addMinutes(self::LOCKOUT_DURATION));
            Cache::put($key . '_time', self::LOCKOUT_DURATION, now()->addMinutes(self::LOCKOUT_DURATION));
            Cache::put($ipKey, true, now()->addMinutes(self::LOCKOUT_DURATION));
            Cache::put($ipKey . '_time', self::LOCKOUT_DURATION, now()->addMinutes(self::LOCKOUT_DURATION));
            
            Log::alert('Compte verrouillé après tentatives échouées', [
                'ip' => $request->ip(),
                'email' => $request->input('email'),
                'attempts' => $attempts,
                'lockout_duration' => self::LOCKOUT_DURATION . ' minutes',
                'user_agent' => $request->userAgent()
            ]);
        }
    }
    
    private function isFailedLogin(Response $response): bool
    {
        $content = json_decode($response->getContent(), true);
        return isset($content['error']) || isset($content['message']);
    }
}
