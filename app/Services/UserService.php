<?php
/**
 * Created by PhpStorm.
 * User: au614698
 * Date: 29-10-2018
 * Time: 13:43
 */

namespace App\Services;


use App\Repositories\User\UserRepository;
use App\User;
use Hash;
use Illuminate\Support\Str;

class UserService
{
    public static function GetUserRole($user_id)
    {
        return UserRepository::GetUserRole($user_id);
    }

    public static function DismissParticipant($user_id, $class_ids, $teacher_id)
    {
        foreach ($class_ids as &$class_id) {
            $class_id = preg_replace('/\D/', '', $class_id);
        }
        unset($class_id);
        $class_ids = array_values($class_ids);
        return UserRepository::DismissParticipant($user_id, $class_ids, $teacher_id);
    }

    public static function DeleteRelatedParticipantInfo($user_id)
    {
        return UserRepository::DeleteRelatedParticipantInfo($user_id);

    }

    public static function UpdatePassword($user_id, $new_password)
    {
        try {
            $user = User::find($user_id);

            $user->password = Hash::make($new_password);

            $user->setRememberToken(Str::random(60));

            $user->save();
            return ['status' => true, 'message' => 'success'];
        } catch (\Exception $e) {
            return ['status' => false, 'message' => $e->getMessage() . ' in file: ' . $e->getFile() . ' at line: ' . $e->getLine()];

        }
    }

    public static function CheckTemporaryParticipationId($temp_participation_id, $session_id): bool
    {
        $result = UserRepository::CheckTemporaryParticipationId($temp_participation_id, $session_id);
        return $result !== null;
    }

    public static function CreateTemporaryParticipant($session_id)
    {
        return UserRepository::CreateTemporaryParticipant($session_id);
    }

    public static function GetTeacherParticipantsNames($teacher_id, $assessment_only = false)
    {
        return UserRepository::GetTeacherParticipantsNames($teacher_id, $assessment_only);
    }

    public static function GetTeacherParticipantsEmails($teacher_id, $assessment_only = false)
    {
        return UserRepository::GetTeacherParticipantsEmails($teacher_id, $assessment_only);
    }


    public static function CreateNewNormalUser($username, $password, $email, $first_name, $last_name, $department, $position, $role, $token = null, $quiz_id = null)
    {

        //TODO field validations
        if ($role == 1) {
            $result = UserRepository::CreateNormalUser($username, $password, $email, $first_name, $last_name, $department, $position, $role);
        } else {
            $result = UserRepository::CreateTeacherUser($username, $password, $email, $first_name, $last_name, $department, $position);

        }
        if ($token !== null && $result !== null) {
            $class_id = ClassService::GetClassIdByToken($token);
            if ($class_id !== null) {
                ClassService::AddClassUsers($class_id, [$result->u_id]);
            }
            ClassService::RemoveParticipantInvite($token);
        } else if ($quiz_id !== null) {
            $class_id = ClassService::GetClassByQuiz($quiz_id);
            if ($class_id !== null) {
                ClassService::AddClassUsers($class_id, [$result->u_id]);
            } else {
                session(['enrolled-quiz-id' => $quiz_id]);

            }
            $result['redirect'] = '/quiz/start-quiz/' . $quiz_id;
        }

        return $result;
    }

    public static function GetUserRoleName($user_id)
    {

        $result = UserRepository::GetUserRoleName($user_id);
        return $result;
    }

    public static function UpdateUserInfo($user_id, $user_email, $user_first_name, $user_last_name,
                                          $user_department, $user_position)
    {

        $result = UserRepository::UpdateUserInfo($user_id, $user_email, $user_first_name, $user_last_name,
            $user_department, $user_position);
        return $result;
    }

    public static function GetTeacherParticipantsCount($user_id)
    {
        $result = UserRepository::GetTeacherParticipantsCount($user_id);
        return $result;
    }

    public static function GetTeacherParticipantsForParticipantsIndexPage($user_id, $class_filter, $role_filter, $order_by_filter, $order_dir_filter, $participant_filter)
    {
        $result = UserRepository::GetTeacherParticipants($user_id, $class_filter, $role_filter, $order_by_filter, $order_dir_filter, $participant_filter);
        return $result;
    }

    public static function GetTeacherAllParticipants($user_id, $active_filter, $role_filter)
    {
        $result = UserRepository::GetTeacherAllParticipants($user_id, $active_filter, $role_filter);
        return $result;
    }

    public static function ChangeUserStatus($user_id, $user_status)
    {
        UserRepository::ChangeUserStatus($user_id, $user_status);
    }

    public static function GetTeacherParticipantsUserIds($teacher_id)
    {
        return UserRepository::GetTeacherParticipantsUserIds($teacher_id);
    }

    public static function IsAdmin($user_id)
    {
        return UserRepository::IsAdmin($user_id);
    }

    public static function ValidateUserInfoForUpdate($email, $first_name, $last_name)
    {
        if ($email == null || strlen(trim($email)) == 0 || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return false;
        }
        if ($first_name == null || strlen(trim($first_name)) == 0 || $last_name == null || strlen(trim($last_name)) == 0) {
            return false;
        }
        return true;
    }

    public static function GetTeacherActiveStatus($user_id) {
        return UserRepository::GetTeacherActiveStatus($user_id);
    }
    public static function IsTeacherQuizCreationLimitReached($user_id) {
        $status = self::GetTeacherActiveStatus($user_id);
        if($status === false) {
            //check number of quizzes
            $quizzes = QuizService::GetTeacherQuizzes($user_id);
            if(count($quizzes->items()) > 0) {
                return true;
            }
            return false;
        }
        return false;
    }
    public static function IsTeacherClassCreationLimitReached($user_id) {
        $status = self::GetTeacherActiveStatus($user_id);
        if($status === false) {
            //check number of classes
            $classes =ClassService::GetTeacherClasses($user_id);
            if(count($classes) > 0) {
                return true;
            }
            return false;
        }
        return false;
    }

}
