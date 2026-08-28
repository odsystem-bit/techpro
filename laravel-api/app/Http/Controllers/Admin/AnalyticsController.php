<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PageView;
use Illuminate\Http\Request;

class AnalyticsController extends Controller
{
    public function index(Request $request)
    {
        $period = $request->get('period', '7');
        $days = (int) $period;

        $startDate = now()->subDays($days)->startOfDay();

        // Total vues sur la période
        $totalViews = PageView::where('created_at', '>=', $startDate)->count();

        // Visiteurs uniques (par session_id)
        $uniqueVisitors = PageView::where('created_at', '>=', $startDate)
            ->whereNotNull('session_id')
            ->distinct('session_id')
            ->count('session_id');

        // Vues aujourd'hui
        $todayViews = PageView::whereDate('created_at', today())->count();

        // Vues hier
        $yesterdayViews = PageView::whereDate('created_at', today()->subDay())->count();

        // Pages les plus visitées
        $topPages = PageView::where('created_at', '>=', $startDate)
            ->selectRaw('path, COUNT(*) as views, COUNT(DISTINCT session_id) as unique_views')
            ->groupBy('path')
            ->orderByDesc('views')
            ->take(15)
            ->get();

        // Vues par jour (graphique)
        $dailyViews = PageView::where('created_at', '>=', $startDate)
            ->selectRaw('DATE(created_at) as date, COUNT(*) as views, COUNT(DISTINCT session_id) as visitors')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Top referers
        $topReferers = PageView::where('created_at', '>=', $startDate)
            ->whereNotNull('referer')
            ->where('referer', '!=', '')
            ->selectRaw('referer, COUNT(*) as views')
            ->groupBy('referer')
            ->orderByDesc('views')
            ->take(10)
            ->get();

        // Top user agents (navigateurs/appareils)
        $topDevices = PageView::where('created_at', '>=', $startDate)
            ->selectRaw('
                CASE
                    WHEN user_agent LIKE "%Mobile%" OR user_agent LIKE "%Android%" OR user_agent LIKE "%iPhone%" THEN "Mobile"
                    WHEN user_agent LIKE "%Tablet%" OR user_agent LIKE "%iPad%" THEN "Tablet"
                    ELSE "Desktop"
                END as device_type,
                COUNT(*) as views
            ')
            ->groupBy('device_type')
            ->orderByDesc('views')
            ->get();

        return view('admin.analytics', compact(
            'period',
            'totalViews',
            'uniqueVisitors',
            'todayViews',
            'yesterdayViews',
            'topPages',
            'dailyViews',
            'topReferers',
            'topDevices'
        ));
    }
}
