<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

if (isset($_SERVER['VERCEL']) || isset($_ENV['VERCEL'])) {
    $envVars = [
        'APP_DEBUG' => 'true',
        'LOG_CHANNEL' => 'stderr',
        'DB_CONNECTION' => 'pgsql',
        'DB_HOST' => 'aws-0-ap-southeast-1.pooler.supabase.com',
        'DB_PORT' => '6543',
        'DB_DATABASE' => 'postgres',
        'DB_USERNAME' => 'postgres.kgdapksvpalgxxtubiwx',
        'DB_PASSWORD' => '7mN*@*wNmN7mhJZ',
        'CACHE_STORE' => 'array',
        'SESSION_DRIVER' => 'cookie'
    ];
    foreach ($envVars as $key => $val) {
        putenv("$key=$val");
        $_ENV[$key] = $val;
        $_SERVER[$key] = $val;
    }
}

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->trustProxies(at: '*');
        $middleware->alias([
        'admin' => \App\Http\Middleware\AdminMiddleware::class,
        'update.last.login'=> \App\Http\Middleware\UpdateLastLogin::class,
        'check.user.status' => \App\Http\Middleware\CheckUserStatus::class,
    ]);

    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();

if (isset($_SERVER['VERCEL']) || isset($_ENV['VERCEL'])) {
    $app->useStoragePath('/tmp/storage');
    $directories = [
        '/tmp/storage/framework/cache/data',
        '/tmp/storage/framework/views',
        '/tmp/storage/framework/sessions',
        '/tmp/storage/logs',
    ];
    foreach ($directories as $dir) {
        if (!is_dir($dir)) {
            @mkdir($dir, 0777, true);
        }
    }
}

return $app;
