<?php

namespace App\Http\Controllers;

use App\Services\CustomerService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    private $customerService;
    public function __construct(CustomerService $customerService){
        $this->customerService = $customerService;
    }
    public function register(Request $request){
        $request->validate([
            'full_name' =>  'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
        ]);
        $customer = $this->customerService->create($request->all());
        $user = $customer->user;
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

    public function profile(){
        $user = Auth::user();
        return response()->json([
            'user' => $user,
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