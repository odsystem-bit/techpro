import dotenv from 'dotenv';
dotenv.config();

import { runFollowups } from '../followup.js';

console.log('[Cron] Lancement manuel des relances...');
runFollowups()
  .then(() => {
    console.log('[Cron] Terminé');
    process.exit(0);
  })
  .catch(err => {
    console.error('[Cron] Erreur:', err);
    process.exit(1);
  });
