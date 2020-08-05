<?php


namespace App\Http\Middleware;
use App\Services\ClassService;
use App\Services\QuizService;
use Auth;
use Closure;
use Illuminate\Support\Facades\Input;


class QuizDirectLinkMiddleware
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
        $quiz_id = $request->route('quiz_id');
        $user = Auth::user();
        if ($user) {
            if ($quiz_id !== null) {
                //check if user is in quiz's class
                $class_id = ClassService::GetClassByQuiz($quiz_id);
                if ($class_id !== null) {
                    $enrolled = ClassService::CheckParticipantClassEnrollment($class_id, $user->u_id);
                    if (!$enrolled) {
                        $enroll_result = ClassService::AddClassUsers($class_id, [$user->u_id]);
                        if ($enroll_result['status'] === false) {

                            $request->session()->flash('status', 'fail');
                            $request->session()->flash('message', 'An error occurred while joining the class of the quiz.');
                            return redirect()->route('home');

                        }
                    }
                }
                //check if quiz is open
                $quiz_sessions = QuizService::GetQuizSessions($quiz_id,false);
                $session_id = null;
                $total_sessions = $quiz_sessions->total();
                if ($total_sessions > 0) {
                    $session_id = $quiz_sessions[0]->id;
                }
                if ($session_id !== null) {
                    $ongoing_progress = QuizService::CheckOngoingProgressOnSession($user->u_id, $session_id);

                    return redirect()->route('quiz-start-page', ['quiz_id' => $quiz_id]);
                }

                $request->session()->flash('status', 'fail');
                $request->session()->flash('message', 'The quiz is closed .');
                return redirect()->route('home');

            }

            $request->session()->flash('status', 'fail');
            $request->session()->flash('message', 'Quiz not found.');
            return route('home');
        }

        $check_quiz_allows_anonymous = QuizService::QuizCheckAnonymousParticipation($quiz_id);
        if ($check_quiz_allows_anonymous !== null) {
            $url = route('quiz-start-page', ['quiz_id' => $quiz_id]);
            $request->session()->put('allow_anonymous', $check_quiz_allows_anonymous);
            $request->session()->put('direct_link', $url);
            $request->session()->put('quiz_id', $quiz_id);

            return redirect()->route('login');



        }
        $request->session()->flash('status', 'fail');
        $request->session()->flash('message', 'Quiz not found.');
        return redirect()->route('login');
    }
    }
