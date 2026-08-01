import dotenv from 'dotenv';
dotenv.config();

const SITE_API_URL = process.env.SITE_API_URL || 'https://tpfcedp.com/api';
const SITE_API_TOKEN = process.env.SITE_API_TOKEN || '';

async function apiGet(path) {
  const res = await fetch(`${SITE_API_URL}${path}`, {
    headers: {
      'Authorization': `Bearer ${SITE_API_TOKEN}`,
      'Accept': 'application/json',
    },
  });
  if (!res.ok) throw new Error(`API ${path}: ${res.status}`);
  return res.json();
}

async function apiPost(path, body) {
  const res = await fetch(`${SITE_API_URL}${path}`, {
    method: 'POST',
    headers: {
      'Authorization': `Bearer ${SITE_API_TOKEN}`,
      'Accept': 'application/json',
      'Content-Type': 'application/json',
    },
    body: JSON.stringify(body),
  });
  if (!res.ok) throw new Error(`API POST ${path}: ${res.status}`);
  return res.json();
}

let catalogCache = null;
let catalogCacheTime = 0;
const CACHE_TTL = 10 * 60 * 1000; // 10 minutes

export async function getCatalog() {
  const now = Date.now();
  if (catalogCache && now - catalogCacheTime < CACHE_TTL) {
    return catalogCache;
  }
  const data = await apiGet('/catalog');
  catalogCache = data;
  catalogCacheTime = now;
  return data;
}

export async function getProduct(slug) {
  return apiGet(`/products/${slug}`);
}

export async function createCheckout({ productId, packId, customerName, customerEmail, customerPhone }) {
  return apiPost('/checkout', {
    product_id: productId,
    pack_id: packId,
    customer_name: customerName,
    customer_email: customerEmail,
    customer_phone: customerPhone,
  });
}

export async function getStats() {
  return apiGet('/stats');
}

export async function getRecentOrders(limit = 20) {
  return apiGet(`/orders?limit=${limit}`);
}

export function formatCatalogForPrompt(catalog) {
  if (!catalog || !catalog.products) return 'Catalogue indisponible pour le moment.';

  const lines = [];
  for (const p of catalog.products.slice(0, 30)) {
    const price = p.discount_price || p.price;
    const discount = p.discount_price ? ` (au lieu de ${p.price} FCFA)` : '';
    lines.push(`- [ID:${p.id}] ${p.name} | ${price} FCFA${discount} | ${p.product_type} | ${p.short_description || p.description?.substring(0, 100) || ''}`);
  }

  if (catalog.packs && catalog.packs.length > 0) {
    lines.push('\n--- PACKS ---');
    for (const pk of catalog.packs.slice(0, 10)) {
      const price = pk.discount_price || pk.price;
      lines.push(`- [PACK:${pk.id}] ${pk.name} | ${price} FCFA | ${pk.short_description || ''}`);
    }
  }

  return lines.join('\n');
}

export function getProductImageUrl(product) {
  if (!product || !product.image) return null;
  if (product.image.startsWith('http')) return product.image;
  return `https://tpfcedp.com/storage/${product.image}`;
}
