<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\WebAuthService;

class AuthController extends Controller
{
    protected $webAuthService;

    public function __construct(WebAuthService $webAuthService)
    {
        $this->webAuthService = $webAuthService;
    }

    public function login(Request $request)
    {
        $result = $this->webAuthService->login($request->email, $request->password);

        if (!$result['success']) {
            return redirect()->back()->with('error', $result['message']);
        }

        return redirect('/admin');
    }

    public function logout()
    {
        $this->webAuthService->logout();
        return redirect('/admin/auth');
    }
}
