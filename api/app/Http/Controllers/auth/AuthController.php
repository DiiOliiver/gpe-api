<?php

namespace App\Http\Controllers\auth;

use App\Models\User;
use App\Traits\HttpResponses;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    use HttpResponses;

    public function login(Request $request)
    {
        if (Auth::attempt($request->only('email', 'password'))) {
            $user = User::with('role')
                ->where('active', '=', '1')
                ->whereNull('deleted_at')
                ->find(Auth::id());

            if (!$user) {
                return $this->error('Bad request', 400, []);
            }

            $permissions = $user->role->permissions->pluck('name')->toArray();
            return $this->response('Authorized', 200, [
                'token' => $request->user()->createToken('token', $permissions)->plainTextToken
            ]);
        }
        return $this->response('Not Authorized', 403);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return $this->response('Token Revoked', 200);
    }
}
