import dotenv from 'dotenv';
dotenv.config();

import { getPendingFollowups, markFollowupSent, getConversation, updateConversation, addMessage, addFollowup } from './conversation.js';
import { sendText } from './whatsapp.js';
import { getCatalog, getProduct, formatCatalogForPrompt, getProductImageUrl } from './catalog.js';
import { generateResponse } from './ai.js';
import { SYSTEM_PROMPT_CLIENT } from './prompts.js';

const FOLLOWUP_DELAY_HOURS = parseInt(process.env.FOLLOWUP_DELAY_HOURS || '48', 10);

export async function runFollowups() {
  const pending = getPendingFollowups();
  console.log(`[Followup] ${pending.length} relance(s) en attente`);

  for (const f of pending) {
    try {
      const conv = getConversation(f.phone);
      if (!conv || conv.followupSent) {
        markFollowupSent(f.phone, f.index);
        continue;
      }

      const catalog = await getCatalog();
      const catalogText = formatCatalogForPrompt(catalog);
      const systemPrompt = SYSTEM_PROMPT_CLIENT.replace('{CATALOG}', catalogText);

      const product = catalog.products?.find(p => p.id === f.productId);
      const productName = product ? product.name : 'le produit qui t\'intéressait';

      const followupMessage = `Bonjour ! J'espère que vous allez bien 😊 Je reviens vers vous concernant ${productName}. Avez-vous eu le temps d'y réfléchir ? Si vous avez des questions, n'hésitez pas, je suis là pour vous aider !`;

      await sendText(f.phone, followupMessage);
      addMessage(f.phone, 'assistant', followupMessage);

      updateConversation(f.phone, { followupSent: true });
      markFollowupSent(f.phone, f.index);

      console.log(`[Followup] Relance envoyée à ${f.phone} pour le produit ${f.productId}`);
    } catch (err) {
      console.error(`[Followup] Erreur pour ${f.phone}:`, err.message);
    }
  }
}

export function scheduleFollowup(phone, productId) {
  const scheduledAt = new Date(Date.now() + FOLLOWUP_DELAY_HOURS * 60 * 60 * 1000).toISOString();
  addFollowup(phone, productId, scheduledAt);
  console.log(`[Followup] Relance planifiée pour ${phone} dans ${FOLLOWUP_DELAY_HOURS}h`);
}
