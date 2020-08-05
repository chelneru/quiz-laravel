<?php

namespace App\Http\Controllers;


use App\Services\ClassService;
use App\Services\QuizService;
use App\Services\ScoresService;
use App\Services\UserService;
use Auth;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;


class ScoresController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return Application|Factory|Response|View
     */
    public function index()
    {
        $teacher_id = Auth::user()->u_id;
        $classes = null;
        $quizzes = null;
        $participants_autocomplete = null;

        if ($teacher_id !== null) {
            $classes = ClassService::GetTeacherClasses($teacher_id);
            $quizzes = QuizService::GetScoresPageQuizzes($teacher_id);

            $participants_names = UserService::GetTeacherParticipantsNames($teacher_id,true);
            $participants_emails = UserService::GetTeacherParticipantsEmails($teacher_id,true);
            $participants_user_ids = UserService::GetTeacherParticipantsUserIds($teacher_id);
            $participants_autocomplete = array_merge($participants_names->toArray(),$participants_emails->toArray(),$participants_user_ids->toArray());

        }
        return view('scores.index', ['classes' => $classes, 'quizzes' => $quizzes,'participants_autocomplete'=>$participants_autocomplete]);
    }

    public function GetParticipantQuickAccessInfo(Request $request ) {
        if($request->has('participant')) {
            $teacher_id = Auth::user()->u_id;

            $result = ScoresService::GetParticipantOverview($teacher_id,$request->input('participant'));
            return json_encode($result);
        }
            return json_encode(['status'=>false,'content'=>null,'message'=>'invalid params']);

}
    public function GetScoresPageData(Request $request)
    {
        if ($request->has('type') && $request->has('id')) {
            $type = $request->input('type');
            $result = ScoresService::GetQuizScores($type, $request->input('id'));
            return json_encode($result);
        }
        return json_encode(['status'=>false,'content'=>null,'message'=>'invalid params']);
    }
}
