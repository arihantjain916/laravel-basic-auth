<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\api\auth;

Route::post('/user/login', [auth::class, "login"]);
Route::post('/user/register', [auth::class, "register"]);
// Route::middleware('auth:api')->group(function () {
// });
