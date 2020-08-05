<?php


namespace App\Repositories\QuizExport;


use DB;
use stdClass;

class QuizExportRepository
{
    public static function GetSessionResponses($session_id, $exclude_incomplete, $is_assessed)
    {
        $select_array = ['pr_id as id', 'pr_finished_at as finished_at', 'pr_started_at as started_at'];
        $participants = DB::table('participants_progress')
            ->where('pr_session_id', '=', $session_id);
        if ($exclude_incomplete == true) {
            $participants = $participants->where('pr_finished', '=', 1);
        }
        if ($is_assessed) {
            $participants = $participants->leftJoin('users', 'u_id', '=', 'pr_user_ud');
            $select_array = array_merge($select_array, ['u_first_name as first_name', 'u_last_name as last_name']);
        }
        $participants = $participants->select($select_array)
            ->get()->toArray();

        $responses = DB::table('quiz_responses')
            ->where('qr_session_id', '=', $session_id)
            ->select('qr_id as id', 'qr_answer_index as answer_index', 'qr_qp_id as participant_id', 'qr_phase as phase', 'qr_question_id as question_id')
            ->orderBy('participant_id')
            ->get()->toArray();
        $acc_questions_responses = DB::table('quiz_responses_acc_questions')
            ->where('qraq_session_id', $session_id)
            ->select('qraq_response_id as response_id', 'qraq_acc_id as acc_question_id', 'qraq_acc_answer_index as answer_index', 'qraq_acc_answer_content as answer_content',
                'qraq_qp_id as participant_id')
            ->get()->toArray();

        $object = [];

        foreach ($participants as &$participant) {
            $participant->responses = [];
            $participant->acc_responses = [];
            $object[$participant->id] = json_decode(json_encode($participant), true);
        }
        foreach ($responses as &$response) {
            $response->acc_responses = [];

            if (isset($object[$response->participant_id])) {
                $object[$response->participant_id]['responses'][$response->id] = json_decode(json_encode($response), true);
            }
        }
        foreach ($acc_questions_responses as $acc_questions_response) {
            if (isset($object[$acc_questions_response->participant_id]['responses'][$acc_questions_response->response_id])) {
                $object[$acc_questions_response->participant_id]['responses'][$acc_questions_response->response_id]['acc_responses'][] = json_decode(json_encode($acc_questions_response), true);
            } else if (isset($object[$acc_questions_response->participant_id])) {
                $object[$acc_questions_response->participant_id]['acc_responses'][] = json_decode(json_encode($acc_questions_response), true);
            }
        }

//        $time_elapsed_secs = microtime(true) - $start;

        return $object;
    }

    public static function GetSessionResultsResponses(int $session_id)
    {
        $result = DB::table('quiz_responses')
            ->select('u_id as user_id',
                'qr_phase as phase',
                'qr_question_id as question_id',
                'qr_answer_index as answer_index',
                'qr_date as date')
            ->join('quiz_session', 'qs_id', '=', 'qr_session_id')
            ->join('participants_progress', 'pr_id', '=', 'qr_qp_id')
            ->join('users', 'u_id', '=', 'pr_user_id')
            ->where('qr_session_id', '=', $session_id)
//            ->where('qs_started_at', '<=', DB::raw('qr_date'))
//            ->where('qs_revealed_ans_at', '>=', DB::raw('qr_date'))
            ->where('qr_phase', '=', 2)
            ->get();
        return $result;
    }
}
