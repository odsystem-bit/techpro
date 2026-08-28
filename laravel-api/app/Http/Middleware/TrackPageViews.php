<?php

namespace App\Http\Middleware;

use App\Models\PageView;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class TrackPageViews
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        if ($request->method() === 'GET' && $response->getStatusCode() === 200) {
            $path = $request->path();

            $skipPaths = ['api/', 'webhooks/', 'js/', 'css/', 'build/', 'storage/', 'favicon', '.txt', '.html', '.ico', 'robots'];
            foreach ($skipPaths as $skip) {
                if (str_contains($path, $skip)) {
                    return $response;
                }
            }

            try {
                PageView::create([
                    'url'         => $request->fullUrl(),
                    'path'        => '/' . $path,
                    'route_name'  => $request->route()?->getName(),
                    'method'      => $request->method(),
                    'ip_address'  => $request->ip(),
                    'user_agent'  => substr($request->userAgent() ?? '', 0, 255),
                    'referer'     => $request->header('referer'),
                    'session_id'  => Session::getId(),
                ]);
            } catch (\Throwable $e) {
            }
        }

        return $response;
    }
}
