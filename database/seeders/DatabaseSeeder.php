<?php

namespace Database\Seeders;

use App\Models\Config;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        //Basic plan
        $basicPlan = Plan::create([
            "name" => "Basic",
            "price" => 0,
            "daily_requests" => 10,
            "threshold" => "00:01:00",
        ]);

        //Premium plan
        $premiumPlan = Plan::create([
            "name" => "Premium",
            "price" => 15,
            "daily_requests" => 100,
            "threshold" => "00:00:10",
        ]);

        //God plan
        $godPlan = Plan::create([
            "name" => "God",
            "price" => 9999,
            "daily_requests" => 10000,
            "threshold" => "00:00:00",
        ]);

        

        //Create the default user and its sub and config:
        $defaultUser = User::create([
            'name' => "admin",
            'email' => "admin@mail.com",
            'password' => Hash::make("q*Hxv-*BJ9zr5eD"),
        ]);
        Subscription::create([
            "user_id" => $defaultUser->id,
            "plan_id" => $godPlan->id,
            "start_date" => now(),
            "daily_requests_left" => $godPlan->daily_requests,
        ]);
        Config::create([
           "user_id" => $defaultUser->id,
           "start_of_the_month" => 1,
        ]);
    }
}
