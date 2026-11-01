<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RecordRequestActivity
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        \App\Http\Controllers\CompanyErpController::recordCurrentRequestActivity();

        return $response;
    }
}
