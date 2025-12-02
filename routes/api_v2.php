<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\GroupController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\TagController;
use App\Http\Controllers\Api\ProxyController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\SettingController;
use App\Http\Controllers\Api\UploadController;
use App\Http\Controllers\HomeController;

/*
|--------------------------------------------------------------------------
| API V2 Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API v2 routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group with "v2" prefix.
|
| Add new endpoints here for future updates while maintaining backward
| compatibility with v1 routes.
|
*/

// Example of a new v2 endpoint
Route::get('/info', function () {
    return response()->json([
        'version' => '2.0',
        'message' => 'GPM Login Private Server API v2',
        'timestamp' => now()->toISOString()
    ]);
});