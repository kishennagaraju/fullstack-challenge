<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;

class UsersController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = [];

        if (Cache::has('users')) {
            $users = Cache::get('users');
        } else {
            $users = User::query()->with([
                'weather' => function ($query) {
                    return $query->select('user_id', 'current');
                }
            ])->get();
            Cache::put('users', $users, Config::get('cache.expiry'));
        }


        return response()->json([
            'status' => true,
            'data' => $users,
        ]);
    }
}
