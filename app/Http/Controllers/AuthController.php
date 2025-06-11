<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Services\UserService;

class AuthController extends Controller
{
    private $userService;
    public function __construct(UserService $userService){
        $this->userService = $userService;
    }
    public function register(Request $request){
        $request->validate([
            'full_name' =>  'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
        ]);
        $user = $this->userService->create($request->all());

        $token = Auth::login($user);
        return response()->json([
            'status' => 'success',
            'message' => 'Đăng ký tài khoản thành công',
            'user' => $user,
            'authorization' => [
                'token' => $token,
                'type' => 'bearer',
            ]
        ]);
    }

    public function login(Request $request){
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string'
        ]);

        $credential = $request->only('email', 'password');

        $token = Auth::attempt($credential);
        if(!$token){
            return response()->json([
                'status' => 'error',
                'message' => 'Sai thông tin đăng nhập'
            ], 401);
        }
        $user = Auth::user();
        return response()->json([
            'status' => 'success',
            'user' => $user,
            'authorization' => [
                'token' => $token,
                'type' => 'bearer',
            ]
        ]);
    }

    public function logout(){
        Auth::logout();
        return response()->json([
            'status' => 'success',
            'message' => 'Đăng xuất thành công'
        ]);
    }
}