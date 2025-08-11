<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\RequestOtpRequest;
use App\Jobs\SendOtpJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use App\Domain\Auth\Entities\User;

class OtpController extends Controller
{
    public function requestOtp(RequestOtpRequest $request)
    {
        $data = $request->validated();

        $identifier = strtolower($data['identifier']);
        $cacheKey = 'otp:request:'.$identifier;
        if (Cache::has($cacheKey)) {
            throw ValidationException::withMessages(['identifier' => 'Too many requests']);
        }
        $code = (string) random_int(100000, 999999);
        Cache::put('otp:code:'.$identifier, Hash::make($code), now()->addMinutes(10));
        Cache::put($cacheKey, true, now()->addSeconds(30));

        Log::info('OTP generated', ['identifier' => $identifier]);
        SendOtpJob::dispatch($identifier, $code, $data['channel']);

        return response()->json(['sent' => true]);
    }

    public function verifyOtp(Request $request)
    {
        $data = $request->validate([
            'identifier' => 'required|string',
            'code' => 'required|string|size:6',
            'name' => 'nullable|string|max:255',
        ]);
        $identifier = strtolower($data['identifier']);
        $hash = Cache::get('otp:code:'.$identifier);
        if (!$hash || !Hash::check($data['code'], (string) $hash)) {
            throw ValidationException::withMessages(['code' => 'Invalid code']);
        }

        $user = User::firstOrCreate(
            filter_var($identifier, FILTER_VALIDATE_EMAIL) ? ['email' => $identifier] : ['name' => $identifier],
            ['name' => $data['name'] ?? ($data['name'] ?? Str::limit($identifier, 32)), 'password' => Str::random(32)]
        );

        $token = $user->createToken('api')->plainTextToken;
        Cache::forget('otp:code:'.$identifier);

        return response()->json(['token' => $token]);
    }
}


