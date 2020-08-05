<?php


namespace App\Services;


use App\Repositories\LeaderboardRepository;

class LeaderboardService
{
    public static function GetTotalNumberOfAnswers($class_id)
    {
        return LeaderboardRepository::GetTotalNumberOfAnswers($class_id);
    }

    public static function GetTotalOfParticipantsWhoAnswered($class_id)
    {
        return LeaderboardRepository::GetTotalOfParticipantsWhoAnswered($class_id);
    }

    public static function GetLeaderboardinfo($class_id)
    {
            return LeaderboardRepository::GetLeaderboardinfo($class_id);
    }

    public static function GetParticipantLeaderboardInfo(int $class_id, $user_id)
    {
        return LeaderboardRepository::GetParticipantLeaderboardInfo($class_id,$user_id);

    }

}