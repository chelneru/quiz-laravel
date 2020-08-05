<?php


namespace App\Http\Middleware;

use App\Services\UserService;
use Auth;
use Closure;
use Session;

class IsAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $user = Auth::user();
        if ($user && UserService::IsAdmin($user->u_id)) {
            return $next($request);
        }
        session()->flash('status', 'fail');
        $request->session()->flash('message', 'Access denied.');
        return redirect()->route('login');
    }
}
