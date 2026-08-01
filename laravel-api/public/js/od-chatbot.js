(function () {
  'use strict';

  var OD_API = window.OD_CHATBOT_API || 'http://localhost:4000/api/widget/chat';
  var OD_CTX = window.OD_CHATBOT_CONTEXT || 'techprofutur';
  var OD_TITLE = window.OD_CHATBOT_TITLE || 'Assistant Tech Pro Futur';
  var OD_WELCOME = window.OD_CHATBOT_WELCOME || 'Bonjour ! Comment puis-je vous aider ?';
  var OD_API_KEY = window.OD_CHATBOT_KEY || '';
  var OD_BRAND_URL = 'https://oeil.odsysteme.tech';

  var history = [];
  var isOpen = false;
  var isLoading = false;
  var hasConsented = localStorage.getItem('od_chat_consent') === 'true';

  // --- Styles ---
  var css = document.createElement('style');
  css.textContent = [
    '#od-chat-fab{position:fixed;bottom:90px;right:20px;z-index:9998;width:56px;height:56px;border-radius:50%;background:#4f46e5;color:#fff;border:none;cursor:pointer;box-shadow:0 4px 14px rgba(79,70,229,.4);display:flex;align-items:center;justify-content:center;transition:transform .2s,box-shadow .2s}',
    '#od-chat-fab:hover{transform:scale(1.08);box-shadow:0 6px 20px rgba(79,70,229,.5)}',
    '#od-chat-fab svg{width:26px;height:26px}',
    '#od-chat-box{position:fixed;bottom:90px;right:20px;z-index:9999;width:370px;max-width:calc(100vw - 32px);height:520px;max-height:calc(100vh - 120px);border-radius:16px;background:#fff;box-shadow:0 12px 40px rgba(0,0,0,.18);display:none;flex-direction:column;overflow:hidden;font-family:-apple-system,BlinkMacSystemFont,Segoe UI,Roboto,sans-serif}',
    '#od-chat-box.open{display:flex}',
    '#od-chat-header{background:#4f46e5;color:#fff;padding:14px 16px;display:flex;align-items:center;justify-content:space-between;flex-shrink:0}',
    '#od-chat-header h3{margin:0;font-size:15px;font-weight:600}',
    '#od-chat-close{background:none;border:none;color:#fff;cursor:pointer;padding:4px;opacity:.8}#od-chat-close:hover{opacity:1}',
    '#od-chat-messages{flex:1;overflow-y:auto;padding:16px;display:flex;flex-direction:column;gap:10px;background:#f9fafb}',
    '.od-msg{max-width:85%;padding:10px 14px;border-radius:12px;font-size:14px;line-height:1.5;word-wrap:break-word}',
    '.od-msg-bot{background:#e0e7ff;color:#1e1b4b;align-self:flex-start;border-bottom-left-radius:4px}',
    '.od-msg-user{background:#4f46e5;color:#fff;align-self:flex-end;border-bottom-right-radius:4px}',
    '.od-msg-loading{align-self:flex-start;background:#e0e7ff;color:#6366f1;font-style:italic}',
    '#od-chat-input-wrap{display:flex;border-top:1px solid #e5e7eb;padding:10px 12px;gap:8px;background:#fff;flex-shrink:0}',
    '#od-chat-input{flex:1;border:1px solid #d1d5db;border-radius:10px;padding:9px 14px;font-size:14px;outline:none;resize:none;font-family:inherit;min-height:20px;max-height:80px}',
    '#od-chat-input:focus{border-color:#4f46e5;box-shadow:0 0 0 2px rgba(79,70,229,.15)}',
    '#od-chat-send{background:#4f46e5;color:#fff;border:none;border-radius:10px;padding:0 14px;cursor:pointer;display:flex;align-items:center;justify-content:center;transition:background .15s}',
    '#od-chat-send:hover{background:#4338ca}',
    '#od-chat-send:disabled{opacity:.5;cursor:not-allowed}',
    '#od-chat-send svg{width:18px;height:18px}',
    '#od-consent{padding:16px;background:#f9fafb;text-align:center;flex:1;display:flex;flex-direction:column;justify-content:center;gap:12px}',
    '#od-consent p{font-size:13px;color:#374151;line-height:1.6;margin:0}',
    '#od-consent-title{font-size:15px;font-weight:600;color:#1e1b4b;margin:0}',
    '#od-consent-accept{background:#4f46e5;color:#fff;border:none;border-radius:10px;padding:10px 20px;font-size:14px;font-weight:600;cursor:pointer;transition:background .15s}',
    '#od-consent-accept:hover{background:#4338ca}',
    '#od-consent-decline{background:none;border:1px solid #d1d5db;border-radius:10px;padding:8px 16px;font-size:13px;color:#6b7280;cursor:pointer}',
    '#od-brand{padding:6px 12px;text-align:center;background:#f9fafb;border-top:1px solid #e5e7eb;flex-shrink:0}',
    '#od-brand a{font-size:11px;color:#9ca3af;text-decoration:none;transition:color .15s}',
    '#od-brand a:hover{color:#4f46e5}',
    '#od-voice-banner{display:none;padding:8px 12px;background:#ecfdf5;border-top:1px solid #d1fae5;text-align:center;flex-shrink:0;cursor:pointer}',
    '#od-voice-banner p{margin:0;font-size:12px;color:#065f46;font-weight:500}',
    '#od-voice-banner:hover{background:#d1fae5}',
    '@media(max-width:480px){#od-chat-box{width:100vw;height:100vh;max-height:100vh;bottom:0;right:0;border-radius:0}#od-chat-fab{bottom:80px;right:14px}}'
  ].join('\n');
  document.head.appendChild(css);

  // --- FAB ---
  var fab = document.createElement('button');
  fab.id = 'od-chat-fab';
  fab.title = 'Ouvrir le chat';
  fab.innerHTML = '<svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>';
  fab.addEventListener('click', toggleChat);

  // --- Chat box ---
  var box = document.createElement('div');
  box.id = 'od-chat-box';
  box.innerHTML = [
    '<div id="od-chat-header">',
    '  <h3>' + OD_TITLE + '</h3>',
    '  <button id="od-chat-close" title="Fermer"><svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg></button>',
    '</div>',
    '<div id="od-chat-messages"></div>',
    '<div id="od-consent" style="display:none">',
    '  <p id="od-consent-title">Avant de commencer</p>',
    '  <p>Cet assistant utilise l\'intelligence artificielle. Vos echanges sont collectes de maniere anonyme pour ameliorer le service. Aucune donnee personnelle (email, telephone) n\'est transmise a l\'IA.</p>',
    '  <button id="od-consent-accept">J\'accepte et je commence</button>',
    '  <button id="od-consent-decline">Non merci</button>',
    '</div>',
    '<div id="od-voice-banner">',
    '  <p>Participez a la formation de OEIL INTELLIGENTE et gagnez de l\'argent</p>',
    '</div>',
    '<div id="od-chat-input-wrap">',
    '  <textarea id="od-chat-input" placeholder="Ecrivez votre message..." rows="1"></textarea>',
    '  <button id="od-chat-send" title="Envoyer"><svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg></button>',
    '</div>',
    '<div id="od-brand"><a href="' + OD_BRAND_URL + '" target="_blank" rel="noopener">Propulse par OEIL INTELLIGENTE (OI)</a></div>'
  ].join('');

  document.body.appendChild(fab);
  document.body.appendChild(box);

  box.querySelector('#od-chat-close').addEventListener('click', toggleChat);
  box.querySelector('#od-chat-send').addEventListener('click', sendMessage);
  box.querySelector('#od-consent-accept').addEventListener('click', acceptConsent);
  box.querySelector('#od-consent-decline').addEventListener('click', toggleChat);
  box.querySelector('#od-voice-banner').addEventListener('click', function() {
    window.open(OD_BRAND_URL + '/contribute?ref=' + encodeURIComponent(location.hostname), '_blank');
  });

  // Geo-detection: afficher contribution vocale si au Benin
  detectBenin();
  var input = box.querySelector('#od-chat-input');
  input.addEventListener('keydown', function (e) {
    if (e.key === 'Enter' && !e.shiftKey) {
      e.preventDefault();
      sendMessage();
    }
  });
  // Auto-resize textarea
  input.addEventListener('input', function () {
    this.style.height = 'auto';
    this.style.height = Math.min(this.scrollHeight, 80) + 'px';
  });

  function toggleChat() {
    isOpen = !isOpen;
    box.classList.toggle('open', isOpen);
    fab.style.display = isOpen ? 'none' : 'flex';
    if (isOpen && !hasConsented) {
      showConsent();
    } else if (isOpen && history.length === 0) {
      appendMessage('bot', OD_WELCOME);
    }
    if (isOpen) input.focus();
  }

  function showConsent() {
    box.querySelector('#od-consent').style.display = 'flex';
    box.querySelector('#od-chat-messages').style.display = 'none';
    box.querySelector('#od-chat-input-wrap').style.display = 'none';
  }

  function acceptConsent() {
    hasConsented = true;
    localStorage.setItem('od_chat_consent', 'true');
    box.querySelector('#od-consent').style.display = 'none';
    box.querySelector('#od-chat-messages').style.display = 'flex';
    box.querySelector('#od-chat-input-wrap').style.display = 'flex';
    if (history.length === 0) appendMessage('bot', OD_WELCOME);
    input.focus();
  }

  function appendMessage(role, text) {
    var msgs = box.querySelector('#od-chat-messages');
    var div = document.createElement('div');
    div.className = 'od-msg ' + (role === 'user' ? 'od-msg-user' : 'od-msg-bot');
    div.textContent = text;
    msgs.appendChild(div);
    msgs.scrollTop = msgs.scrollHeight;
    return div;
  }

  function detectBenin() {
    fetch('https://ipapi.co/json/', { method: 'GET' })
      .then(function(r) { return r.json(); })
      .then(function(data) {
        if (data && data.country_code === 'BJ') {
          box.querySelector('#od-voice-banner').style.display = 'block';
        }
      })
      .catch(function() { /* silent fail */ });
  }

  function sendMessage() {
    if (isLoading) return;
    var text = input.value.trim();
    if (!text) return;

    input.value = '';
    input.style.height = 'auto';
    appendMessage('user', text);
    history.push({ role: 'user', content: text });

    isLoading = true;
    var btn = box.querySelector('#od-chat-send');
    btn.disabled = true;
    var loader = appendMessage('bot', 'En train de reflechir...');
    loader.classList.add('od-msg-loading');

    var payload = {
      productContext: OD_CTX,
      messages: history,
      locale: 'fr'
    };

    var headers = { 'Content-Type': 'application/json' };
    if (OD_API_KEY) headers['x-api-key'] = OD_API_KEY;

    fetch(OD_API, {
      method: 'POST',
      headers: headers,
      body: JSON.stringify(payload)
    })
      .then(function (res) {
        if (!res.ok) throw new Error('Erreur ' + res.status);
        return res.json();
      })
      .then(function (data) {
        loader.remove();
        var reply = data.response || data.localizedResponse || 'Desolee, je n\'ai pas pu repondre.';
        appendMessage('bot', reply);
        history.push({ role: 'assistant', content: reply });
      })
      .catch(function (err) {
        loader.remove();
        appendMessage('bot', 'Desolee, une erreur est survenue. Veuillez reessayer.');
        console.error('OD Chatbot error:', err);
      })
      .finally(function () {
        isLoading = false;
        btn.disabled = false;
        input.focus();
      });
  }
})();
