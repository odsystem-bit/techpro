import fs from 'fs';
import path from 'path';
import { fileURLToPath } from 'url';

const __dirname = path.dirname(fileURLToPath(import.meta.url));
const DATA_DIR = path.join(__dirname, '..', 'data');

if (!fs.existsSync(DATA_DIR)) {
  fs.mkdirSync(DATA_DIR, { recursive: true });
}

const CONVERSATIONS_FILE = path.join(DATA_DIR, 'conversations.json');
const FOLLOWUPS_FILE = path.join(DATA_DIR, 'followups.json');

function loadJson(file) {
  try {
    return JSON.parse(fs.readFileSync(file, 'utf8'));
  } catch {
    return {};
  }
}

function saveJson(file, data) {
  fs.writeFileSync(file, JSON.stringify(data, null, 2));
}

// ---- Conversations ----

export function getConversation(phone) {
  const data = loadJson(CONVERSATIONS_FILE);
  if (!data[phone]) {
    data[phone] = {
      phone,
      messages: [],
      intent: null,
      interestedProducts: [],
      offeredCheckout: false,
      lastMessageAt: null,
      firstContactAt: new Date().toISOString(),
      followupSent: false,
      clientName: null,
    };
    saveJson(CONVERSATIONS_FILE, data);
  }
  return data[phone];
}

export function saveConversation(phone, conv) {
  const data = loadJson(CONVERSATIONS_FILE);
  data[phone] = conv;
  saveJson(CONVERSATIONS_FILE, data);
}

export function addMessage(phone, role, content) {
  const conv = getConversation(phone);
  conv.messages.push({ role, content, timestamp: new Date().toISOString() });
  conv.lastMessageAt = new Date().toISOString();
  saveConversation(phone, conv);
  return conv;
}

export function getAllConversations() {
  return loadJson(CONVERSATIONS_FILE);
}

export function updateConversation(phone, updates) {
  const conv = getConversation(phone);
  Object.assign(conv, updates);
  saveConversation(phone, conv);
  return conv;
}

// ---- Follow-ups ----

export function getFollowups() {
  return loadJson(FOLLOWUPS_FILE);
}

export function addFollowup(phone, productId, scheduledAt) {
  const data = loadJson(FOLLOWUPS_FILE);
  if (!data[phone]) data[phone] = [];
  data[phone].push({
    productId,
    scheduledAt,
    sent: false,
    sentAt: null,
    createdAt: new Date().toISOString(),
  });
  saveJson(FOLLOWUPS_FILE, data);
}

export function markFollowupSent(phone, index) {
  const data = loadJson(FOLLOWUPS_FILE);
  if (data[phone] && data[phone][index]) {
    data[phone][index].sent = true;
    data[phone][index].sentAt = new Date().toISOString();
    saveJson(FOLLOWUPS_FILE, data);
  }
}

export function getPendingFollowups() {
  const data = loadJson(FOLLOWUPS_FILE);
  const pending = [];
  const now = new Date();
  for (const [phone, followups] of Object.entries(data)) {
    followups.forEach((f, i) => {
      if (!f.sent && new Date(f.scheduledAt) <= now) {
        pending.push({ phone, index: i, ...f });
      }
    });
  }
  return pending;
}
