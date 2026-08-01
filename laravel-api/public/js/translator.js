// TPF Translation System - FR (default) / EN
(function () {
  'use strict';

  const STORAGE_KEY = 'tpf_lang';
  const DEFAULT_LANG = 'fr';

  // Dictionary: FR text → EN text
  const translations = {
    // Navbar
    'Accueil': 'Home',
    'Boutique': 'Shop',
    'Packs': 'Packs',
    'À propos': 'About',
    'Contact': 'Contact',
    'Panier': 'Cart',

    // Hero / Home
    'Voir les produits': 'View products',
    'Nous contacter': 'Contact us',
    'Livraison instantanee': 'Instant delivery',
    'Livraison instantanée': 'Instant delivery',
    'Paiement securise': 'Secure payment',
    'Paiement sécurisé': 'Secure payment',
    'Support WhatsApp': 'WhatsApp support',
    'Boostez vos competences digitales': 'Boost your digital skills',
    'Boostez vos compétences digitales': 'Boost your digital skills',
    'Ebooks, templates et formations — livres instantanement apres paiement.': 'Ebooks, templates and courses — delivered instantly after payment.',
    'Ebooks, templates et formations — livrés instantanément après paiement.': 'Ebooks, templates and courses — delivered instantly after payment.',

    // Shop
    'produit(s) disponible(s)': 'product(s) available',
    'Filtrer les produits': 'Filter products',
    'Recherche': 'Search',
    'Nom du produit...': 'Product name...',
    'Type de produit': 'Product type',
    'Tous': 'All',
    'Ebooks': 'Ebooks',
    'Formations': 'Courses',
    'Templates': 'Templates',
    'Catégories': 'Categories',
    'Catégorie': 'Category',
    'Ajouter au panier': 'Add to cart',
    'Acheter maintenant': 'Buy now',
    'Voir les détails': 'View details',
    'Voir plus': 'See more',
    'Voir tout': 'See all',
    'Produits populaires': 'Popular products',
    'Produits vedettes': 'Featured products',
    'Nouveautés': 'New arrivals',
    'En vedette': 'Featured',
    'Promotion': 'Sale',
    'Prix': 'Price',
    'Prix spécial': 'Special price',

    // Product detail
    'Ce que vous obtenez': 'What you get',
    'Description': 'Description',
    'Description détaillée': 'Detailed description',
    'Produits similaires': 'Similar products',
    'Vous aimerez aussi': 'You may also like',
    'ODIBOT inclus gratuitement avec cet ebook !': 'ODIBOT included free with this ebook!',
    'Achetez cet ebook et recevez': 'Buy this ebook and get',
    'votre assistant IA personnel': 'your personal AI assistant',
    'gratuitement. Posez vos questions, apprenez plus vite, et progressez dans vos projets.': 'for free. Ask questions, learn faster, and progress in your projects.',
    'Retour à la boutique': 'Back to shop',

    // Cart
    'Votre panier': 'Your cart',
    'Panier vide': 'Empty cart',
    'Votre panier est vide': 'Your cart is empty',
    'Continuer mes achats': 'Continue shopping',
    'Sous-total': 'Subtotal',
    'Total': 'Total',
    'Procéder au paiement': 'Proceed to checkout',
    'Supprimer': 'Remove',
    'Quantité': 'Quantity',

    // Checkout
    'Paiement': 'Payment',
    'Informations de paiement': 'Payment information',
    'Nom complet': 'Full name',
    'Adresse email': 'Email address',
    'Numéro de téléphone': 'Phone number',
    'Payer': 'Pay',
    'Payer maintenant': 'Pay now',
    'Commande': 'Order',
    'Récapitulatif de la commande': 'Order summary',
    'Merci pour votre achat !': 'Thank you for your purchase!',
    'Votre commande a été confirmée': 'Your order has been confirmed',
    'Télécharger': 'Download',
    'Télécharger votre produit': 'Download your product',

    // About
    'À propos de Tech Pro Futur': 'About Tech Pro Futur',
    'Notre Mission': 'Our Mission',
    'Notre Vision': 'Our Vision',
    'Nos Valeurs': 'Our Values',
    'Bienvenue sur Tech Pro Futur': 'Welcome to Tech Pro Futur',

    // Footer
    'Suivez-nous': 'Follow us',
    'Tous droits réservés': 'All rights reserved',
    'Conçu par': 'Designed by',

    // Contact
    'Contactez-nous': 'Contact us',
    'Envoyer un message': 'Send a message',
    'Votre nom': 'Your name',
    'Votre email': 'Your email',
    'Votre message': 'Your message',
    'Envoyer': 'Send',

    // Misc
    'Chargement...': 'Loading...',
    'Voir plus de produits': 'See more products',
    'Découvrir': 'Discover',
    'Explorer': 'Explore',
    'En savoir plus': 'Learn more',
    'Voir notre catalogue': 'View our catalog',
    'Témoignages': 'Testimonials',
    'Ce que disent nos clients': 'What our customers say',
    'Pourquoi nous choisir ?': 'Why choose us?',
    'Livraison immédiate': 'Immediate delivery',
    'Paiement 100% sécurisé': '100% secure payment',
    'Support 7j/7': '24/7 support',
    'Produits de qualité': 'Quality products',
    'Satisfait ou remboursé': 'Money-back guarantee',
    'Garantie satisfaction': 'Satisfaction guarantee',
  };

  // Reverse translations for switching back to FR
  const reverseTranslations = {};
  Object.entries(translations).forEach(([fr, en]) => {
    reverseTranslations[en] = fr;
  });

  function getCurrentLang() {
    return localStorage.getItem(STORAGE_KEY) || DEFAULT_LANG;
  }

  function setLang(lang) {
    localStorage.setItem(STORAGE_KEY, lang);
    applyTranslations(lang);
    updateSwitcher(lang);
  }

  function translateText(text, lang) {
    if (!text) return text;
    const trimmed = text.trim();
    if (lang === 'en') {
      // Try exact match first
      if (translations[trimmed]) return translations[trimmed];
      // Try case-insensitive
      for (const [fr, en] of Object.entries(translations)) {
        if (fr.toLowerCase() === trimmed.toLowerCase()) return en;
      }
    } else if (lang === 'fr') {
      if (reverseTranslations[trimmed]) return reverseTranslations[trimmed];
      for (const [en, fr] of Object.entries(reverseTranslations)) {
        if (en.toLowerCase() === trimmed.toLowerCase()) return fr;
      }
    }
    return null;
  }

  function applyTranslations(lang) {
    // Translate elements with data-i18n
    document.querySelectorAll('[data-i18n]').forEach(el => {
      const key = el.getAttribute('data-i18n');
      if (lang === 'en' && translations[key]) {
        el.textContent = translations[key];
      } else if (lang === 'fr' && reverseTranslations[key]) {
        el.textContent = reverseTranslations[key];
      }
    });

    // Auto-translate text nodes
    const walker = document.createTreeWalker(
      document.body,
      NodeFilter.SHOW_TEXT,
      {
        acceptNode: function (node) {
          const parent = node.parentElement;
          if (!parent) return NodeFilter.FILTER_REJECT;
          // Skip script, style, and translation switcher
          if (parent.tagName === 'SCRIPT' || parent.tagName === 'STYLE') return NodeFilter.FILTER_REJECT;
          if (parent.closest('.lang-switcher')) return NodeFilter.FILTER_REJECT;
          const text = node.textContent.trim();
          if (text.length < 2) return NodeFilter.FILTER_REJECT;
          return NodeFilter.FILTER_ACCEPT;
        }
      }
    );

    const textNodes = [];
    let node;
    while ((node = walker.nextNode())) {
      textNodes.push(node);
    }

    textNodes.forEach(textNode => {
      const original = textNode.textContent.trim();
      const translated = translateText(original, lang);
      if (translated) {
        // Preserve surrounding whitespace
        const leading = textNode.textContent.match(/^\s*/)[0];
        const trailing = textNode.textContent.match(/\s*$/)[0];
        textNode.textContent = leading + translated + trailing;
      }
    });

    // Update html lang attribute
    document.documentElement.lang = lang;
  }

  function updateSwitcher(lang) {
    document.querySelectorAll('.lang-switcher button').forEach(btn => {
        if (btn.dataset.lang === lang) {
          btn.classList.add('bg-indigo-600', 'text-white');
          btn.classList.remove('text-gray-600');
        } else {
          btn.classList.remove('bg-indigo-600', 'text-white');
          btn.classList.add('text-gray-600');
        }
    });
  }

  // Wait for DOM
  function init() {
    const lang = getCurrentLang();
    if (lang === 'en') {
      applyTranslations('en');
    }
    updateSwitcher(lang);

    // Bind switcher buttons
    document.querySelectorAll('.lang-switcher button').forEach(btn => {
      btn.addEventListener('click', function () {
        setLang(this.dataset.lang);
      });
    });
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }

  // Re-apply on Alpine.js updates
  document.addEventListener('alpine:initialized', () => {
    const lang = getCurrentLang();
    if (lang === 'en') applyTranslations('en');
  });
})();
