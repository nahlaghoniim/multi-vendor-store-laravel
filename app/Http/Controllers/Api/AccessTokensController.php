<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Response;
use Laravel\Sanctum\PersonalAccessToken;


class AccessTokensController extends Controller
{
    /**
     * Issue a new access token
     */
    public function store(Request $request)
    {
        $request->validate([
            'email' => 'required|email|max:255',
            'password' => 'required|string|min:6',
            'device_name' => 'nullable|string|max:255',
            'abilities' => 'nullable|array',
            'abilities.*' => 'string',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return Response::json([
                'message' => 'Invalid credentials',
            ], 401);
        }

        $deviceName = $request->post('device_name', $request->userAgent());

       $token = $user->createToken($deviceName, [
    'products.create',
    'products.update',
    'products.delete',
]);


        return Response::json([
            'token' => $token->plainTextToken,
            'token_type' => 'Bearer',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ],
        ], 201);
    }

    /**
     * Revoke current or specific token
     */
    public function destroy(Request $request, $token = null)
    {
        $user = $request->user();

        if (!$user) {
            abort(401);
        }

        // Revoke current token
        if ($token === null) {
            $user->currentAccessToken()->delete();

            return Response::json([
                'message' => 'Token revoked',
            ]);
        }

        // Revoke specific token
        $personalToken = PersonalAccessToken::findToken($token);

        if (
            $personalToken &&
            $personalToken->tokenable_id === $user->id &&
            $personalToken->tokenable_type === get_class($user)
        ) {
            $personalToken->delete();
        }

        return Response::json([
            'message' => 'Token revoked',
        ]);
    }
}
