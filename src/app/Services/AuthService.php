<?php

namespace App\Services;

use Exception;
use App\Models\User;
use App\Helpers\ResponseHelper;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use League\OAuth2\Server\Exception\OAuthServerException;
use Psr\Http\Message\ServerRequestInterface;

class AuthService
{
    use ResponseHelper;

    private $modelUser;

    public function __construct()
    {
        $this->modelUser = new User();
    }

    public function register($request)
    {
        DB::beginTransaction();
        try {
            $payload = $this->modelUser->rawPayload($request);
            $newData = $this->modelUser->create($payload);

            DB::commit();
            return $this->responseSuccess(200, 'Registrasi Sukses.', $newData);
        } catch (\Throwable $th) {
            return $this->responseFailed($th->getMessage(), 500);
        }
    }

    public function login($request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            $tokenResult = $user->createToken('Personal Access Token');
            $token = $tokenResult->token;
            $token->save();

            return response()->json([
                'ok' => true,
                'data' => [
                    'user' => [
                        'id' => $user->id,
                        'email' => $user->email,
                        'name' => $user->name
                    ],
                    'access_token' => $tokenResult->accessToken,
                    'refresh_token' => $token->id
                ]
            ], 200);
        } else {
            return response()->json([
                'ok' => false,
                'err' => 'ERR_INVALID_CREDS',
                'msg' => 'incorrect username or password',
            ], 401);
        }
    }

    public function logout($request)
    {
        $request->user()->token()->revoke();
        return response()->json([
            'message' => 'Successfully logged out'
        ]);
        return $this->responseSuccess(200, 'Logout Success.');
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh($request)
    {
        $refreshToken = $request->header('Authorization');
        $tokenId = explode(' ', $refreshToken)[1];
        $token = DB::table('oauth_access_tokens')->where('id', $tokenId)->first();

        if ($token && !$token->revoked) {
            $user = User::find($token->user_id);
            $tokenResult = $user->createToken('Personal Access Token');
            $newToken = $tokenResult->token;
            $newToken->save();

            return response()->json([
                'ok' => true,
                'data' => [
                    'access_token' => $tokenResult->accessToken,
                    'refresh_token' => $newToken->id
                ]
            ], 200);
        } else {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
    }
}
