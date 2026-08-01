// Charger Alpine.js
import Alpine from 'alpinejs';

window.Alpine = Alpine;
Alpine.start();

const currencyMap = {
    FR: 'EUR',
    BE: 'EUR',
    CH: 'CHF',
    MA: 'MAD',
    CI: 'XAF',
    SN: 'XOF',
    ML: 'XOF',
    BF: 'XOF',
    TG: 'XOF',
    BJ: 'XOF',
    NE: 'XOF',
    CM: 'XAF',
    GA: 'XAF',
    QC: 'CAD',
    US: 'USD',
    CA: 'CAD',
    GB: 'GBP',
    NG: 'NGN',
};

const conversionRates = {
    XAF: 1,
    XOF: 1,
    EUR: 0.00152,
    USD: 0.00162,
    CAD: 0.00212,
    GBP: 0.00131,
    CHF: 0.00151,
    MAD: 0.017,
    NGN: 0.78,
};

function detectCurrency() {
    const locale = navigator.language || 'fr-FR';
    const region = locale.split('-')[1] || 'FR';
    const currency = currencyMap[region.toUpperCase()] || 'XAF';
    const rate = conversionRates[currency] || 1;
    return { currency, rate, locale };
}

function formatCurrency(value, locale, currency) {
    return new Intl.NumberFormat(locale, {
        style: 'currency',
        currency,
        maximumFractionDigits: 0,
    }).format(value);
}

function updatePrices() {
    const { currency, rate, locale } = detectCurrency();
    document.querySelectorAll('[data-price-xaf]').forEach((element) => {
        const baseValue = Number(element.dataset.priceXaf);
        if (!Number.isFinite(baseValue)) {
            return;
        }
        element.textContent = formatCurrency(Math.round(baseValue * rate), locale, currency);
    });
    document.querySelectorAll('[data-discount-xaf]').forEach((element) => {
        const baseValue = Number(element.dataset.discountXaf);
        if (!Number.isFinite(baseValue)) {
            return;
        }
        element.textContent = formatCurrency(Math.round(baseValue * rate), locale, currency);
    });
    const label = document.querySelector('#currency-label');
    if (label) {
        label.textContent = currency;
    }
}

document.addEventListener('DOMContentLoaded', updatePrices);
