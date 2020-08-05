<?php


namespace App\Http\Middleware;


use App\Services\UserService;
use Closure;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Session;

class AnonymousAuth extends Middleware
{
    public function handle($request, Closure $next, ...$guards)
    {
        $id_is_valid = false;
        $temp_participation_id = $request->session()->get('participant-id');
        $session_id = $request->session()->get('session-id');
        if($temp_participation_id !== null) {
            $id_is_valid = UserService::CheckTemporaryParticipationId($temp_participation_id,$session_id);
        }

        if($id_is_valid === false) {
            $this->authenticate($request, $guards);
        }

        return $next($request);
    }
    protected function redirectTo($request)
    {
        return route('login');
    }

}