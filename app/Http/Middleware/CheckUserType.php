<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckUserType
{
    public function handle(Request $request, Closure $next, ...$types)
    {
        $user = $request->user();

        if (!$user || (!in_array($user->type, $types) && $user->type !== 'super_admin')) {
            abort(Response::HTTP_FORBIDDEN, 'Unauthorized action.');
        }

        return $next($request);
    }
}
