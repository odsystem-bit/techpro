<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Commande {{ $order->order_number }} — Tech Pro Futur</title>
</head>
<body style="margin:0;padding:0;background:#f1f5f9;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',sans-serif;color:#1e293b;">

<table width="100%" cellpadding="0" cellspacing="0" style="background:#f1f5f9;padding:40px 16px;">
<tr><td align="center">
<table width="600" cellpadding="0" cellspacing="0" style="max-width:600px;width:100%;">

    {{-- Header --}}
    <tr>
        <td style="background:linear-gradient(135deg,#4f46e5,#7c3aed);border-radius:16px 16px 0 0;padding:32px 40px;text-align:center;">
            <div style="display:inline-block;background:rgba(255,255,255,.15);border-radius:12px;padding:10px 20px;margin-bottom:16px;">
                <span style="color:white;font-weight:800;font-size:18px;letter-spacing:.05em;">TECH PRO FUTUR</span>
            </div>
            <h1 style="margin:0;color:white;font-size:26px;font-weight:800;">Paiement confirmé ! ✅</h1>
            <p style="margin:8px 0 0;color:rgba(255,255,255,.8);font-size:15px;">Commande n° <strong>{{ $order->order_number }}</strong></p>
        </td>
    </tr>

    {{-- Body --}}
    <tr>
        <td style="background:white;padding:36px 40px;">

            <p style="margin:0 0 20px;font-size:16px;color:#374151;">
                Bonjour <strong>{{ $order->customer_name ?? 'cher client' }}</strong>,
            </p>
            <p style="margin:0 0 28px;font-size:15px;color:#6b7280;line-height:1.6;">
                Merci pour votre achat sur <strong>Tech Pro Futur</strong> ! Votre paiement a bien été reçu et votre produit est prêt à être téléchargé.
            </p>

            {{-- Récapitulatif commande --}}
            <table width="100%" cellpadding="0" cellspacing="0" style="background:#f8fafc;border-radius:12px;border:1px solid #e2e8f0;margin-bottom:28px;overflow:hidden;">
                <tr style="background:#f1f5f9;">
                    <td colspan="2" style="padding:12px 20px;font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:#64748b;">Détail de la commande</td>
                </tr>
                <tr>
                    <td style="padding:12px 20px;font-size:14px;color:#64748b;border-top:1px solid #e2e8f0;">Produit</td>
                    <td style="padding:12px 20px;font-size:14px;font-weight:600;text-align:right;border-top:1px solid #e2e8f0;">{{ $order->product->name ?? 'Produit numérique' }}</td>
                </tr>
                <tr>
                    <td style="padding:12px 20px;font-size:14px;color:#64748b;border-top:1px solid #e2e8f0;">Quantité</td>
                    <td style="padding:12px 20px;font-size:14px;font-weight:600;text-align:right;border-top:1px solid #e2e8f0;">{{ $order->quantity }}</td>
                </tr>
                <tr style="background:#f0fdf4;">
                    <td style="padding:14px 20px;font-size:15px;font-weight:700;color:#166534;border-top:1px solid #dcfce7;">Montant payé</td>
                    <td style="padding:14px 20px;font-size:15px;font-weight:800;text-align:right;color:#166534;border-top:1px solid #dcfce7;">
                        {{ number_format($order->total_amount, 0, ',', ' ') }} {{ $order->currency }}
                    </td>
                </tr>
            </table>

            {{-- Bouton téléchargement --}}
            <div style="background:#faf5ff;border:2px solid #e9d5ff;border-radius:14px;padding:28px;text-align:center;margin-bottom:28px;">
                <div style="font-size:36px;margin-bottom:12px;">📥</div>
                <p style="margin:0 0 8px;font-size:16px;font-weight:700;color:#1e293b;">Votre fichier est prêt !</p>
                <p style="margin:0 0 20px;font-size:13px;color:#7c3aed;">Lien valable <strong>{{ $order->product && $order->product->product_type === 'formation' ? '365 jours' : '7 jours' }}</strong> — <strong>{{ $order->download_limit }} téléchargements</strong> maximum</p>
                <a href="{{ $order->download_url }}"
                   style="display:inline-block;background:linear-gradient(135deg,#4f46e5,#7c3aed);color:white;text-decoration:none;font-weight:700;font-size:16px;padding:14px 36px;border-radius:10px;letter-spacing:.02em;">
                    ⬇️ &nbsp;Télécharger maintenant
                </a>
                <p style="margin:16px 0 0;font-size:12px;color:#94a3b8;">
                    Lien direct : <a href="{{ $order->download_url }}" style="color:#7c3aed;word-break:break-all;">{{ $order->download_url }}</a>
                </p>
            </div>

            {{-- Infos utiles --}}
            <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:24px;">
                <tr>
                    <td style="padding:10px 0;border-top:1px solid #f1f5f9;">
                        <span style="font-size:13px;color:#64748b;">⏰ &nbsp;Téléchargements restants</span>
                        <span style="float:right;font-size:13px;font-weight:600;">{{ max(0, $order->download_limit - $order->download_count) }} / {{ $order->download_limit }}</span>
                    </td>
                </tr>
                <tr>
                    <td style="padding:10px 0;border-top:1px solid #f1f5f9;">
                        <span style="font-size:13px;color:#64748b;">📅 &nbsp;Expire le</span>
                        <span style="float:right;font-size:13px;font-weight:600;">{{ $order->download_expires_at?->format('d/m/Y') }}</span>
                    </td>
                </tr>
            </table>

            {{-- Section ODIBOT pour les ebooks --}}
            @if ($order->isEbookOrder())
            <div style="background:#eef2ff;border:2px solid #c7d2fe;border-radius:14px;padding:24px;text-align:center;margin-bottom:24px;">
                <div style="font-size:32px;margin-bottom:8px;">🤖</div>
                <p style="margin:0 0 6px;font-size:16px;font-weight:700;color:#1e293b;">ODIBOT — Votre assistant IA gratuit !</p>
                <p style="margin:0 0 16px;font-size:13px;color:#4f46e5;line-height:1.5;">Vous avez acheté un ebook, vous bénéficiez donc d'<strong>ODIBOT</strong> gratuitement. Téléchargez l'application et posez vos questions à votre assistant IA personnel.</p>
                <a href="{{ $order->odibot_url }}"
                   style="display:inline-block;background:linear-gradient(135deg,#4f46e5,#7c3aed);color:white;text-decoration:none;font-weight:700;font-size:15px;padding:12px 32px;border-radius:10px;">
                    📲 &nbsp;Télécharger ODIBOT
                </a>
                <p style="margin:12px 0 0;font-size:11px;color:#94a3b8;">
                    Lien direct : <a href="{{ $order->odibot_url }}" style="color:#7c3aed;word-break:break-all;">{{ $order->odibot_url }}</a>
                </p>
            </div>
            @endif

            <p style="margin:0;font-size:14px;color:#94a3b8;line-height:1.6;">
                Un problème ? Répondez à cet email ou contactez-nous directement.
            </p>
        </td>
    </tr>

    {{-- Footer --}}
    <tr>
        <td style="background:#f8fafc;border-radius:0 0 16px 16px;border-top:1px solid #e2e8f0;padding:20px 40px;text-align:center;">
            <p style="margin:0;font-size:12px;color:#94a3b8;">
                © {{ date('Y') }} Tech Pro Futur — Produits numériques premium<br/>
                Cet email a été envoyé à {{ $order->customer_email }}
            </p>
        </td>
    </tr>

</table>
</td></tr>
</table>

</body>
</html>
