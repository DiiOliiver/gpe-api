<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureTheUserIsActive
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // bloquear usuário inativo e usuário deletado
        if (auth()->user()->trashed()) {
            return response()->json([
                'message' => 'Inactivated user',
                'status' => '401',
            ], 401);
        }

        return $next($request);
    }
}
