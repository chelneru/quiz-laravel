<?php
/**
 * Created by PhpStorm.
 * User: au614698
 * Date: 12-11-2018
 * Time: 13:02
 */

namespace App\Services;

use App\Repositories\Quiz\QuizRepository;
use App\Repositories\Score\ScoreRepository;
use Auth;


class ScoresService
{


    public static function GetQuizScores($type, $id, $session = null)
    {
        $result = ['status' => true, 'message' => 'success', 'value' => null];

        $content_result = [];
        if ($type == 'quiz') {
            $is_assessed = QuizService::IsQuizAssessed($id);
            if ($is_assessed !== null) {
                $responses = QuizRepository::GetQuizScores($id, null, $is_assessed);
                //we have a quiz view we group everything by user and quiz
                return $responses;
            }

            return ['status' => false, 'message' => 'quiz not found', 'content' => null];
        }

        if ($type == 'session') {
            $quiz_id = QuizRepository::GetQuizIdFromSession($id);
            $is_assessed = QuizService::IsQuizAssessed($quiz_id);
            if ($is_assessed !== null) {
                $responses = QuizRepository::GetQuizScores($quiz_id, $id, $is_assessed);
                //we have a quiz view we group everything by user and quiz
                return $responses;
            }

            return ['status' => false, 'message' => 'quiz not found', 'content' => null];
        }

        if ($type == 'participant') {
            $teacher_id = Auth::user()->u_id;

                $responses = QuizRepository::GetParticipantScores($id, $teacher_id);
                //we have a quiz view we group everything by user and quiz
                return $responses;
            }
        return ['status' => false, 'message' => 'invalid type', 'content' => null];

    }

    public static function GetParticipantOverview($teacher_id, $participant)
    {
        return ScoreRepository::GetParticipantOverview($teacher_id, $participant);
    }
}
