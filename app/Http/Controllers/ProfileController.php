<?php

namespace App\Http\Controllers;

use App\Models\Profile;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    // GET /api/profiles
    public function index()
    {
        return response()->json(Profile::all(), 200);
    }

    // POST /api/profiles
    public function store(Request $request)
    {
        $profile = Profile::create([
            'name' => $request->name,
            'bio' => $request->bio,
            'cover_photo' => $request->cover_photo,
            'profile_photo' => $request->profile_photo,
        ]);

        return response()->json($profile, 201);
    }

    // GET /api/profiles/{id}
    public function show($id)
    {
        $profile = Profile::find($id);

        if (!$profile) {
            return response()->json(['message' => 'Profile not found'], 404);
        }

        return response()->json($profile, 200);
    }

    // PUT /api/profiles/{id}
    public function update(Request $request, $id)
    {
        $profile = Profile::find($id);

        if (!$profile) {
            return response()->json(['message' => 'Profile not found'], 404);
        }

        $profile->update([
            'name' => $request->name,
            'bio' => $request->bio,
            'cover_photo' => $request->cover_photo,
            'profile_photo' => $request->profile_photo
        ]);

        return response()->json($profile, 200);
    }

    // DELETE /api/profiles/{id}
    public function destroy($id)
    {
        $profile = Profile::find($id);

        if (!$profile) {
            return response()->json(['message' => 'Profile not found'], 404);
        }

        $profile->delete();

        return response()->json(['message' => 'deleted'], 200);
    }
}