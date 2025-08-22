<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class EarlyHintsMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Get the response after the request is processed
        $response = $next($request);

        // Check if the request is valid for Early Hints (Optional logic can be added)
        if (env('APP_ENV') === 'local') {
            // Send Early Hints headers (this will send the headers before the body)
            $this->sendEarlyHints();
        }

        return $response;
    }

    /**
     * Send the Early Hints headers.
     *
     * @return void
     */
    protected function sendEarlyHints(): void
    {
        // Early Hints Headers
        header('Link: </css/styles.css>; rel=preload; as=style', false);
        header('Link: </js/app.js>; rel=preload; as=script', false);

        // You can add as many preloads as you need based on your assets.
    }
}
