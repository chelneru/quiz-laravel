<?php


namespace App\Repositories;


use DB;

class LeaderboardRepository
{
    public static function GetTotalNumberOfAnswers($class_id)
    {
        $result = DB::table('class_quizzes')
            ->join('quiz_session','cq_quiz_id','=','qs_quiz_id')
            ->join('quiz_responses','qs_id','=','qr_session_id')
            ->where('cq_class_id','=',$class_id)
            ->count(DB::raw('DISTINCT qr_id'));
        return $result;
    }

    public static function GetTotalOfParticipantsWhoAnswered($class_id)
    {
        $result = DB::table('class_users')
            ->join('class_quizzes','cq_class_id','=','cu_class_id')
            ->join('quiz_session','cq_quiz_id','=','qs_quiz_id')
            ->join('participants_progress','qs_id','=','pr_session_id')
            ->where('pr_index','>',0)
            ->where('cu_class_id','=',$class_id)
            ->count(DB::raw('DISTINCT cu_id'));
        return $result;
    }

    public static function GetLeaderboardinfo($class_id)
    {
        $result = DB::table('class_users')
            ->leftJoin('participant_scores', 'ps_participant_id', '=', 'cu_user_id')
            ->leftJoin('participants_progress', 'pr_user_id', '=', 'cu_user_id')
            ->leftJoin('quiz_responses', 'qr_qp_id', '=', 'pr_id')
            ->where('cu_class_id','=',$class_id)
            ->havingRaw('avg_time IS NOT NULL')
            ->select('cu_user_id as user_id', DB::raw('AVG(qr_duration) as avg_time'), DB::raw('SUM(ps_value) as score'))
            ->groupBy('cu_user_id');
        $result_2 = $result;
            $times = $result->orderBy('avg_time','asc')->pluck('avg_time');
            $scores = $result_2->orderBy('score','asc')->pluck('score')->toArray();
        arsort($scores);

        return ['times'=>$times,'scores'=>$scores];
    }

    public static function GetParticipantLeaderboardInfo(int $class_id, $user_id)
    {
        $result = DB::table('class_users')
            ->leftJoin('participant_scores', 'ps_participant_id', '=', 'cu_user_id')
            ->leftJoin('participants_progress', 'pr_user_id', '=', 'cu_user_id')
            ->leftJoin('quiz_responses', 'qr_qp_id', '=', 'pr_id')
            ->where('cu_class_id','=',$class_id)
            ->where('cu_user_id','=',$user_id)
                ->havingRaw('avg_time IS NOT NULL')
            ->select('cu_user_id as user_id', DB::raw('AVG(qr_duration) as avg_time'), DB::raw('SUM(ps_value) as score'))
            ->groupBy('cu_user_id');
        $times = $result->pluck('avg_time')->first();
        $scores = $result->pluck('score')->first();
        return ['times'=>$times,'scores'=>$scores];
    }

}