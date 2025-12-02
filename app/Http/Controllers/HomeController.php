<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\SetupService;
use Carbon\Carbon;

class HomeController extends Controller
{
    protected $setupService;

    public function __construct(SetupService $setupService)
    {
        $this->setupService = $setupService;
    }

    public function index()
    {
        if ($this->setupService->isDatabaseSetup()) {
            return view("ready");
        } else {
            return redirect('/setup');
        }
    }

    public function setup()
    {
        if ($this->setupService->isDatabaseSetup()) {
            return redirect('/');
        } else {
            return view('setup');
        }
    }

    /*
     * Create database
     */
    public function createDb(Request $request)
    {
        $result = $this->setupService->createDatabase(
            $request->host,
            $request->port,
            $request->username,
            $request->password,
            $request->dbname
        );

        if ($result['success']) {
            return $result['message'];
        } else {
            return view('setup')->withErrors($result['message']);
        }
    }

    /**
     * Get system time
     */
    public function getSystemTime()
    {
        return $this->setupService->getSystemTime();
    }

    public function test()
    {
        return 'ok';
    }
}
