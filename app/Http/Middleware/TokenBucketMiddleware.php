<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class TokenBucketMiddleware
{
    protected $capacity = 10;
    protected $refillRate = 1;

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $key = 'token_bucket:'.$request->ip();

        $bucket = Cache::get($key, [
            'tokens' => $this->capacity,
            'last_refill' => now()->timestamp
        ]);

        $now = now()->timestamp;
        $elapsed = $now - $bucket['last_refill'];

        Log::info("data", [
            $elapsed, $bucket['tokens']
        ]);

        // Refill tokens based on elapsed time
        $newTokens = $bucket['tokens'] + ($elapsed * $this->refillRate);
        $bucket['tokens'] = min($this->capacity, $newTokens);
        $bucket['last_refill'] = $now;

        if ($bucket['tokens'] < 1) {
            return \response()->json(['message' => 'Too man requests'], 429);
        }

        $bucket['tokens'] -= 1;

        Cache::put($key, $bucket, now()->addMinutes(1)); // Short TTL since we refresh often

        return $next($request);
    }
}
