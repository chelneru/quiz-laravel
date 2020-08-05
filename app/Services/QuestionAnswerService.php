<?php
/**
 * Created by PhpStorm.
 * User: au614698
 * Date: 12-11-2018
 * Time: 14:31
 */

namespace App\Services;


use App\Repositories\QuestionAnswer\QuestionAnswerRepository;
use DB;

class QuestionAnswerService
{
    public static function ValidateQuestionAnswer($question_answer)
    {
        $message = '';
        $is_valid = true;
        if (is_array($question_answer) && !isAssoc($question_answer)) {
            foreach ($question_answer as $question_answer_item) {
                if (!isset($question_answer_item['answer_text']) ||
                    $question_answer_item['answer_text'] == '' ||
                    $question_answer_item['answer_text'] == null) {
                    $is_valid = false;
                    $message = 'invalid-answer-text';
                }
                if (
                    !is_numeric($question_answer_item['answer_index']) ||
                    $question_answer_item['answer_index'] < 0
                ) {
                    $is_valid = false;
                    $message = 'invalid-answer-index';
                }

            }
        }
        else {

                if (!isset($question_answer['answer_text']) ||
                    $question_answer['answer_text'] == '' ||
                    $question_answer['answer_text'] == null) {
                    $is_valid = false;
                    $message = 'invalid-answer-text';
                }
                if (
                    !is_numeric($question_answer['answer_index']) ||
                    $question_answer['answer_index'] < 0
                ) {
                    $is_valid = false;
                    $message = 'invalid-answer-index';
                }

        }
        return ['status' => $is_valid, 'message' => $message];
    }

    public static function CreateNewQuestionAnswer($question_id, $question_answers)
    {
        if (is_array($question_answers) && !isAssoc($question_answers)) {
            foreach ($question_answers as $question_answer) {
                $valid_answer = QuestionAnswerService::ValidateQuestionAnswer($question_answer);
                if($valid_answer['status'] == false) {
                    return $valid_answer;
                }
            }
        } else {
            $valid_answer = QuestionAnswerService::ValidateQuestionAnswer([$question_answers]);
        }
        if ($valid_answer['status'] == true) {

            if (is_array($question_answers) && !isAssoc($question_answers)) {
                foreach ($question_answers as $question_answer) {
                    $result = QuestionAnswerRepository::CreateNewQuestionAnswer($question_answer['answer_text'], $question_id, $question_answer['answer_index']);

                }
            } else {
                $result = QuestionAnswerRepository::CreateNewQuestionAnswer($question_answers['answer_text'], $question_id, $question_answers['answer_index']);

            }
            return $result;
        } else {
            return ['status' => false, 'message' => $valid_answer['message'], 'value' => null];
        }
    }

    public static function UpdateQuestionAnswers($question_id, $question_answers)
    {
        $answers_validation_result = QuestionAnswerService::ValidateQuestionAnswer($question_answers);

        if (is_array($question_answers)&& !isAssoc($question_answers)) {
            foreach ($question_answers as $question_answer) {
                $answers_validation_result = QuestionAnswerService::ValidateQuestionAnswer($question_answer);
                if($answers_validation_result['status'] == false) {
                    return $answers_validation_result;
                }
            }
        } else {
            $answers_validation_result = QuestionAnswerService::ValidateQuestionAnswer($question_answers);
        }

        if ($answers_validation_result['status'] == true) {

            $existing_answers = DB::table('question_answers')
                ->select('qa_id as id')
                ->where('qa_question_id', '=', $question_id)
                ->pluck('id');

            //remove from db quiz's questions that are no longer present in the $questions array
            foreach ($existing_answers as $existing_answer) {
                $found_answer = null;
                foreach ($question_answers as $question_answer) {
                    if ($question_answer['answer_id'] == $existing_answer) {
                        $found_answer = true;
                    }
                }
                if ($found_answer == null) {
                    //if not found we remove the link between current quiz and current question
                    QuestionAnswerService::DeleteQuestionAnswer($existing_answer);

                }

            }
            //update questions
            foreach ($question_answers as $answer) {
                if ($answer['answer_id'] !== null) {
                    //update question
                    $result = QuestionAnswerRepository::UpdateAnswer($answer['answer_id'], $answer['answer_text'], $answer['answer_index']);
                    if ($result['status'] == false) {
                        return $result;

                    }
                } else {
                    //create new question answer
                    $new_answer = QuestionAnswerRepository::CreateNewQuestionAnswer($answer['answer_text'], $question_id,
                        $answer['answer_index']);
                    if ($new_answer['status'] == false) {
                        return $new_answer;

                    }

                }
            }
            return ['status' => true, 'message' => 'success'];

        } else {
            return $answers_validation_result;
        }
    }

    public static function DeleteQuestionAnswersByQuestionId($question_id)
    {
        QuestionAnswerRepository::DeleteQuestionAnswersByQuestionId($question_id);

    }

    public static function DeleteQuestionAnswer($qa_id) {
        QuestionAnswerRepository::DeleteQuestionAnswer($qa_id);
    }
}