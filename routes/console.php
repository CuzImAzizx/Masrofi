<?php

use App\Models\Plan;
use App\Models\Subscription;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

//Artisan::command('inspire', function () {
//    $this->comment(Inspiring::quote());
//})->purpose('Display an inspiring quote')->hourly();

/**
 * Refresh daily requests for all subscriptions
 */
Schedule::call(function () {
    $subscriptions = Subscription::all();
    foreach($subscriptions as $subscription){
        $planId = $subscription->plan_id;
        $plan = Plan::find($planId);
        $subscription->daily_requests_left = $plan->daily_requests;
        $subscription->update();
    }
})->daily();