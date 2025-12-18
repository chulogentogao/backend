<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Exceptions\HttpResponseException;

class Authenticate extends Middleware
{
    protected function redirectTo($request)
    {
        // Special-case: if anything ever hits /api/logout while unauthenticated,
        // treat it as a successful logout instead of an error to avoid UX issues.
        if ($request->is('api/logout')) {
            throw new HttpResponseException(
                response()->json([
                    'status' => true,
                    'message' => 'Successfully logged out',
                ], 200)
            );
        }

        throw new HttpResponseException(
            response()->json(['message' => 'Unauthenticated.'], 401)
        );
    }
}
