import dotenv from 'dotenv';
dotenv.config();

import twilio from 'twilio';

const accountSid = process.env.TWILIO_ACCOUNT_SID;
const authToken = process.env.TWILIO_AUTH_TOKEN;
const fromNumber = process.env.TWILIO_WHATSAPP_NUMBER || 'whatsapp:+14155238886';

const client = twilio(accountSid, authToken);

export async function sendText(to, body) {
  return client.messages.create({
    from: fromNumber,
    to: to,
    body,
  });
}

export async function sendMedia(to, body, mediaUrl) {
  return client.messages.create({
    from: fromNumber,
    to: to,
    body,
    mediaUrl: [mediaUrl],
  });
}

export async function sendButtons(to, body, buttons) {
  // Twilio WhatsApp supports interactive buttons via content templates
  // For now, we send text with formatted options
  let msg = body + '\n\n';
  buttons.forEach((b, i) => {
    msg += `${i + 1}. ${b}\n`;
  });
  return sendText(to, msg);
}

export function formatPhone(phone) {
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

export { client, fromNumber };
