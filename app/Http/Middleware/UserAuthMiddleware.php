<?php

namespace App\Http\Middleware;

use App\Helpers\Response;
use App\Helpers\StatusCodeRequest;
use Closure;
use Exception;
use PHPOpenSourceSaver\JWTAuth\Exceptions\TokenExpiredException;
use PHPOpenSourceSaver\JWTAuth\Exceptions\TokenInvalidException;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

class UserAuthMiddleware
{
    use Response;
    public function handle($request, Closure $next)
    {
        try {
            JWTAuth::parseToken()->authenticate();
        } catch (Exception $e) {
            if ($e instanceof TokenInvalidException)
                return $this->handleResponse(null,'Token is Invalid',StatusCodeRequest::INVALID_TOKEN);
            else if ($e instanceof TokenExpiredException)
                return $this->handleResponse(null,'Token is Expired',StatusCodeRequest::INVALID_TOKEN);
            else
                return $this->handleResponse(null,'Authorization Token not found',StatusCodeRequest::INVALID_TOKEN);
        }
        return $next($request);
    }
}
