<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Symfony\Component\HttpFoundation\Response;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use JWTAuth;

class EnsureTokenIsValid
{
    protected $key;
    protected $payload;

    public function __construct()
    {
        $this->key = \config('app.JWT_KEY');
        $this->payload = \config('app.JWT_PAYLOAD');
    }


    /**
     * Removes fileds from given token
     * @param $token
     * @return array
     */
    private function cleanToken($token)
    {
        $token = (array)$token;

        foreach (['exp', 'iat', 'nbf'] as $item) {
            unset($token[$item]);
        }

        return $token;
    }

    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $token = !is_null($request->header('jwt_token')) ? $request->header('jwt_token') : getallheaders()['jwt_token'];

        try {
            $decoded_token = $this->cleanToken(JWT::decode($token, new Key($this->key, 'HS256')));

            if (array_diff($decoded_token, $this->payload)) {
                return new JsonResponse(['jwt_error_message' => 'Token mismatch']);
            }

        } catch (\Exception $e) {
            report($e->getMessage());
            return false;
        }


        return $next($request);
    }
}
