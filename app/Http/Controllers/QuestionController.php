<?php

namespace App\Http\Controllers;

use App\Question;
use App\Services\ClassService;
use App\Services\QuestionAnswerService;
use App\Services\QuestionService;
use App\Services\QuizService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class QuestionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return View
     */
    public function index(Request $request)
    {
        $teacher_id = Auth::user()->u_id;
        $classes = ClassService::GetTeacherClasses($teacher_id);

        $class_id = null;
        $quiz_id = null;

        if ($request->has('class_id')) {
            $class_id = $request->input('class_id');
        }

        if ($request->has('quiz_id')) {
            $quiz_id = $request->input('quiz_id');
        }

        $quizzes = QuizService::GetTeacherQuizzesDropdown($teacher_id, $class_id);


        $questions = QuestionService::GetTeacherQuestions($teacher_id, $class_id, $quiz_id);
        return view('questions.index', ['questions' => $questions, 'classes' => $classes,
            'quizzes' => $quizzes, 'class_filter' => $class_id, 'quiz_filter' => $quiz_id]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return  view('questions.create-question-page', []);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Question  $question
     * @return \Illuminate\Http\Response
     */
    public function show(Question $question)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Question  $question
     * @return \Illuminate\Http\Response
     */
    public function edit(Question $question)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Question  $question
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Question $question)
    {
        //
    }

    public function getTeacherQuestions(Request $request) {
        if(Auth::user()){
            $user_id = Auth::user()->u_id;
            $questions = QuestionService::GetTeacherQuestionsForImport($user_id,null,null);
            return json_encode(['status'=>true,'questions'=>$questions]);
        }
        return json_encode(['status'=>false,'questions'=>null]);

    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Question  $question
     * @return \Illuminate\Http\Response
     */
    public function destroy(Question $question)
    {
        //
    }
}
