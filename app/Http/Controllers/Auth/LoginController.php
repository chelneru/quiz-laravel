<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\UserLogin;
use App\Services\ClassService;
use App\Services\QuizService;
use Carbon\Carbon;
use DB;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    protected function authenticated(Request $request, $user)
    {
        //add user last login
        $now = Carbon::now('Europe/Copenhagen')->toDateTimeString();

        DB::table('users')
            ->where('u_id','=',$user->u_id)
            ->update(['u_last_login'=>$now]);

        //redirect to start quiz page if we have the param for quiz id
        if($request->has('quiz_id')) {
            $quiz_id = $request->input('quiz_id');
            if($quiz_id !== null) {
                //check if user is enrolled

                if($user->u_role == 1 ) {
                    $enrolled = QuizService::CheckParticipantQuizEnrollment($user->u_id,$quiz_id);
                    if($enrolled === false) {
                        $class_id = ClassService::GetClassByQuiz($quiz_id);
                        if($class_id !== null){
                            ClassService::AddClassUsers($class_id,[$user->u_id]);
                        }
                        else {
                            $request->session()->put('enrolled-quiz-id', $quiz_id);
                        }
                    }
                }
                $this->redirectTo = '/quiz/start-quiz/'.$quiz_id;

                $quiz_sessions = QuizService::GetQuizSessions($quiz_id,false);
                $session_id = null;
                $total_sessions = $quiz_sessions->total();

                if ($total_sessions > 0) {
                    $session_id = $quiz_sessions[0]->id;
                }
                if ($session_id !== null) {
                    return redirect()->route('quiz-start-page', ['quiz_id' => $quiz_id]);
                }
                else{
                    $request->session()->flash('status', 'fail');
                    $request->session()->flash('message', 'The quiz is closed.');
                    $this->redirectTo = '/home';
                }

            }
            else{
                $request->session()->flash('status', 'fail');
                $request->session()->flash('message', 'Quiz not found.');
                $this->redirectTo = '/home';
            }

        }

    }
    public function logout(Request $request)
    {
        $this->guard()->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return $this->loggedOut($request) ?: redirect('/login');
    }

    public function showLoginForm(Request $request)
    {
        $allow_anonymous = $request->session()->pull('allow_anonymous');
        $direct_link = $request->session()->pull('direct_link');
        $quiz_id = $request->session()->pull('quiz_id');
        return view('auth.login', ['allow_anonymous' => $allow_anonymous, 'direct_link' => $direct_link,'quiz_id'=>$quiz_id]);
    }

}
