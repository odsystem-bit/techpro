import dotenv from 'dotenv';
dotenv.config();

import { getStats, getRecentOrders, getCatalog, formatCatalogForPrompt, getProductImageUrl, createCheckout } from './catalog.js';
import { generateOwnerResponse } from './ai.js';
import { sendText } from './whatsapp.js';
import { getConversation, addMessage, updateConversation, getAllConversations } from './conversation.js';

const OWNER_WHATSAPP = process.env.OWNER_WHATSAPP || 'whatsapp:+22900000000';

export async function handleOwnerMessage(from, body) {
  addMessage(from, 'user', body);

  const stats = await getStats();
  const orders = await getRecentOrders(20);
  const conversations = getAllConversations();

  const statsData = {
    stats,
    recentOrders: orders,
    conversations: Object.values(conversations).map(c => ({
      phone: c.phone,
      clientName: c.clientName,
      intent: c.intent,
      interestedProducts: c.interestedProducts,
      offeredCheckout: c.offeredCheckout,
      followupSent: c.followupSent,
      lastMessageAt: c.lastMessageAt,
      firstContactAt: c.firstContactAt,
      messageCount: c.messages?.length || 0,
    })),
  };

  const response = await generateOwnerResponse(statsData, getConversation(from).messages, body);
  addMessage(from, 'assistant', response);
  await sendText(from, response);
  return response;
}

export function isOwnerMessage(from) {
  return from === OWNER_WHATSAPP || from === formatPhone(OWNER_WHATSAPP);
}

function formatPhone(phone) {
  if (!phone) return null;
  let formatted = phone.trim();
  if (!formatted.startsWith('whatsapp:')) {
    if (!formatted.startsWith('+')) {
      formatted = '+' + formatted;
    }
    formatted = 'whatsapp:' + formatted;
  }
  return formatted;
}
