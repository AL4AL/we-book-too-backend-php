<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Domain\Auth\Entities\User;
use App\Domain\Auth\Entities\Identity;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;

class SocialiteController extends Controller
{
    public function googleCallback(Request $request)
    {
        try {
            $socialUser = Socialite::driver('google')->stateless()->user();
            
            $identity = Identity::where('provider', 'google')
                ->where('provider_user_id', $socialUser->getId())
                ->first();

            if ($identity) {
                $user = $identity->user;
            } else {
                $user = User::firstOrCreate(
                    ['email' => $socialUser->getEmail()],
                    [
                        'name' => $socialUser->getName(),
                        'status' => 'active',
                    ]
                );

                Identity::create([
                    'user_id' => $user->id,
                    'provider' => 'google',
                    'provider_user_id' => $socialUser->getId(),
                    'data' => [
                        'name' => $socialUser->getName(),
                        'email' => $socialUser->getEmail(),
                        'avatar' => $socialUser->getAvatar(),
                    ],
                ]);
            }

            $token = $user->createToken('api')->plainTextToken;

            return response()->json(['token' => $token]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Authentication failed'], 422);
        }
    }

    public function telegramCallback(Request $request)
    {
        $data = $request->validate([
            'id' => 'required|string',
            'first_name' => 'required|string',
            'username' => 'nullable|string',
            'auth_date' => 'required|integer',
            'hash' => 'required|string',
        ]);

        // TODO: Verify Telegram widget signature
        $identity = Identity::where('provider', 'telegram')
            ->where('provider_user_id', $data['id'])
            ->first();

        if ($identity) {
            $user = $identity->user;
        } else {
            $user = User::create([
                'name' => $data['first_name'],
                'status' => 'active',
            ]);

            Identity::create([
                'user_id' => $user->id,
                'provider' => 'telegram',
                'provider_user_id' => $data['id'],
                'data' => $data,
            ]);
        }

        $token = $user->createToken('api')->plainTextToken;

        return response()->json(['token' => $token]);
    }

    public function logout(Request $request)
    {
        auth()->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Logged out successfully']);
    }
}
