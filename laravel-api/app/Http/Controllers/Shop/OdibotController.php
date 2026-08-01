<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\SiteSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class OdibotController extends Controller
{
    public function download(Request $request)
    {
        $apkPath = SiteSetting::get('odibot_apk_path');

        if (!$apkPath || !Storage::disk('public')->exists($apkPath)) {
            abort(404, 'Le bot n\'est pas encore disponible au téléchargement.');
        }

        $token = $request->query('token');

        if ($token) {
            $order = Order::where('download_token', $token)->first();
            if (!$order) {
                abort(403, 'Lien invalide ou expiré.');
            }
            $product = $order->product;
            if (!$product || $product->product_type !== 'ebook') {
                abort(403, 'ODIBOT est disponible uniquement avec l\'achat d\'un ebook.');
            }
        }

        $fileName = 'ODIBOT-' . (SiteSetting::get('odibot_version', '1.0.0')) . '.apk';

        return Storage::disk('public')->download($apkPath, $fileName, [
            'Content-Type' => 'application/vnd.android.package-archive',
        ]);
    }

    public function info()
    {
        $apkPath = SiteSetting::get('odibot_apk_path');
        $hasBot = $apkPath && Storage::disk('public')->exists($apkPath);

        return response()->json([
            'available'   => $hasBot,
            'version'     => SiteSetting::get('odibot_version'),
            'description' => SiteSetting::get('odibot_description'),
            'size'        => $hasBot ? Storage::disk('public')->size($apkPath) : null,
        ]);
    }
}
