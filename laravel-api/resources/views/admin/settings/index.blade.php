@extends('admin.layouts.app')
@section('title', 'Paramètres du site')

@section('content')
<div class="mx-auto max-w-3xl">
    <form method="POST" action="{{ route('admin.settings.update') }}" enctype="multipart/form-data"
          class="space-y-8">
        @csrf

        @if ($errors->any())
        <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
            <ul class="list-inside list-disc space-y-1">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
        @endif

        {{-- Identité --}}
        <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
            <h2 class="mb-5 text-base font-bold text-gray-900">Identité du site</h2>
            <div class="grid gap-4 sm:grid-cols-2">
                <div class="sm:col-span-2">
                    <label class="label">Nom du site *</label>
                    <input type="text" name="site_name" value="{{ old('site_name', $settings['site_name'] ?? 'Tech Pro Futur') }}" required class="input" />
                </div>
                <div class="sm:col-span-2">
                    <label class="label">Slogan / Tagline</label>
                    <input type="text" name="site_tagline" value="{{ old('site_tagline', $settings['site_tagline'] ?? '') }}" class="input" />
                </div>
                <div class="sm:col-span-2">
                    <label class="label">Description (meta SEO)</label>
                    <textarea name="site_description" rows="3" class="input">{{ old('site_description', $settings['site_description'] ?? '') }}</textarea>
                </div>
                <div>
                    <label class="label">Logo (remplacer)</label>
                    @if (!empty($settings['logo']))
                        <img src="{{ asset('storage/' . $settings['logo']) }}" class="mb-2 h-12 rounded-lg object-contain bg-gray-100 p-1" />
                    @endif
                    <input type="file" name="logo" accept="image/*" class="input-file" />
                </div>
                <div>
                    <label class="label">Favicon</label>
                    @if (!empty($settings['favicon']))
                        <img src="{{ asset('storage/' . $settings['favicon']) }}" class="mb-2 h-8 w-8 rounded object-contain" />
                    @endif
                    <input type="file" name="favicon" accept="image/*" class="input-file" />
                </div>
            </div>
        </div>

        {{-- Contact --}}
        <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
            <h2 class="mb-5 text-base font-bold text-gray-900">Contact & Support</h2>
            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label class="label">Email de contact</label>
                    <input type="email" name="contact_email" value="{{ old('contact_email', $settings['contact_email'] ?? '') }}" class="input" />
                </div>
                <div>
                    <label class="label">Numéro WhatsApp (avec indicatif, ex: +2250707…)</label>
                    <input type="text" name="whatsapp_number" value="{{ old('whatsapp_number', $settings['whatsapp_number'] ?? '') }}" class="input" placeholder="+2250707000000" />
                </div>
                <div class="sm:col-span-2">
                    <label class="label">Texte du footer</label>
                    <input type="text" name="footer_text" value="{{ old('footer_text', $settings['footer_text'] ?? '') }}" class="input" />
                </div>
            </div>
        </div>

        {{-- Paiement Moneroo --}}
        <div class="rounded-xl border border-amber-200 bg-amber-50 p-6 shadow-sm">
            <div class="mb-5 flex items-center gap-3">
                <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-amber-400 text-white">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                </div>
                <div>
                    <h2 class="text-base font-bold text-gray-900">Paiement Moneroo</h2>
                    <p class="text-xs text-amber-700">Ces clés seront sauvegardées dans les paramètres du site et utilisées pour traiter les paiements.</p>
                </div>
            </div>
            <div class="space-y-4">
                <div class="rounded-lg border border-amber-200 bg-white px-4 py-3 text-sm text-amber-800">
                    ⚠️ Obtenez vos clés sur <a href="https://moneroo.io" target="_blank" class="font-bold underline">moneroo.io</a> → Dashboard → API Keys.
                </div>
                <div>
                    <label class="label">Clé API Moneroo (MONEROO_API_KEY)</label>
                    <input type="text" name="moneroo_api_key" value="{{ old('moneroo_api_key', $settings['moneroo_api_key'] ?? '') }}"
                        class="input font-mono text-sm" placeholder="mk_live_xxxxxxxxxxxxxxxx" autocomplete="off" />
                    <p class="mt-1 text-xs text-gray-400">Visible dans moneroo.io → Settings → API Keys</p>
                </div>
                <div>
                    <label class="label">Secret Webhook Moneroo (MONEROO_WEBHOOK_SECRET)</label>
                    <input type="text" name="moneroo_webhook_secret" value="{{ old('moneroo_webhook_secret', $settings['moneroo_webhook_secret'] ?? '') }}"
                        class="input font-mono text-sm" placeholder="whsec_xxxxxxxxxxxxxxxx" autocomplete="off" />
                    <p class="mt-1 text-xs text-gray-400">Généré dans moneroo.io → Webhooks → votre endpoint</p>
                </div>
                <div>
                    <label class="label">URL de base Moneroo</label>
                    <input type="url" name="moneroo_base_url" value="{{ old('moneroo_base_url', $settings['moneroo_base_url'] ?? 'https://api.moneroo.io/v1') }}"
                        class="input font-mono text-sm" />
                    <p class="mt-1 text-xs text-gray-400">Ne pas modifier sauf instruction de Moneroo</p>
                </div>
                <div class="rounded-lg border border-gray-200 bg-gray-50 p-4">
                    <p class="text-xs font-semibold text-gray-600 mb-2">URL Webhook à configurer dans Moneroo :</p>
                    <code class="block rounded bg-gray-900 px-3 py-2 text-xs text-green-400 select-all break-all">{{ url('api/webhooks/moneroo') }}</code>
                </div>
            </div>
        </div>

        {{-- ODIBOT --}}
        <div class="rounded-xl border border-indigo-200 bg-indigo-50/50 p-6 shadow-sm">
            <div class="mb-5 flex items-center gap-3">
                <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-indigo-600 text-white">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                </div>
                <div>
                    <h2 class="text-base font-bold text-gray-900">ODIBOT — Assistant IA gratuit avec ebook</h2>
                    <p class="text-xs text-indigo-700">Uploadez l'APK du bot. Les clients qui achètent un ebook peuvent le télécharger gratuitement.</p>
                </div>
            </div>
            <div class="space-y-4">
                @if (!empty($settings['odibot_apk_path']))
                <div class="rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">
                    <p class="font-semibold">APK actuel : {{ basename($settings['odibot_apk_path']) }}</p>
                    <p class="text-xs mt-1">
                        Taille : {{ number_format(($settings['odibot_apk_size'] ?? 0) / 1024 / 1024, 1) }} Mo
                        @if (!empty($settings['odibot_version'])) — Version : {{ $settings['odibot_version'] }}@endif
                    </p>
                    <a href="{{ route('odibot.download') }}" target="_blank" class="mt-2 inline-block text-xs font-semibold text-indigo-600 hover:underline">Tester le téléchargement</a>
                </div>
                @endif
                <div>
                    <label class="label">Fichier APK du bot (remplacer)</label>
                    <input type="file" name="odibot_apk" accept=".apk,application/vnd.android.package-archive" class="input-file" />
                    <p class="mt-1 text-xs text-gray-400">Fichier .apk uniquement. Max 200 Mo.</p>
                </div>
                <div>
                    <label class="label">Version du bot (ex: 1.0.0)</label>
                    <input type="text" name="odibot_version" value="{{ old('odibot_version', $settings['odibot_version'] ?? '') }}" class="input" placeholder="1.0.0" />
                </div>
                <div>
                    <label class="label">Description du bot (affichée publiquement)</label>
                    <textarea name="odibot_description" rows="4" class="input" placeholder="ODIBOT est votre assistant IA personnel...">{{ old('odibot_description', $settings['odibot_description'] ?? '') }}</textarea>
                </div>
            </div>
        </div>

        {{-- Sécurité Admin --}}
        <div class="rounded-xl border border-red-100 bg-white p-6 shadow-sm">
            <h2 class="mb-5 text-base font-bold text-gray-900">Sécurité &amp; Accès Admin</h2>
            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label class="label">Email admin</label>
                    <input type="email" name="admin_email" value="{{ old('admin_email', $settings['admin_email'] ?? config('app.admin_email')) }}" class="input" />
                </div>
                <div>
                    <label class="label">Nouveau mot de passe admin (laisser vide = pas de changement)</label>
                    <input type="password" name="admin_password" class="input" autocomplete="new-password" placeholder="••••••••" />
                </div>
            </div>
        </div>

        {{-- Meta Pixel --}}
        <div class="rounded-xl border border-blue-200 bg-blue-50/50 p-6 shadow-sm">
            <div class="mb-5 flex items-center gap-3">
                <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-blue-600 text-white">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                </div>
                <div>
                    <h2 class="text-base font-bold text-gray-900">Meta Pixel (Facebook/Instagram Ads)</h2>
                    <p class="text-xs text-blue-700">Trackez les conversions sur votre site avec le Meta Pixel.</p>
                </div>
            </div>
            <div class="space-y-4">
                <div>
                    <label class="label">Meta Pixel ID</label>
                    <input type="text" name="meta_pixel_id" value="{{ old('meta_pixel_id', $settings['meta_pixel_id'] ?? '') }}"
                        class="input font-mono text-sm" placeholder="123456789012345" autocomplete="off" />
                    <p class="mt-1 text-xs text-gray-400">Ex: 123456789012345 (15 chiffres). Laissez vide pour désactiver le Pixel.</p>
                </div>
                <div class="rounded-lg border border-blue-200 bg-white px-4 py-3 text-sm text-blue-800">
                    <p class="font-semibold">Comment obtenir votre Pixel ID :</p>
                    <ol class="mt-2 list-decimal list-inside space-y-1 text-xs text-gray-600">
                        <li>Allez sur <a href="https://business.facebook.com/pixel" target="_blank" class="font-bold text-blue-600 hover:underline">business.facebook.com/pixel</a></li>
                        <li>Créez un nouveau Pixel ou utilisez un existant</li>
                        <li>Copiez l'ID (ex: 123456789012345)</li>
                        <li>Collez-le ici et sauvegardez</li>
                    </ol>
                </div>
            </div>
        </div>

        {{-- Réseaux Sociaux --}}
        <div class="rounded-xl border border-purple-200 bg-purple-50/50 p-6 shadow-sm">
            <div class="mb-5 flex items-center gap-3">
                <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-purple-600 text-white">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/></svg>
                </div>
                <div>
                    <h2 class="text-base font-bold text-gray-900">Réseaux Sociaux</h2>
                    <p class="text-xs text-purple-700">Liens vers vos profils sociaux (affichés dans le footer).</p>
                </div>
            </div>
            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label class="label">Facebook</label>
                    <input type="url" name="facebook_url" value="{{ old('facebook_url', $settings['facebook_url'] ?? '') }}"
                        class="input" placeholder="https://facebook.com/votre-page" />
                </div>
                <div>
                    <label class="label">Instagram</label>
                    <input type="url" name="instagram_url" value="{{ old('instagram_url', $settings['instagram_url'] ?? '') }}"
                        class="input" placeholder="https://instagram.com/votre-compte" />
                </div>
                <div>
                    <label class="label">Twitter / X</label>
                    <input type="url" name="twitter_url" value="{{ old('twitter_url', $settings['twitter_url'] ?? '') }}"
                        class="input" placeholder="https://x.com/votre-compte" />
                </div>
                <div>
                    <label class="label">LinkedIn</label>
                    <input type="url" name="linkedin_url" value="{{ old('linkedin_url', $settings['linkedin_url'] ?? '') }}"
                        class="input" placeholder="https://linkedin.com/in/votre-profil" />
                </div>
                <div>
                    <label class="label">YouTube</label>
                    <input type="url" name="youtube_url" value="{{ old('youtube_url', $settings['youtube_url'] ?? '') }}"
                        class="input" placeholder="https://youtube.com/@votre-chaine" />
                </div>
            </div>
        </div>

        {{-- Systeme.io --}}
        <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
            <h2 class="mb-1 text-base font-bold text-gray-900">Intégration Systeme.io</h2>
            <p class="mb-5 text-sm text-gray-500">Collez ici le code embed de votre formulaire Systeme.io (pop-up ou inline). Il s'affichera sur la page d'accueil et/ou le checkout.</p>
            <div class="space-y-4">
                <div>
                    <label class="label">ID du formulaire Systeme.io (optionnel, pour tracking)</label>
                    <input type="text" name="systemeio_form_id" value="{{ old('systemeio_form_id', $settings['systemeio_form_id'] ?? '') }}" class="input" placeholder="ex: 123456" />
                </div>
                <div>
                    <label class="label">Code embed HTML Systeme.io</label>
                    <textarea name="systemeio_embed" rows="6" class="input font-mono text-xs" placeholder='<script src="https://systeme.io/..."></script>'>{{ old('systemeio_embed', $settings['systemeio_embed'] ?? '') }}</textarea>
                    <p class="mt-1 text-xs text-gray-400">Copiez le code depuis Systeme.io → Tunnels / Formulaires → Intégrer.</p>
                </div>
            </div>
        </div>

        <div class="flex items-center justify-between border-t border-gray-200 pt-6">
            <p class="text-sm text-gray-400">Les modifications sont appliquées immédiatement.</p>
            <button type="submit" class="rounded-xl bg-indigo-600 px-8 py-3 text-sm font-bold text-white shadow transition hover:bg-indigo-700">
                💾 Enregistrer tous les paramètres
            </button>
        </div>
    </form>
</div>

<style>
.label  { display:block; margin-bottom:6px; font-size:.875rem; font-weight:500; color:#374151; }
.input  { width:100%; border-radius:.5rem; border:1px solid #d1d5db; padding:.625rem 1rem; font-size:.875rem; outline:none; }
.input:focus { border-color:#6366f1; box-shadow:0 0 0 2px #e0e7ff; }
.input-file { width:100%; border-radius:.5rem; border:1px solid #d1d5db; padding:.5rem; font-size:.875rem; }
</style>
@endsection
