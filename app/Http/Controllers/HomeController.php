<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Services\BusinessHourService;

class HomeController extends Controller
{
    public function index(BusinessHourService $businessHourService)
    {
        $services = Service::where('is_active', true)
            ->where('is_public', true)
            ->orderBy('category')
            ->orderBy('sort_order')
            ->get(['code','name','price','category']);

        $weeklyHours = $businessHourService->getWeeklyHours();
        $isOpenNow = $businessHourService->isOpenNow();
        $todayHours = $businessHourService->getTodayHours();

        return view('tilmidhome', compact(
            'services',
            'weeklyHours',
            'isOpenNow',
            'todayHours'
        ));
    }
}