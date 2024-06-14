<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class auth extends Controller
{
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                "message" => $validator->messages(),
                "success" => false
            ], 400);
        }
        $user = User::where("email", $request->email)->first();
        if ($user) {
            $passCheck = Hash::check($request->password, $user->password);
            if ($passCheck) {
                $token = $user->createToken('auth_token')->accessToken;
                return response()->json(["message" => "Login Successfull!!", "success" => true, "token" => $token], 200);

            } else {
                return response()->json(["message" => "Incorrect Password", "success" => false], 500);

            }
        } else {
            return response()->json(["message" => "User not found", "success" => false], 500);
        }
    }

    public function register(Request $request)
    {
        $validate_data = Validator::make($request->all(), [
            'username' => ['required', 'unique:users,username'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'min:8'],
        ]);
        if ($validate_data->fails()) {
            return response()->json([
                "message" => $validate_data->messages(),
                "success" => false
            ], 400);
        } else {
            try {
                DB::beginTransaction();
                $user = User::create([
                    'username' => $request->username,
                    'email' => $request->email,
                    'password' => Hash::make($request->password)
                ]);
                $token = $user->createToken('auth_token')->accessToken;

                DB::commit();
                return response()->json([
                    "message" => "User created successfully",
                    "success" => true,
                    "token" => $token
                ], 200);
            } catch (\Throwable $th) {
                DB::rollBack();
                return response()->json([
                    "message" => $th->getMessage(),
                    "success" => false
                ], 500);
            }
        }
    }
}
