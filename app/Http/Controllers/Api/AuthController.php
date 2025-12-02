<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseController;
use Illuminate\Http\Request;
use App\Services\AuthService;

class AuthController extends BaseController
{
    protected AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function login(Request $request)
    {
        $result = $this->authService->login(
            $request->email ?? $request->user_name,
            $request->password
        );

        return $this->getJsonResponse($result['success'], $result['message'], $result['data']);
    }

    public function logout(Request $request)
    {
        $user = $request->user();
        $result = $this->authService->logout($user);
        return $this->getJsonResponse($result['success'], $result['message'], $result['data']);
    }
}
