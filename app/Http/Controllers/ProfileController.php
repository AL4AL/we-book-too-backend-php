<?php

namespace App\Http\Controllers;

use App\Domain\Profile\Entities\Profile;
use App\Http\Resources\ProfileResource;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function show(Request $request)
    {
        $profile = Profile::firstOrCreate(
            ['user_id' => auth()->id()],
            ['data' => [], 'completion_score' => 0]
        );

        return new ProfileResource($profile);
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'data' => 'required|array',
        ]);

        $profile = Profile::firstOrCreate(
            ['user_id' => auth()->id()],
            ['data' => [], 'completion_score' => 0]
        );

        $profile->updateData($data['data']);

        return new ProfileResource($profile);
    }

    public function me(Request $request)
    {
        $user = auth()->user();
        // TODO: Get user roles for current tenant
        
        return response()->json([
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
            ],
            'roles' => ['customer'], // Placeholder
        ]);
    }
}
