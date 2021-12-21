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

    public function __construct()
    {
        $this->key = \config('app.JWT_KEY');
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
            JWT::decode(getallheaders()['jwt_token'], new Key($this->key, 'HS256'));
        }catch (\Exception $e){
            return new JsonResponse(['jwt_error_message' => $e->getMessage()]);
        }

        return $next($request);
    }
}
