#!/bin/bash
# Script d'arrêt du bot TPF WhatsApp

BOT_DIR="/home/u217725874/domains/tpfcedp.com/bot/whatsapp-bot"
PID_FILE="$BOT_DIR/bot.pid"

if [ -f "$PID_FILE" ]; then
    PID=$(cat "$PID_FILE")
    if kill -0 "$PID" 2>/dev/null; then
        kill "$PID"
        echo "Bot arrêté (PID: $PID)"
    else
        echo "Processus $PID introuvable"
    fi
    rm -f "$PID_FILE"
else
    echo "Aucun PID enregistré"
fi
