import dotenv from 'dotenv';
dotenv.config();

import express from 'express';
import crypto from 'crypto';
import cron from 'node-cron';

import { sendText, sendMedia, formatPhone } from './whatsapp.js';
import { generateResponse, detectIntent } from './ai.js';
import { getCatalog, formatCatalogForPrompt, getProductImageUrl, createCheckout } from './catalog.js';
import { getConversation, addMessage, updateConversation } from './conversation.js';
import { SYSTEM_PROMPT_CLIENT } from './prompts.js';
import { handleOwnerMessage, isOwnerMessage } from './owner.js';
import { runFollowups, scheduleFollowup } from './followup.js';

const app = express();
app.use(express.urlencoded({ extended: true }));
app.use(express.json());

const PORT = process.env.PORT || 3000;
const OWNER_WHATSAPP = process.env.OWNER_WHATSAPP || 'whatsapp:+22900000000';

// ---- Webhook Twilio (réception messages WhatsApp) ----

app.post('/webhook', async (req, res) => {
  const from = req.body.From || '';
  const body = req.body.Body || '';
  const numMedia = parseInt(req.body.NumMedia || '0', 10);

  console.log(`[Webhook] From: ${from}, Body: ${body}, Media: ${numMedia}`);

  if (!from || !body) {
    return res.status(400).send('Missing From or Body');
  }

  // Vérifier si c'est le propriétaire
  if (isOwnerMessage(from)) {
    try {
      await handleOwnerMessage(from, body);
      return res.status(200).send('OK');
    } catch (err) {
      console.error('[Owner] Erreur:', err.message);
      return res.status(500).send('Error');
    }
  }

  // --- Traitement client ---
  try {
    await handleClientMessage(from, body);
    res.status(200).send('OK');
  } catch (err) {
    console.error('[Client] Erreur:', err.message);
    res.status(500).send('Error');
  }
});

async function handleClientMessage(from, body) {
  const conv = getConversation(from);
  addMessage(from, 'user', body);

  // Détecter l'intention
  const intent = await detectIntent(body);
  updateConversation(from, { intent });

  // Récupérer le catalogue
  const catalog = await getCatalog();
  const catalogText = formatCatalogForPrompt(catalog);
  const systemPrompt = SYSTEM_PROMPT_CLIENT.replace('{CATALOG}', catalogText);

  // Générer la réponse IA
  const response = await generateResponse(systemPrompt, conv.messages, body);
  addMessage(from, 'assistant', response);

  // Envoyer la réponse texte
  await sendText(from, response);

  // Si l'intention est "info" ou "conseil", essayer d'envoyer une image de produit
  if ((intent === 'info' || intent === 'conseil') && catalog.products?.length > 0) {
    const mentionedProduct = findMentionedProduct(body, catalog.products);
    if (mentionedProduct && mentionedProduct.image) {
      const imageUrl = getProductImageUrl(mentionedProduct);
      if (imageUrl) {
        await sendMedia(from, `Voici l'image de ${mentionedProduct.name} 👇`, imageUrl);
        updateConversation(from, {
          interestedProducts: [...new Set([...(conv.interestedProducts || []), mentionedProduct.id])],
        });
      }
    }
  }

  // Si l'intention est "achat", créer un lien de checkout
  if (intent === 'achat') {
    const mentionedProduct = findMentionedProduct(body, catalog.products);
    if (mentionedProduct) {
      try {
        const checkout = await createCheckout({
          productId: mentionedProduct.id,
          customerName: conv.clientName || from,
          customerEmail: '',
          customerPhone: from,
        });

        if (checkout.payment_url) {
          const paymentMsg = `Parfait ! Voici votre lien de paiement sécurisé pour ${mentionedProduct.name} (${mentionedProduct.discount_price || mentionedProduct.price} FCFA) :\n\n${checkout.payment_url}\n\nUne fois le paiement effectué, vous recevrez votre produit automatiquement. 🎉`;
          await sendText(from, paymentMsg);
          addMessage(from, 'assistant', paymentMsg);
          updateConversation(from, { offeredCheckout: true });

          // Planifier une relance si pas d'achat confirmé
          scheduleFollowup(from, mentionedProduct.id);
        }
      } catch (err) {
        console.error('[Checkout] Erreur:', err.message);
        await sendText(from, 'Je peux vous donner le lien direct de la boutique pour finaliser votre achat : https://tpfcedp.com');
      }
    }
  }

  // Si c'est une salutation, envoyer un message de bienvenue avec le catalogue
  if (intent === 'salutation' && conv.messages.length <= 2) {
    const welcomeMsg = `Bienvenue chez Tech Pro Futur ! 🚀\n\nNous proposons des formations, ebooks et templates pour développer votre business et vos compétences.\n\nQue recherchez-vous aujourd'hui ?`;
    await sendText(from, welcomeMsg);
    addMessage(from, 'assistant', welcomeMsg);
  }
}

function findMentionedProduct(message, products) {
  const msgLower = message.toLowerCase();
  // Chercher par nom exact ou partiel
  for (const p of products) {
    if (msgLower.includes(p.name.toLowerCase()) || msgLower.includes(p.slug)) {
      return p;
    }
  }
  // Chercher par mots-clés dans la description
  for (const p of products) {
    const desc = (p.short_description || p.description || '').toLowerCase();
    if (desc && msgLower.split(' ').some(w => w.length > 4 && desc.includes(w))) {
      return p;
    }
  }
  return null;
}

// ---- Route santé ----
app.get('/health', (req, res) => {
  res.json({ status: 'ok', timestamp: new Date().toISOString() });
});

// ---- Cron pour les relances ----
const FOLLOWUP_CRON = process.env.FOLLOWUP_CRON || '0 9 * * *';
cron.schedule(FOLLOWUP_CRON, () => {
  console.log('[Cron] Vérification des relances...');
  runFollowups().catch(err => console.error('[Cron] Erreur:', err.message));
});

// ---- Démarrage ----
app.listen(PORT, () => {
  console.log(`[Bot] TPF WhatsApp Bot démarré sur le port ${PORT}`);
  console.log(`[Bot] Webhook: http://localhost:${PORT}/webhook`);
  console.log(`[Bot] Propriétaire: ${OWNER_WHATSAPP}`);
  console.log(`[Bot] Relance cron: ${FOLLOWUP_CRON}`);
});
