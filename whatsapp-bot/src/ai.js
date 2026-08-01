import OpenAI from 'openai';
import dotenv from 'dotenv';
dotenv.config();

const client = new OpenAI({
  apiKey: process.env.OPENAI_API_KEY,
});

const MODEL = process.env.OPENAI_MODEL || 'gpt-4o-mini';

export async function generateResponse(systemPrompt, conversationHistory, userMessage) {
  const messages = [
    { role: 'system', content: systemPrompt },
    ...conversationHistory.slice(-10).map(m => ({
      role: m.role,
      content: m.content,
    })),
  ];

  if (userMessage) {
    messages.push({ role: 'user', content: userMessage });
  }

  const completion = await client.chat.completions.create({
    model: MODEL,
    messages,
    max_tokens: 500,
    temperature: 0.7,
  });

  return completion.choices[0]?.message?.content?.trim() || 'Désolé, je n\'ai pas pu générer une réponse. Pouvez-vous reformuler ?';
}

export async function generateOwnerResponse(statsData, conversationHistory, userMessage) {
  const systemContent = `Tu es l'assistant IA de reporting pour le propriétaire de Tech Pro Futur (TPF).

Ton rôle:
- Répondre aux questions du propriétaire sur les ventes, commandes, clients
- Fournir des statistiques (nombre de ventes, revenus, produits populaires)
- Indiquer quelles relances ont été effectuées
- Résumer les conversations clients récentes
- Donner des recommandations business

Style: Direct, factuel, avec des chiffres. Français professionnel.

Données disponibles:
${JSON.stringify(statsData, null, 2)}`;

  const messages = [
    { role: 'system', content: systemContent },
    ...conversationHistory.slice(-10).map(m => ({
      role: m.role,
      content: m.content,
    })),
  ];

  if (userMessage) {
    messages.push({ role: 'user', content: userMessage });
  }

  const completion = await client.chat.completions.create({
    model: MODEL,
    messages,
    max_tokens: 800,
    temperature: 0.5,
  });

  return completion.choices[0]?.message?.content?.trim() || 'Données indisponibles pour le moment.';
}

export async function detectIntent(message) {
  const prompt = `Analyse ce message WhatsApp d'un client et détecte son intention. Réponds avec UN seul mot parmi:
- "achat" (veut acheter / demande lien de paiement)
- "info" (demande info sur un produit)
- "conseil" (demande conseil / recommandation)
- "prix" (demande le prix)
- "support" (problème / réclamation)
- "salutation" (bonjour / première interaction)
- "autre"

Message: "${message}"
Intention:`;

  const completion = await client.chat.completions.create({
    model: MODEL,
    messages: [{ role: 'user', content: prompt }],
    max_tokens: 10,
    temperature: 0,
  });

  return completion.choices[0]?.message?.content?.trim().toLowerCase() || 'autre';
}
