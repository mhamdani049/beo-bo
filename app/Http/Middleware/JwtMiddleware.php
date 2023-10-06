<?php
namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Http\Middleware\BaseMiddleware;
use Tymon\JWTAuth\Facades\JWTAuth;
use Exception;
use Closure;

class JwtMiddleware extends BaseMiddleware
{

    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
        } catch (TokenInvalidException $e) {
            return response()->json(['status' => 'error', 'code' => '10', 'message' => 'Token is Invalid', 'data' => null], 401);
        } catch (TokenExpiredException $e) {
            return response()->json(['status' => 'error', 'code' => '20', 'message' => 'Token is Expired', 'data' => null], 401);
        } catch (Exception $e) {
            return response()->json(['status' => 'error', 'code' => '30', 'message' => $e->getMessage(), 'data' => null], 401);
        }

        return $next($request);
    }
}
