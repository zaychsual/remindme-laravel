<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\AuthService;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    private $authService;
    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    /**
     * Register function
     *
     * @param AuthRequest $request
     * @return void
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8|confirmed'
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $create = $this->authService->register($request);

        return $create;
    }

    public function refresh(Request $request)
    {
        return $this->authService->refresh($request);
    }

    /**
     * Login function
     *
     * @param AuthRequest $request
     * @return void
     */
    public function session(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $login = $this->authService->login($request);

        return $login;
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        return response()->json(auth()->user());
    }

    /**
     * logout function
     *
     * @param Request $request
     * @return void
     */
    public function logout(Request $request)
    {
        return $this->authService->logout($request);
    }
}
