<?php

namespace App\Http\Controllers;

use App\Services\GoogleAnalyticsService;

class AnalyticsController extends Controller
{
    public function realtimeActiveUsers(GoogleAnalyticsService $ga)
    {
        $count = $ga->getRealtimeActiveUsers();
        return response()->json(['active_users' => $count]);
    }
}