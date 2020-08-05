<?php
/**
 * Created by PhpStorm.
 * User: au614698
 * Date: 12-11-2018
 * Time: 14:25
 */

namespace App\Repositories\QuestionAnswer;


use DB;

class QuestionAnswerRepository
{
 public static function CreateNewQuestionAnswer($answer_text,$question_id,$answer_index) {

     $answer_data = [
         'qa_text' => $answer_text,
         'qa_question_id' => $question_id,
         'qa_index' => $answer_index,
         'qa_active' => 1
     ];

     $new_answer_id = DB::table('question_answers')
         ->insertGetId($answer_data);

     if ($new_answer_id !== null) {
         return ['status' => true, 'message' => 'success', 'value' => $new_answer_id];
     } else {
         return ['status' => false, 'message' => 'failed-inserting-answer', 'value' => $new_answer_id];

     }
 }

    public static function UpdateAnswer($answer_id, $answer_text, $answer_index)
    {
        try {
            DB::table('question_answers')
                ->where('qa_id', '=', $answer_id)
                ->update([
                    'qa_text' => $answer_text,
                    'qa_index' => $answer_index
                ]);

            return ['status' => true, 'message' => 'success'];
        } catch (\Exception $e) {
            return ['status' => false, 'message' => $e->getMessage() . ' in file: ' . $e->getFile() . ' at line: ' . $e->getLine()];

        }
    }

    public static function DeleteQuestionAnswer($qa_id)
    {
        DB::table('question_answers')
            ->where('qa_id','=',$qa_id)
            ->delete();
    }

    public static function DeleteQuestionAnswersByQuestionId($question_id)
    {

        DB::table('question_answers')
            ->where('qa_question_id','=',$question_id)
            ->delete();
    }
}