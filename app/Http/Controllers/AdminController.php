<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Services\AdminService;

// Simple, so not use middleware
class AdminController extends Controller
{
    protected $adminService;

    public function __construct(AdminService $adminService)
    {
        $this->adminService = $adminService;
    }

    public function index()
    {
        $loginUser = Auth::user();
        if ($loginUser == null || $loginUser->system_role === User::ROLE_USER) {
            return redirect('/admin/auth');
        }

        $data = $this->adminService->getDashboardData($loginUser);
        return view('index', $data);
    }

    public function toogleActiveUser($id)
    {
        $this->adminService->toggleUserActiveStatus($id);
        return redirect()->back();
    }

    public function resetUserPassword($id)
    {
        $result = $this->adminService->resetUserPassword($id);

        if ($result['success']) {
            $message = $result['message'] . " New password: " . $result['newPassword'];
        } else {
            $message = $result['message'];
        }

        return redirect()->back()->with('msg', $message);
    }

    public function saveSetting(Request $request)
    {
        $message = $this->adminService->saveSettings(
            $request->type,
            $request->S3_KEY,
            $request->S3_PASSWORD,
            $request->S3_BUCKET,
            $request->S3_REGION,
            $request->cache_extension ? 'on' : 'off'
        );

        return redirect()->back()->with('msg', $message);
    }

    public function resetProfileStatus()
    {
        $this->adminService->resetProfileStatuses();
        return redirect()->back()->with('msg', 'Reset profile status successfully');
    }

    public function runMigrations()
    {
        $result = $this->adminService->runMigrations();
        return redirect()->back()->with('msg', $result['message']);
    }
}
