<?php

// app/Http/Middleware/TokenMiddleware.php

namespace App\Http\Middleware;

use Closure;
use Firebase\JWT\JWT;
use Illuminate\Http\Request;
use Exception;

class TokenMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        // Get the Authorization header
        $authorizationHeader = $request->header('Authorization');

        if (!$authorizationHeader) {
            return response()->json(['message' => 'Authorization token required.'], 401);
        }

        // Extract token from Bearer header
        $token = str_replace('Bearer ', '', $authorizationHeader);

        if (!$token) {
            return response()->json(['message' => 'Authorization token invalid.'], 401);
        }

        // Validate JWT with the public key
        try {
            // Get the public key from the file
            $publicKey = file_get_contents(storage_path('keys/public.key'));

            // Decode the token using the RS256 algorithm
            $decoded = JWT::decode($token, new \Firebase\JWT\Key($publicKey, 'RS256'));

            // Optionally, you can store the decoded payload in the request object
            $request->attributes->add(['user' => $decoded]);

            return $next($request);
        } catch (Exception $e) {
            return response()->json(['message' => 'Invalid token.', 'error' => $e->getMessage()], 401);
        }
    }
}
