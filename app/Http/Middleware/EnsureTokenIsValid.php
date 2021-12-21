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
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        try {
            $decoded_token = (array)JWT::decode(getallheaders()['jwt_token'], new Key($this->key, 'HS256'));
            if(array_diff($decoded_token,$this->payload)){
                return new JsonResponse(['jwt_error_message' => 'Token mismatch']);
            }

        }catch (\Exception $e){
            return new JsonResponse(['jwt_error_message' => $e->getMessage()]);
        }

        return $next($request);
    }
}
