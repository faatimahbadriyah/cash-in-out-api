<?php

use App\Http\Controllers\Auth\MeController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Auth::loginUsingId(2);

Auth::routes();

Route::middleware('auth:sanctum')->group(function () {
    Route::get('me', [MeController::class, '__invoke']);
});
