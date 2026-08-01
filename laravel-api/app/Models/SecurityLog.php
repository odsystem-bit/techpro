<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SecurityLog extends Model
{
    protected $fillable = [
        'event_type',
        'ip_address',
        'user_agent',
        'email',
        'url',
        'details',
        'country',
        'city'
    ];

    public $timestamps = false;
    
    protected $casts = [
        'created_at' => 'datetime',
    ];

    // Types d'événements
    const FAILED_LOGIN = 'failed_login';
    const IP_BLOCKED = 'ip_blocked';
    const SUSPICIOUS_ACTIVITY = 'suspicious_activity';
    const ADMIN_ACCESS = 'admin_access';
    const ADMIN_LOGIN_SUCCESS = 'admin_login_success';
    const SQL_INJECTION_ATTEMPT = 'sql_injection_attempt';
    const XSS_ATTEMPT = 'xss_attempt';

    /**
     * Enregistrer un événement de sécurité
     */
    public static function log(string $eventType, array $data = []): void
    {
        self::create([
            'event_type' => $eventType,
            'ip_address' => $data['ip'] ?? request()->ip(),
            'user_agent' => $data['user_agent'] ?? request()->userAgent(),
            'email' => $data['email'] ?? null,
            'url' => $data['url'] ?? request()->fullUrl(),
            'details' => json_encode($data['details'] ?? []),
            'created_at' => now()
        ]);
    }

    /**
     * Obtenir les tentatives échouées récentes
     */
    public static function recentFailedLogins(int $minutes = 60)
    {
        return self::where('event_type', self::FAILED_LOGIN)
            ->where('created_at', '>=', now()->subMinutes($minutes))
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Vérifier si une IP est suspecte
     */
    public static function isSuspiciousIp(string $ip): bool
    {
        $failedAttempts = self::where('ip_address', $ip)
            ->where('event_type', self::FAILED_LOGIN)
            ->where('created_at', '>=', now()->subMinutes(30))
            ->count();

        return $failedAttempts >= 5;
    }
}
