<?php
/**
 * Created by PhpStorm.
 * User: au614698
 * Date: 12-11-2018
 * Time: 14:27
 */

namespace App\Services;


use App\Repositories\Question\QuestionRepository;

class QuestionService
{
    public static function ValidateQuestion($question_text, $question_index, $question_right_answer, $user_id)
    {
        $message = 'success';
        $valid = true;

        if ($question_text == '' || $question_text == null) {
            $valid = false;
            $message = 'missing-question-title';
        }

         else if ($question_index == '' || $question_index == null || !is_numeric($question_index)) {
            $valid = false;
            $message = 'question-invalid-index';

        }

         else if ($question_right_answer === null) {
            $valid = false;
            $message = 'question-right-answer-missing';
        }

         else if (!is_numeric($user_id)) {
            $valid = false;
            $message = 'question-invalid-user-id';
        }


        return ['status' => $valid, 'message' => $message,'value'=>null];
    }

    public static function CreateNewQuestion($question_text, $question_right_answer, $user_id, $question_index,$image_link)
    {
        $is_valid = self::ValidateQuestion($question_text, $question_index,
            $question_right_answer, $user_id);
        if ($is_valid['status'] === true) {
            $result = QuestionRepository::CreateNewQuestion($question_text, $question_right_answer,$image_link,
                $user_id);
            return $result;
        }

        return $is_valid;

    }

    public static function UpdateQuestion($question_id, $question_answers, $question_text,
                                          $quiz_id, $question_index, $question_correct_answer,$image_link, $user_id)
    {

        $is_valid = QuestionService::ValidateQuestion($question_text,
            $question_index, $question_correct_answer,
            $user_id);
        if ($is_valid['status'] == true) {
            $update_question_result = QuestionRepository::UpdateQuestion($question_id, $quiz_id, $question_text,
                $question_correct_answer, $image_link,$question_index);


            if ($update_question_result['status'] == false) {
                return $update_question_result;
            }


            $answers_update_result = QuestionAnswerService::UpdateQuestionAnswers($question_id,$question_answers);
            return $answers_update_result;
        } else {
            return $is_valid;
        }
    }

    public static function DeleteQuestion($question_id) {
        QuestionRepository::DeleteQuestion($question_id);
        //delete related question answers
        QuestionAnswerService::DeleteQuestionAnswersByQuestionId($question_id);
    }

    public static function GetTeacherQuestions($teacher_id,$class_id,$quiz_id)
    {
        $result = QuestionRepository::GetTeacherQuestions($teacher_id,$class_id,$quiz_id);

        return $result;
    } public static function GetTeacherQuestionsForImport($teacher_id,$class_id,$quiz_id)
    {
        $result = QuestionRepository::GetTeacherQuestionsForImport($teacher_id,$class_id,$quiz_id);

        return $result;
    }
}