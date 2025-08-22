<?php

use App\Events\UserCreated;
use App\Http\Middleware\EarlyHintsMiddleware;
use App\Http\Middleware\TokenBucketMiddleware;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->middleware(EarlyHintsMiddleware::class);

Route::get('/health', function () {
    return 'OK';
})->middleware([TokenBucketMiddleware::class, EarlyHintsMiddleware::class]);


Route::get('/create-user', function () {
    event(new UserCreated([
        'id' => rand(1, 1000),
        'name' => \Illuminate\Support\Str::random(5)],
    ));

    sleep(2);
    \Illuminate\Support\Facades\Log::info("Done".\Illuminate\Support\Carbon::now()->toDateTimeString());

    return 'OK';
});

