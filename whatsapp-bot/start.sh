#!/bin/bash
# Script de démarrage du bot TPF WhatsApp
# Utilisation: ./start.sh ou via cron @reboot

export PATH=/opt/alt/alt-nodejs22/root/usr/bin:$PATH

BOT_DIR="/home/u217725874/domains/tpfcedp.com/bot/whatsapp-bot"
PID_FILE="$BOT_DIR/bot.pid"
LOG_FILE="$BOT_DIR/bot.log"

# Vérifier si le bot tourne déjà
if [ -f "$PID_FILE" ]; then
    PID=$(cat "$PID_FILE")
    if kill -0 "$PID" 2>/dev/null; then
        echo "Le bot tourne déjà (PID: $PID)"
        exit 0
    fi
fi

# Démarrer le bot
cd "$BOT_DIR"
nohup node src/index.js >> "$LOG_FILE" 2>&1 &
echo $! > "$PID_FILE"
echo "Bot démarré (PID: $(cat $PID_FILE))"
echo "Logs: $LOG_FILE"
