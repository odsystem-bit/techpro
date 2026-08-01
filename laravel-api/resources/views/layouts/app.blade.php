<!DOCTYPE html>
<html lang="fr" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Tech Pro Futur')</title>
    <meta name="description" content="@yield('description', 'Ebooks, templates et formations pour professionnels du digital en Afrique.')">

    {{-- Meta Pixel --}}
    @include('partials.meta-pixel')

    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    @if(file_exists(public_path('build/assets/app-C1O9397X.js')))
    <script defer src="{{ asset('build/assets/app-C1O9397X.js') }}"></script>
    @endif
    
{{-- Google Translate Widget --}}    <script type="text/javascript">    function googleTranslateElementInit() {        new google.translate.TranslateElement({            pageLanguage: "fr",            includedLanguages: "fr,en",            layout: google.translate.TranslateElement.InlineLayout.SIMPLE,            autoDisplay: false        }, "google_translate_element");    }    </script>    <script src="https://translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>    <style>        .goog-te-banner-frame.skiptranslate { display: none !important; }        body { top: 0 !important; }        .goog-tooltip, .goog-tooltip:hover { display: none !important; }        .goog-text-highlight { background: none !important; box-shadow: none !important; }        #google_translate_element { display: none; }        .goog-te-gadget { font-size: 0 !important; }        .goog-te-gadget .goog-te-combo { display: none; }    </style>
</head>
<body class="min-h-screen bg-gray-50 text-gray-900 antialiased">
@php
    use App\Models\SiteSetting;
    $siteName    = SiteSetting::get('site_name', 'Tech Pro Futur');
    $siteTagline = SiteSetting::get('site_tagline', 'ebooks · templates · formations');
    $waNumber    = SiteSetting::get('whatsapp_number', '');
    $waClean     = preg_replace('/[^0-9]/', '', $waNumber);
    $footerText  = SiteSetting::get('footer_text', '');
    $logoPath    = SiteSetting::get('logo', '');
    $fbUrl       = SiteSetting::get('facebook_url', '');
    $igUrl       = SiteSetting::get('instagram_url', '');
    $twUrl       = SiteSetting::get('twitter_url', '');
    $liUrl       = SiteSetting::get('linkedin_url', '');
    $ytUrl       = SiteSetting::get('youtube_url', '');
@endphp

    {{-- Navbar --}}
    <header class="sticky top-0 z-40 border-b border-gray-200 bg-white/95 backdrop-blur-sm shadow-sm">
        <div class="mx-auto flex max-w-7xl items-center justify-between gap-4 px-4 py-3 sm:px-6 lg:px-8">
            <a href="{{ route('home') }}" class="flex items-center gap-3">
                @if ($logoPath)
                    <img src="{{ asset('storage/' . $logoPath) }}" class="h-10 w-auto object-contain" alt="{{ $siteName }}" />
                @else
                    <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-indigo-600 text-sm font-bold text-white shadow">TP</span>
                @endif
                <div>
                    <p class="text-sm font-bold uppercase tracking-widest text-gray-900">{{ $siteName }}</p>
                    <p class="text-xs text-gray-400">{{ $siteTagline }}</p>
                </div>
            </a>

            <nav class="hidden items-center gap-6 text-sm font-medium text-gray-600 md:flex">
                <a href="{{ route('home') }}" class="transition hover:text-indigo-600">Accueil</a>
                <a href="{{ route('shop.index') }}" class="transition hover:text-indigo-600">Boutique</a>
                <a href="{{ route('shop.index', ['type' => 'pack']) }}" class="flex items-center gap-1 rounded-full bg-amber-100 px-3 py-1 text-amber-700 transition hover:bg-amber-200">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                    Packs
                </a>
                <a href="{{ route('apropos') }}" class="transition hover:text-indigo-600">À propos</a>
                <a href="{{ route('home') }}#contact" class="transition hover:text-indigo-600">Contact</a>
            </nav>

            <div class="flex items-center gap-3">
                {{-- Language Switcher (Google Translate) --}}
                <div id="google_translate_element" style="display:none"></div>
                <div class="lang-switcher flex items-center gap-0.5 rounded-lg border border-gray-200 bg-white p-0.5">
                    <button id="btn-fr" onclick="setLang('fr')" class="rounded-md px-2 py-1 text-xs font-bold transition bg-indigo-600 text-white">FR</button>
                    <button id="btn-en" onclick="setLang('en')" class="rounded-md px-2 py-1 text-xs font-bold transition text-gray-600">EN</button>
                </div>
                <script>
                function setLang(lang) {
                    var sel = document.querySelector(".goog-te-combo");
                    if (sel) {
                        sel.value = lang;
                        sel.dispatchEvent(new Event("change"));
                    }
                    var btnFr = document.getElementById("btn-fr");
                    var btnEn = document.getElementById("btn-en");
                    if (lang === "fr") {
                        btnFr.classList.add("bg-indigo-600", "text-white");
                        btnFr.classList.remove("text-gray-600");
                        btnEn.classList.remove("bg-indigo-600", "text-white");
                        btnEn.classList.add("text-gray-600");
                    } else {
                        btnEn.classList.add("bg-indigo-600", "text-white");
                        btnEn.classList.remove("text-gray-600");
                        btnFr.classList.remove("bg-indigo-600", "text-white");
                        btnFr.classList.add("text-gray-600");
                    }
                    localStorage.setItem("tpf_lang", lang);
                }
                document.addEventListener("DOMContentLoaded", function() {
                    var savedLang = localStorage.getItem("tpf_lang");
                    if (savedLang === "en") {
                        setTimeout(function() { setLang("en"); }, 1500);
                    }
                });
                </script>
                {{-- Cart --}}
                @php $cartCount = collect(session('cart', []))->sum('quantity'); @endphp
                <a href="{{ route('shop.cart') }}" class="relative inline-flex items-center gap-2 rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm transition hover:border-indigo-400 hover:text-indigo-600">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    Panier
                    @if ($cartCount > 0)
                        <span class="absolute -right-1.5 -top-1.5 flex h-5 w-5 items-center justify-center rounded-full bg-indigo-600 text-xs font-bold text-white">{{ $cartCount }}</span>
                    @endif
                </a>
            </div>
        </div>
    </header>

    {{-- Flash messages --}}
    @if (session('success') || session('info') || session('error'))
    <div class="mx-auto max-w-7xl px-4 pt-4 sm:px-6 lg:px-8">
        @if (session('success'))
            <div class="rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">{{ session('success') }}</div>
        @endif
        @if (session('info'))
            <div class="rounded-lg border border-blue-200 bg-blue-50 px-4 py-3 text-sm text-blue-800">{{ session('info') }}</div>
        @endif
        @if (session('error'))
            <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">{{ session('error') }}</div>
        @endif
    </div>
    @endif

    <main>
        @yield('content')
    </main>

    {{-- Footer --}}
    <footer id="contact" class="mt-20 border-t border-gray-200 bg-white">
        <div class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
            <div class="grid gap-8 sm:grid-cols-3">
                <div>
                    <div class="flex items-center gap-2">
                        @if ($logoPath)
                            <img src="{{ asset('storage/' . $logoPath) }}" class="h-9 w-auto object-contain" alt="{{ $siteName }}" />
                        @else
                            <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-indigo-600 text-sm font-bold text-white">TP</span>
                        @endif
                        <span class="font-bold text-gray-900">{{ $siteName }}</span>
                    </div>
                    <p class="mt-3 text-sm text-gray-500">{{ $footerText ?: 'Ressources numériques premium pour professionnels du digital en Afrique.' }}</p>
                </div>
                <div>
                    <h3 class="text-sm font-semibold text-gray-900">Navigation</h3>
                    <ul class="mt-3 space-y-2 text-sm text-gray-500">
                        <li><a href="{{ route('shop.index') }}" class="hover:text-indigo-600">Boutique</a></li>
                        <li><a href="{{ route('shop.index', ['type' => 'pack']) }}" class="hover:text-amber-600">Packs</a></li>
                        <li><a href="{{ route('apropos') }}" class="hover:text-indigo-600">À propos</a></li>
                        <li><a href="{{ route('shop.cart') }}" class="hover:text-indigo-600">Mon panier</a></li>
                    </ul>
                </div>
                <div>
                    <h3 class="text-sm font-semibold text-gray-900">Contact</h3>
                    <ul class="mt-3 space-y-2 text-sm text-gray-500">
                        <li>
                            <a href="https://wa.me/{{ $waClean }}" target="_blank" class="inline-flex items-center gap-2 hover:text-green-600">
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.031-.967-.273-.1-.472-.149-.672.15-.198.297-.767.967-.94 1.164-.173.198-.347.223-.644.075-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.52.149-.173.198-.298.298-.497.1-.198.05-.372-.025-.521-.075-.149-.672-1.612-.92-2.206-.242-.579-.487-.5-.672-.51l-.573-.01c-.198 0-.52.075-.792.372s-1.04 1.016-1.04 2.479 1.064 2.876 1.213 3.074c.149.198 2.096 3.2 5.077 4.487.71.307 1.262.49 1.693.627.712.227 1.36.195 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.413z"/></svg>
                                WhatsApp
                            </a>
                        </li>
                    </ul>
                </div>
                <div>
                    <h3 class="text-sm font-semibold text-gray-900">Suivez-nous</h3>
                    <div class="mt-3 flex gap-3">
                        @if ($fbUrl)
                        <a href="{{ $fbUrl }}" target="_blank" class="flex h-8 w-8 items-center justify-center rounded-full bg-blue-600 text-white transition hover:bg-blue-700">
                            <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                        </a>
                        @endif
                        @if ($igUrl)
                        <a href="{{ $igUrl }}" target="_blank" class="flex h-8 w-8 items-center justify-center rounded-full bg-gradient-to-br from-purple-600 via-pink-500 to-orange-400 text-white transition hover:opacity-90">
                            <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
                        </a>
                        @endif
                        @if ($twUrl)
                        <a href="{{ $twUrl }}" target="_blank" class="flex h-8 w-8 items-center justify-center rounded-full bg-black text-white transition hover:bg-gray-800">
                            <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                        </a>
                        @endif
                        @if ($liUrl)
                        <a href="{{ $liUrl }}" target="_blank" class="flex h-8 w-8 items-center justify-center rounded-full bg-blue-700 text-white transition hover:bg-blue-800">
                            <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg>
                        </a>
                        @endif
                        @if ($ytUrl)
                        <a href="{{ $ytUrl }}" target="_blank" class="flex h-8 w-8 items-center justify-center rounded-full bg-red-600 text-white transition hover:bg-red-700">
                            <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24"><path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg>
                        </a>
                        @endif
                    </div>
                </div>
            </div>
            <div class="mt-8 border-t border-gray-100 pt-6 text-center text-xs text-gray-400">
                © {{ date("Y") }} {{ $siteName }}. Tous droits réservés. — Conçu par <a href="https://wa.me/2290152168223" target="_blank" class="text-gray-400 hover:text-green-600 transition">OD SYSTEME</a>
            </div>
        </div>
    </footer>

    {{-- WhatsApp FAB --}}
    @if ($waClean)
    <a href="https://wa.me/{{ $waClean }}" target="_blank"
       class="fixed bottom-6 right-6 z-50 flex h-14 w-14 items-center justify-center rounded-full bg-green-500 text-white shadow-xl transition hover:bg-green-400">
        <svg class="h-7 w-7" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.031-.967-.273-.1-.472-.149-.672.15-.198.297-.767.967-.94 1.164-.173.198-.347.223-.644.075-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.52.149-.173.198-.298.298-.497.1-.198.05-.372-.025-.521-.075-.149-.672-1.612-.92-2.206-.242-.579-.487-.5-.672-.51l-.573-.01c-.198 0-.52.075-.792.372s-1.04 1.016-1.04 2.479 1.064 2.876 1.213 3.074c.149.198 2.096 3.2 5.077 4.487.71.307 1.262.49 1.693.627.712.227 1.36.195 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.413z"/><path d="M12 0C5.373 0 0 5.373 0 12c0 2.136.559 4.14 1.535 5.875L0 24l6.283-1.507A11.945 11.945 0 0012 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 22c-1.896 0-3.678-.5-5.214-1.374l-.374-.218-3.728.895.938-3.626-.243-.386A9.96 9.96 0 012 12C2 6.477 6.477 2 12 2s10 4.477 10 10-4.477 10-10 10z"/></svg>
    </a>
    @endif

</body>
</html>
