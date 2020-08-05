<?php
/**
 * Created by PhpStorm.
 * User: au614698
 * Date: 12-11-2018
 * Time: 13:20
 */

namespace App\Repositories\Score;



use Carbon\Carbon;
use Config;
use DB;

class ScoreRepository
{

    public static function GetParticipantOverview($teacher_id, $participant)
    {
        $select_user_query_part = null;
        try {
            $result = DB::table('users')
                ->join('class_users', 'cu_user_id', '=', 'u_id')
                ->join('classes', 'cu_class_id', '=', 'class_id')
                ->join('participants_progress', 'pr_user_id', 'u_id')
                ->join('quiz_session', 'qs_id', '=', 'pr_session_id')
                ->join('quizzes', 'quiz_id', '=', 'qs_quiz_id');
            if (is_numeric($participant)) {
                //we have an user id
                $result = $result->where('u_id', '=', $participant);
                $select_user_query_part = 'u_id as name';
            } else {
                //we have user name
                $result = $result->where(DB::raw('CONCAT(u_first_name, \' \', u_last_name)'), 'like', '%' . $participant . '%');
                $select_user_query_part = DB::raw('CONCAT(u_first_name, \' \', u_last_name) as name');

            }

            $result = $result->where('class_created_by', '=', $teacher_id)
                ->where('quiz_created_by', '=', $teacher_id)
                ->select('u_id as id', $select_user_query_part, 'class_name', 'quiz_title','class_id','quiz_id',
                    DB::raw('GROUP_CONCAT(qs_id SEPARATOR \',\') as sessions'))
                ->groupBy('quiz_title', 'class_name', 'name', 'id','class_id','quiz_id')
                ->get();

            foreach ($result as $participation) {
                $participation->sessions = DB::table('quiz_session')
                    ->whereIn('qs_id',explode(',',$participation->sessions))
                    ->select('qs_id as id','qs_started_at as started_at')
                    ->get();
            }

            return ['status' => true, 'content' => $result, 'message' => 'success'];
        } catch (\Exception $e) {
            return ['status' => false, 'message' => $e->getMessage() . ' in file: ' . $e->getFile() . ' at line: ' . $e->getLine(), 'content' => null];
        }
    }
}
