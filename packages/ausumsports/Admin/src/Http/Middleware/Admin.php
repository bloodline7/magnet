<?php
namespace Ausumsports\Admin\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class Admin
{
    public function handle($request, Closure $next)
    {
        if(!$request->user()) return redirect()->route('adminLogin');

        Log::info("Admin Access:". auth()->user()->name);


        return $next($request);
    }

}
