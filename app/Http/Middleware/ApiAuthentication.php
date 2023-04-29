<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ApiAuthentication
{
    public function handle(Request $request, Closure $next)
    {
        $apiKey = $request->header('api-key');
        $signature = $request->header('signature');
        $data = $request->all();
        // Verify the signature
        if (!$this->verifySignature($data, $signature)) {
            return response()->json(['error' => 'Invalid signature'], 401);
        }

        // Authenticate the API request
        // use custom api auth
        if ($apiKey !== 'test_api_key') {
            return response()->json(['error' => 'Invalid API key'], 401);
        }
        return $next($request);
    }

    private function verifySignature(array $data, string $signature): bool {
        return true;
    }
}
