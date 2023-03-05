<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;

class WeatherController extends Controller
{
    public function getWeather(Request $request, $email)
    {
        $cacheKey = 'users_' . $email . '_weather';
        $weatherData = null;

        if (Cache::has($cacheKey)) {
            $weatherData = Cache::get($cacheKey);
        } else {
            $weatherData = User::query()
                ->with('weather')
                ->whereHas('weather')
                ->where('email', '=', $email)
                ->first()
                ->weather;
            Cache::put($cacheKey, $weatherData, Config::get('cache.expiry'));
        }

        return response()->json([
            'status' => true,
            'data' => $weatherData
        ]);
    }
}
