# 🔒 Guide de Sécurité - Tech Pro Futur

## 🛡️ Mesures de sécurité implémentées

### 1. URL Admin Secrète (Anti-reconnaissance)
```
URL standard : /admin → 404 Intentionnellement
URL secrète   : /porte-secrete-{code}/login (configurable dans .env)
```
**Pourquoi ?** Empêche les bots et hackers de trouver la page de connexion admin.

### 2. Protection Anti-Brute Force
- **3 tentatives maximum** avant blocage
- **Durée du blocage** : 15 minutes
- **Blocage par IP** ET par email
- **Logs automatiques** de toutes les tentatives

### 3. Headers de Sécurité (OWASP 2024)
- `X-Frame-Options: DENY` → Anti clickjacking
- `X-XSS-Protection: 1; mode=block` → Protection XSS
- `X-Content-Type-Options: nosniff` → Anti MIME sniffing
- `Content-Security-Policy` → Contrôle des ressources chargées
- `Strict-Transport-Security` → Force HTTPS (en production)

### 4. Protection des fichiers sensibles
- Accès interdit aux fichiers `.env`, `.git*`, `composer.*`
- Protection du dossier `storage/`
- Interdiction d'exécution de scripts PHP dans uploads

### 5. Système de logs de sécurité
Table `security_logs` qui enregistre :
- Tentatives de connexion échouées
- IPs bloquées
- Activités suspectes
- Accès admin réussis

---

## 🔧 Configuration post-déploiement

### Étape 1 : Configurer l'URL admin secrète

Dans le fichier `.env` :
```env
ADMIN_SECRET_URL=/porte-secrete-xyz123/login
```

**Générer un code unique aléatoire** (ex: 8-12 caractères)

### Étape 2 : Accéder au panel admin

```
https://tpfcedp.com/porte-secrete-xyz123/login
```
**NE JAMAIS** partager cette URL !

### Étape 3 : Changer les mots de passe

1. Connectez-vous à l'admin
2. Allez dans Paramètres → Sécurité
3. Changez le mot de passe admin immédiatement
4. Utilisez un mot de passe fort (12+ caractères, majuscules, minuscules, chiffres, symboles)

### Étape 4 : Activer HTTPS forcé (HSTS)

Dans `public/.htaccess`, décommenter :
```apache
Header always set Strict-Transport-Security "max-age=31536000; includeSubDomains; preload"
```

---

## 🚨 Surveillance de sécurité

### Vérifier les logs de sécurité

```sql
-- Tentatives de connexion échouées (24h)
SELECT * FROM security_logs 
WHERE event_type = 'failed_login' 
AND created_at >= DATEADD(hour, -24, GETDATE())
ORDER BY created_at DESC;

-- IPs les plus suspectes
SELECT ip_address, COUNT(*) as attempts 
FROM security_logs 
WHERE event_type = 'failed_login' 
GROUP BY ip_address 
ORDER BY attempts DESC 
LIMIT 10;
```

### Dans Laravel Artisan

```bash
# Voir les logs récents
tail -f storage/logs/laravel.log | grep -i "security\|failed\|blocked"

# Vider le cache de blocage (si nécessaire)
php artisan cache:clear
```

---

## ⚠️ Bonnes pratiques

### 1. Mots de passe
- Changer tous les 90 jours
- Ne jamais réutiliser le même mot de passe
- Utiliser un gestionnaire de mots de passe

### 2. URLs
- Ne jamais partager l'URL admin secrète
- Ne pas l'inclure dans la documentation publique
- La changer si un employé part

### 3. Accès
- Limité à 2-3 personnes maximum
- Connexion 2FA si possible
- Déconnexion après inactivité

### 4. Sauvegardes
- Sauvegarder la base de données quotidiennement
- Stocker les backups hors site
- Tester les restaurations mensuellement

---

## 🔐 Checklist avant mise en production

- [ ] URL admin secrète configurée (différente de /admin)
- [ ] Mot de passe admin fort (12+ caractères)
- [ ] HTTPS activé avec certificat valide
- [ ] HSTS activé (forcer HTTPS)
- [ ] Base de données migrée avec données de test supprimées
- [ ] Mode debug désactivé (APP_DEBUG=false)
- [ ] Clés API (Moneroo, Feexpay) en mode production
- [ ] Email de contact configuré
- [ ] Logs de sécurité activés
- [ ] Fichier .env protégé (chmod 600)
- [ ] Dossier storage/ accessible en écriture

---

## 📞 En cas de problème

### Si vous êtes bloqué (IP bannie)

1. Attendre 15 minutes
2. OU vider le cache via FTP/SSH : supprimer `storage/framework/cache/data/*`

### Si vous suspectez une intrusion

1. Changer immédiatement tous les mots de passe
2. Changer l'URL admin secrète
3. Vérifier les logs de sécurité
4. Scanner les fichiers modifiés récemment
5. Contacter votre hébergeur

---

**Dernière mise à jour** : Sécurité niveau entreprise - Mai 2026
