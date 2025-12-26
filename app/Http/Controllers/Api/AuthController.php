<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AppUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite; // For later
use Illuminate\Support\Str;

class AuthController extends Controller
{
    // 1. Guest Login (The default entry point)
    public function guestLogin(Request $request)
    {
        $request->validate([
            'device_uuid' => 'required|string',
            'device_info' => 'nullable|array',
            'country_code' => 'nullable|string'
        ]);

        // Find existing guest by UUID or create new one
        $user = AppUser::firstOrCreate(
            ['device_uuid' => $request->device_uuid],
            [
                'is_guest' => true,
                'auth_provider' => 'guest',
                'username' => 'Guest_' . Str::random(8),
                'last_device_info' => $request->device_info,
                'country_code' => $request->country_code,
                'last_ip_address' => $request->ip(),
            ]
        );

        // Update IP and device info on every login
        $user->update([
            'last_ip_address' => $request->ip(),
            'last_device_info' => $request->device_info ?? $user->last_device_info,
        ]);

        // Create a long-lived Sanctum token
        $token = $user->createToken('guest_device')->plainTextToken;

        return response()->json([
            'status' => true,
            'message' => 'Guest Access Granted',
            'user' => $user,
            'token' => $token
        ]);
    }

    // 2. Social Login (Stub for now)
    // The Android app will send a "provider_token" from Google/FB.
    // We will verify it later using Socialite/Firebase.
    public function socialLogin(Request $request)
    {
        // We will build this logic next
        return response()->json(['message' => 'Social Login coming soon']);
    }
}