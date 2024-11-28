<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Traits\ApiResponseTrait;
use Illuminate\Support\Facades\Validator;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    use ApiResponseTrait;

    public function login(Request $request)
    {
        $rules = [
            'email' => 'required|string|email',
            'password' => 'required|string'
        ];

        $validate = Validator::make($request->all(), $rules);
        if ($validate->fails()) {
            return $this->validationErrorResponse($validate->errors());
        }

        $credentials = $request->only('email', 'password');

        if (!$token = JWTAuth::attempt($credentials)) {
            return $this->unauthorizedResponse();
        }

        $user = new UserResource(Auth::user());

        $data = [
            'user' => $user,
            'authorization' => [
                'token' => $token,
                'type' => 'bearer',
                'expires_in' => JWTAuth::factory()->getTTL() * 60
            ]
        ];

        return $this->apiResponse('success', 'You are logged in successfully', $data);
    }

    public function register(Request $request)
    {
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
        ];

        $validate = Validator::make($request->all(), $rules);
        if ($validate->fails()) {
            return $this->validationErrorResponse($validate->errors());
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);


        $token = JWTAuth::fromUser($user);

        $user = new UserResource($user);

        $data = [
            'user' => $user,
            'authorization' => [
                'token' => $token,
                'type' => 'bearer',
                'expires_in' => JWTAuth::factory()->getTTL() * 60
            ]
        ];

        return $this->createdResponse($data, 'You are registered successfully');
    }

    public function logout()
    {
        try {
            $token = JWTAuth::getToken();

            if (!$token) {
                return $this->unauthorizedResponse('Token not provided');
            }

            JWTAuth::invalidate($token);

            return $this->apiResponse('success', 'User logged out successfully');
        } catch (\PHPOpenSourceSaver\JWTAuth\Exceptions\TokenInvalidException $e) {
            return $this->unauthorizedResponse('Invalid token');
        } catch (\Exception $e) {
            return $this->apiResponse('error', 'Something went wrong', []);
        }
    }

    public function refresh()
    {
        try {
            $newToken = JWTAuth::refresh(JWTAuth::getToken());

            $data = [
                'token' => $newToken,
                'type' => 'bearer',
                'expires_in' => JWTAuth::factory()->getTTL() * 60,
            ];

            return $this->apiResponse('success', 'Token refreshed successfully', $data);
        } catch (\PHPOpenSourceSaver\JWTAuth\Exceptions\TokenInvalidException $e) {
            return $this->unauthorizedResponse('Invalid token');
        } catch (\Exception $e) {
            return $this->apiResponse('error', 'Something went wrong', []);
        }
    }

}
