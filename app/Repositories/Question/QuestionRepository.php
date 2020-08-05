<?php
/**
 * Created by PhpStorm.
 * User: au614698
 * Date: 12-11-2018
 * Time: 14:23
 */

namespace App\Repositories\Question;


use Carbon\Carbon;
use DB;

class QuestionRepository
{
    public static function CreateNewQuestion($question_text, $question_right_answer,$image_link, $user_id)
    {

        $now = Carbon::now('Europe/Copenhagen')->toDateTimeString();

        $question_data = [
            'question_u_id' => $user_id,
            'question_text' => shortenedString($question_text, 497),
            'question_right_answer' => $question_right_answer,
            'question_active' => '1',
            'question_required' => 1,
            'question_image_link' => shortenedString(strip_tags($image_link),255),
            'question_created_on' => $now
        ];
        $new_question_id = DB::table('questions')
            ->insertGetId($question_data);

        if ($new_question_id != 0 && $new_question_id !== null) {
            return ['status' => true, 'message' => 'success', 'value' => $new_question_id];
        }
        else {
            return ['status' => false, 'message' => 'failed-inserting-question', 'value' => null];

        }
    }

    public static function UpdateQuestion($question_id, $quiz_id, $question_text, $question_correct_answer,$image_link, $question_index)
    {

        try {
            DB::table('questions')
                ->where('question_id', '=', $question_id)
                ->update([
                    'question_text' => $question_text,
                    'question_right_answer' => $question_correct_answer,
                    'question_image_link'=>$image_link
                ]);
            //update question index for the current quiz
            DB::table('quiz_questions')
                ->where('qq_quiz_id', '=', $quiz_id)
                ->where('qq_question_id', '=', $question_id)
                ->update([
                    'qq_question_index' => $question_index
                ]);
            return ['status' => true, 'message' => 'success'];
        } catch (\Exception $e) {
            return ['status' => false, 'message' => $e->getMessage() . ' in file: ' . $e->getFile() . ' at line: ' . $e->getLine()];

        }
    }

    public static function DeleteQuestion($question_id)
    {
        DB::table('questions')
            ->where('question_id','=',$question_id)
            ->delete();
        //delete links between question and quizzes
        DB::table('quiz_questions')
            ->where('qq_question_id','=',$question_id)
            ->delete();

    }

    public static function GetTeacherQuestions($teacher_id,$class_id = null,$quiz_id = null)
    {

        $result = DB::table('questions')
            ->where('question_u_id', '=', $teacher_id)
            ->select('question_text', 'question_id as id', 'quiz_title as quiz_text', 'quiz_id',
                'class_name as class_text', 'class_id',
                DB::raw('DATE_FORMAT(question_created_on, \'%d-%m-%Y, %H:%i\') as created_on'))
            ->leftJoin('quiz_questions', 'qq_question_id', '=', 'question_id')
            ->leftJoin('quizzes', 'quiz_id', '=', 'qq_quiz_id')
            ->leftJoin('class_quizzes', 'cq_quiz_id', '=', 'quiz_id')
            ->leftJoin('classes', 'class_id', '=', 'cq_class_id');


        if($class_id !== null) {
            $result = $result->where('class_id','=',$class_id);
        }
        if($quiz_id !== null) {
            $result = $result->where('quiz_id','=',$quiz_id);
        }

        $result = $result->paginate(10);

        return $result;
    }

    public static function GetTeacherQuestionsForImport($teacher_id, $class_id = null, $quiz_id = null)
    {

        $result = DB::table('questions')
            ->where('question_u_id', '=', $teacher_id)
            ->select('question_id as id','question_text','question_image_link as image_link', 'quiz_title as quiz_text', 'quiz_id',
                'class_name as class_text', 'class_id','question_right_answer as right_answer',
                DB::raw('DATE_FORMAT(question_created_on, \'%d-%m-%Y, %H:%i\') as created_on'))
            ->leftJoin('quiz_questions', 'qq_question_id', '=', 'question_id')
            ->leftJoin('quizzes', 'quiz_id', '=', 'qq_quiz_id')
            ->leftJoin('class_quizzes', 'cq_quiz_id', '=', 'quiz_id')
            ->leftJoin('classes', 'class_id', '=', 'cq_class_id');


        if ($class_id !== null) {
            $result = $result->where('class_id', '=', $class_id);
        }
        if ($quiz_id !== null) {
            $result = $result->where('quiz_id', '=', $quiz_id);
        }

        $result = $result->get();
        foreach ($result as $question) {
            $question->answers = DB::table('question_answers')
                ->where('qa_question_id', '=', $question->id)
                ->select('qa_text as text', 'qa_id as id', 'qa_index as index')
                ->orderBy('index', 'asc')
                ->get();
        }

        return $result;
    }

}