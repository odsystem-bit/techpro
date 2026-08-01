# TPF WhatsApp Bot — IA de vente, conseil et relance

Bot WhatsApp intelligent pour **Tech Pro Futur** (tpfcedp.com) propulsé par GPT-4o mini.

## Fonctionnalités

- **Réponse automatique**: L'IA répond aux clients 24/7, même sans le propriétaire
- **Vente & conseil**: Recommande les produits du catalogue, convainc avec des arguments
- **Images produits**: Envoie les images des produits directement depuis le site
- **Liens de paiement**: Génère et envoie les liens de checkout Moneroo
- **Upsell**: Propose des produits complémentaires naturellement
- **Relance intelligente**: Une seule relance, 48h après intérêt (configurable)
- **Reporting propriétaire**: Canal dédié où le propriétaire pose des questions et l'IA répond avec les stats

## Architecture

```
Client WhatsApp ──→ Twilio Webhook ──→ Bot (Express) ──→ OpenAI GPT-4o mini
                        ↓                                      ↓
                   API Laravel                           Réponse au client
                   (catalogue, paiement,                + images + liens
                    stats, commandes)
                        
Propriétaire ──→ Twilio Webhook ──→ Bot ──→ IA (stats, ventes, Q&A)
```

## Structure du projet

```
whatsapp-bot/          # Bot Node.js (Twilio + OpenAI + Express)
├── src/
│   ├── index.js       # Serveur Express + webhook Twilio
│   ├── whatsapp.js    # Client Twilio WhatsApp
│   ├── ai.js          # Intégration OpenAI GPT-4o mini
│   ├── catalog.js     # Communication avec l'API Laravel
│   ├── conversation.js# Gestion des conversations (stockage local)
│   ├── followup.js    # Logique de relance intelligente
│   ├── owner.js       # Canal propriétaire (stats, Q&A)
│   ├── prompts.js     # Prompts système pour l'IA
│   └── cron/
│       └── followup-runner.js  # Runner manuel des relances
├── .env.example       # Configuration à copier en .env
└── package.json

laravel-api/           # Fichiers à ajouter au site Laravel
├── routes/api.php     # Routes API pour le bot
├── app/Http/
│   ├── Controllers/Api/ApiController.php  # Endpoints API
│   └── Middleware/ApiToken.php             # Authentification par token
└── bootstrap/app.php  # Version modifiée (active les routes API)
```

## Installation

### 1. Bot WhatsApp (Node.js)

```bash
cd whatsapp-bot
npm install
cp .env.example .env
# Éditer .env avec vos clés Twilio, OpenAI et API Laravel
npm start
```

### 2. API Laravel (sur le serveur)

Copier les fichiers de `laravel-api/` vers le site:

```bash
# Sur le serveur
cp routes/api.php /home/u217725874/domains/tpfcedp.com/public_html/routes/api.php
cp app/Http/Controllers/Api/ApiController.php /home/u217725874/domains/tpfcedp.com/public_html/app/Http/Controllers/Api/
cp app/Http/Middleware/ApiToken.php /home/u217725874/domains/tpfcedp.com/public_html/app/Http/Middleware/
cp bootstrap/app.php /home/u217725874/domains/tpfcedp.com/public_html/bootstrap/app.php

# Générer un token API et l'ajouter au .env
echo "BOT_API_TOKEN=$(openssl rand -hex 24)" >> /home/u217725874/domains/tpfcedp.com/public_html/.env

# Vider le cache
cd /home/u217725874/domains/tpfcedp.com/public_html
php artisan route:clear
php artisan config:clear
php artisan cache:clear
```

### 3. Configuration Twilio

1. Créer un compte sur [twilio.com](https://twilio.com)
2. Activer WhatsApp Business API (sandbox ou compte complet)
3. Obtenir:
   - `TWILIO_ACCOUNT_SID`
   - `TWILIO_AUTH_TOKEN`
   - `TWILIO_WHATSAPP_NUMBER`
4. Configurer le webhook: URL `https://votre-serveur:3000/webhook` pour les messages entrants
5. Ajouter les clés dans `.env`

### 4. Configuration OpenAI

1. Clé API sur [platform.openai.com](https://platform.openai.com)
2. Ajouter `OPENAI_API_KEY` dans `.env`
3. Modèle par défaut: `gpt-4o-mini` (rapide et économique)

## Configuration (.env)

| Variable | Description |
|----------|-------------|
| `TWILIO_ACCOUNT_SID` | Account SID Twilio |
| `TWILIO_AUTH_TOKEN` | Auth token Twilio |
| `TWILIO_WHATSAPP_NUMBER` | Numéro WhatsApp Twilio (format: whatsapp:+XXXX) |
| `OPENAI_API_KEY` | Clé API OpenAI |
| `OPENAI_MODEL` | Modèle IA (défaut: gpt-4o-mini) |
| `SITE_API_URL` | URL de l'API Laravel (https://tpfcedp.com/api) |
| `SITE_API_TOKEN` | Token API (même que BOT_API_TOKEN sur le site) |
| `OWNER_WHATSAPP` | Numéro WhatsApp du propriétaire |
| `FOLLOWUP_DELAY_HOURS` | Délai relance en heures (défaut: 48) |
| `FOLLOWUP_CRON` | Cron relance (défaut: 0 9 * * * = 9h chaque jour) |

## API Laravel Endpoints

| Méthode | Route | Description |
|---------|-------|-------------|
| GET | `/api/catalog` | Catalogue complet (produits + packs) |
| GET | `/api/products/{slug}` | Détail d'un produit |
| POST | `/api/checkout` | Créer une session de paiement |
| GET | `/api/stats` | Statistiques de vente |
| GET | `/api/orders` | Commandes récentes |

Toutes les routes sont protégées par le middleware `api.token` (Bearer token).

## Déploiement

### Bot sur le serveur

```bash
# Cloner le repo
cd /home/u217725874/domains/tpfcedp.com
git clone <repo-url> bot
cd bot/whatsapp-bot
npm install --production
cp .env.example .env
# Configurer .env

# Démarrer avec PM2 (recommandé)
pm2 start src/index.js --name tpf-bot
pm2 save
pm2 startup
```

### Webhook Twilio

Configurer l'URL webhook dans Twilio:
```
https://tpfcedp.com:3000/webhook
```

Note: Twilio nécessite HTTPS. Utiliser un reverse proxy Nginx ou un tunnel Cloudflare.

## Fonctionnement

### Côté client
1. Le client écrit au numéro WhatsApp du bot
2. L'IA détecte l'intention (salutation, info, achat, conseil, support)
3. L'IA répond avec des recommandations de produits
4. Si le client veut acheter → lien de paiement généré
5. Si le client n'achète pas → relance planifiée à 48h (une seule fois)

### Côté propriétaire
1. Le propriétaire écrit au même numéro (reconnu par OWNER_WHATSAPP)
2. L'IA bascule en mode reporting
3. Le propriétaire peut demander: ventes, stats, commandes, relances, résumé
4. L'IA répond avec les données réelles du site

## Sécurité

- API Laravel protégée par token Bearer
- Webhook Twilio vérifiable (signature Twilio)
- Clés API dans `.env` (jamais commité)
- Le `.gitignore` exclut `.env` et les données de conversations
