<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AppUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;

class AuthController extends Controller
{

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:app_users',
            'password' => 'required|string|min:8|confirmed',
            'device_uuid' => 'required',
            'device_name' => 'required|string',
        ]);

        // 1. Check if this device is already in the database
        $guestUser = AppUser::where('device_uuid', $request->device_uuid)
            ->where('is_guest', true)->first();

        $user = null;

        if ($guestUser) {
            // SCENARIO A: It's a Guest -> Update them to a Real User
            $guestUser->update([
                'username' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'is_guest' => false, // No longer a guest!
            ]);

            $user = $guestUser;

        } else {
            // SCENARIO C: New Device -> Create new User
            $user = AppUser::create([
                'username' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'device_uuid' => $request->device_uuid,
                'is_guest' => false,
                'status' => 'active', // Ensure default values exist
                'hate_speech_violation_count' => 0,
                'banned_posts_count' => 0,
            ]);
        }

        // Issue Token
        $token = $user->createToken($request->device_name)->plainTextToken;
        return response()->json(['token' => $token], 201);
    }

    public function login(Request $request)
    {

        $request->validate([
            'email' => 'required|email',
            'password' => ['required', Password::min(8)],
            'device_name' => 'required|string',
        ]);

        $user = AppUser::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        // Issue Token
        $token = $user->createToken($request->device_name)->plainTextToken;

        return response()->json(['token' => $token]);


    }

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
            ['device_uuid' => $request->device_uuid, 'is_guest' => true],
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

    public function logout(Request $request)
    {
        // Revoke the specific token the user used to authenticate the current request
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logged out successfully'
        ], 200);
    }
}