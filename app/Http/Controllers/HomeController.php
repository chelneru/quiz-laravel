<?php

namespace App\Http\Controllers;

use App\Services\ClassService;
use App\Services\QuizService;
use App\Services\UserService;
use Auth;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\View\View;
use Mail;
use Redirect;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth')->only('index');
    }

    /**
     * Show the application dashboard.
     *
     * @return Factory|RedirectResponse|Response|View
     */
    public function index()
    {

        if(UserService::IsAdmin(Auth::user()->u_id)) {
            return Redirect::route('admin-users');

        }
        if (Auth::user()->u_role == 2) {

//            if(Auth::user()->u_id == 1) {
//            Mail::send("emails.test",[],function ($message) {
//                $message->from('sagaprojectmail@uni.au.dk');
//                $message->to('alin.panainte95@gmail.com')
//                    ->subject('test 2');
//            });
//            }
            $user_id = Auth::user()->u_id;
            $classes = ClassService::GetTeacherDashboardClasses($user_id);
            $quizzes = QuizService::GetDashboardTeacherQuizzes($user_id);
            $participants = UserService::GetTeacherParticipantsCount($user_id);
            return view('dashboard.teacher-dashboard', ['classes' => $classes,
                'quizzes' => $quizzes, 'participants' => $participants]);
        }
        else if(Auth::user()->u_role == 1) {
            $user_id = Auth::user()->u_id;
            $classes = ClassService::GetParticipantClasses($user_id);
            $quizzes = QuizService::GetParticipantQuizzes($user_id);
            return view('dashboard.participant-dashboard', ['classes' => $classes,
                'quizzes' => $quizzes,]);
        }
        else if(Auth::user()->u_role == 3) {

        }
        return view('home');
    }

    /**
     *
     * This is the first page when we go to the application route and we are not logged in.
     * @return Factory|View
     */
    public function intro() {
        return view('intro.landing_page');
    }
    public function examples() {
        return view('intro.examples');

    }
    public function howTo() {
        return view('intro.how_to');

    }
    public function publications() {
        return view('intro.publications');

    }



}
