<?php

namespace App\Http\Middleware;
use Tymon\JWTAuth\Http\Middleware\BaseMiddleware;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Exception;
use JWTAuth;
use Closure;


class JwtMiddleware
{
    public function handle($request, Closure $next, $role = null)
    {
        try{
            $user = JWTAuth::parseToken()->authenticate();
       
        } catch (TokenExpiredException $e) {

            return response()->json([
                'success' => false,
                'message' => 'Token Expired!',
                '$request' => $request,
            ], 401);

        } catch (TokenInvalidException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Not Authorized!',
                '$request' => $request,
            ], 401);

        } catch (\Tymon\JWTAuth\Exceptions\JWTException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Authorization token not found or invalid!',
            ], 401);
        }
        return $next($request);
    }
}
