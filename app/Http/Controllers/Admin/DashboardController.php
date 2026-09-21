<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BrochureDownload;
use App\Models\TelegramLead;
use App\Models\TelegramSession;
use App\Models\TelegramUser;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $totalUsers = TelegramUser::count();
        $totalLeads = TelegramLead::count();
        $activeSessions = TelegramSession::where('state', '!=', 'idle')->count();
        $totalDownloads = BrochureDownload::count();

        $recentLeads = TelegramLead::with('service')
            ->orderByDesc('created_at')
            ->take(5)
            ->get();

        return view('admin.dashboard', compact(
            'totalUsers',
            'totalLeads',
            'activeSessions',
            'totalDownloads',
            'recentLeads'
        ));
    }
}
