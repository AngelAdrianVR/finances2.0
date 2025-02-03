<?php

namespace App\Http\Middleware;

use App\Models\Loan;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsOwnResource
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $loan = Loan::find($request->route('loan')->id);

        if (!$loan || $loan->user_id !== auth()->id()) {
            abort(403);
        }

        return $next($request);
    }
}
