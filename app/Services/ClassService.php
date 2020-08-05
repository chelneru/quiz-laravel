<?php
/**
 * Created by PhpStorm.
 * User: au614698
 * Date: 16-11-2018
 * Time: 14:05
 */

namespace App\Services;

use App\Http\Controllers\QuizController;
use App\Repositories\ClassRoom\ClassRepository;
use App\Repositories\Quiz\QuizRepository;
use Carbon\Carbon;
use Exception;
use DB;
use Mail;
use Illuminate\Support\Str;


class ClassService
{

public static function GetTeacherClasses($user_id,$paginate = null)
    {
        return ClassRepository::GetTeacherClasses($user_id,$paginate);
    }

    public static function GetTeacherDashboardClasses($user_id)
    {
        return ClassRepository::GetTeacherDashboardClasses($user_id);
    }

    public static function GetTeacherClassesInfo($user_id)
    {
        return ClassRepository::GetTeacherClassesInfo($user_id);
    }

    public static function AddQuizToClass($new_quiz_id, $class_id): array
    {
        return ClassRepository::AddQuizToClass($new_quiz_id, $class_id);
    }

    public static function ImportQuizToClass($user_id,$new_quiz_id, $class_id): array
    {
        $result = QuizService::DuplicateQuiz($user_id,$new_quiz_id,false,false);
        if($result['status'] === true) {
            $new_quiz_id = $result['quiz_id']; //the quiz id that we will add is the id of the new duplicated quiz
            return ClassRepository::AddQuizToClass($new_quiz_id, $class_id);
        }

        return $result;
    }

    /**
     * @param $class_name
     * @param $class_description
     * @param $user_id
     * @return array
     */
    public static function CreateClass($class_name, $class_description, $user_id): array
    {
        $result = self::ValidateClass($class_name);
        if ($result['status'] === false) {
            return $result;
        }

        $class_name = shortenedString($class_name, 191, false);
        $class_description = shortenedString($class_description ??'', 191, false);

        $class_code = self::GenerateClassCode(6);
        $create_result = ClassRepository::CreateClass($class_name, $class_description, $user_id, $class_code);
        $created_add_result = ClassRepository::AddClassUsers($create_result['value'], [$user_id]);
        return $create_result;
    }

    public static function ValidateClass($class_name): array
    {
        $is_valid = true;
        if ($class_name === null || $class_name === '') {
            $is_valid = false;
            return ['status' => $is_valid, 'message' => 'invalid-class-name'];
        }

        return ['status' => $is_valid, 'message' => 'success', 'value' => null];
    }


    public static function CopyClass($original_class_id, $class_name, bool $copy_participants, bool $copy_teachers, bool $copy_quizzes,$user_id): array
    {
        $class_quizzes = null;
        $class_participants = null;
        $class_teachers = null;

        //TODO Validate input

        $class_info = self::GetClassInfo($original_class_id);

        if($class_info !== null){
        if ($class_name === '' || $class_name === null) {
            $today = Carbon::now('Europe/Copenhagen')->toDateString();
            $class_name = $class_info->name . ' - Copy ' . $today;
        }

        if ($copy_quizzes === true) {
            $class_quizzes = self::GetClassQuizzesIds($original_class_id);
        }
        if ($copy_participants === true) {
            $class_participants = self::GetClassParticipantsIds($original_class_id);
        }
        if ($copy_teachers === true) {
            $class_teachers = self::GetClassTeachers($original_class_id);
            $class_teachers = $class_teachers['result'];
        }

        try {
            DB::beginTransaction();
            $new_class_id = self::CreateClass($class_name, $class_info->description, $class_info->user_id);

            //add quizzes
            if ($copy_quizzes === true && $class_quizzes !== null) {

                $new_class_quizzes = [];
                foreach ($class_quizzes as $class_quiz) {
                    $result = QuizService::DuplicateQuiz($user_id, $class_quiz,false,false);
                    if ($result['status'] === true) {
                        $new_class_quizzes[] = $result['quiz_id'];
                    } else {
                        DB::rollback();
                        return ['status' => false, 'message' => $result['message']];
                    }
                }
                $add_quizzes_result = self::AddClassQuizzes($new_class_id['value'], $new_class_quizzes);
            }

            //add participants
            if ($copy_participants === true && $class_participants !== null) {
                $add_participants_result = ClassRepository::AddClassUsers($new_class_id['value'], $class_participants);
            }

            if ($copy_teachers === true && $class_teachers !== null) {
                $add_teachers_result = ClassRepository::AddClassUsers($new_class_id['value'], $class_teachers);
            }
            DB::commit();

        } catch (Exception $e) {
            DB::rollback();
            return ['status' => false, 'message' => $e->getMessage() . ' in file: ' . $e->getFile() . ' at line: ' . $e->getLine(),'class_name'=>$class_name];

        }
        return ['status' => true, 'message' => 'success','class_name'=>$class_name];
        }
        else {
            return ['status' => false, 'message' => 'class not found','class_name'=>$class_name];
        }
    }

    public static function GetClassInfo($class_id)
    {
        return ClassRepository::GetClassInfo($class_id);
    }

    public static function UpdateClassInfo($class_id, $class_name, $class_desc = null): array
    {
        return ClassRepository::UpdateClassInfo($class_id, $class_name, $class_desc);
    }

    private static function GetClassQuizzesIds($class_id)
    {
        return ClassRepository::GetClassQuizzesIds($class_id);
    }

    public static function GetClassQuizzes($class_id)
    {
        return ClassRepository::GetClassQuizzes($class_id);
    }


    private static function GetClassParticipantsIds($class_id)
    {
        return ClassRepository::GetClassParticipantsIds($class_id);

    }

    public static function GetClassParticipants($class_id)
    {
        return ClassRepository::GetClassParticipants($class_id);

    }

    private static function GetClassTeachers($class_id)
    {
        return ClassRepository::GetClassTeachers($class_id);
    }

    public static function ManuallyInviteParticipants($class_id, $participant_first_names,$participant_last_names, $participant_emails): array
    {
        $successful_emails = [];
        $total_participant_names = count($participant_first_names);
        for ($iter = 0; $iter < $total_participant_names; $iter++) {
//            add invite into database
            $token = Str::Random(16);
            $result = ClassRepository::AddParticipantInvite($class_id, $token);

            if ($result['status'] === false) {
                return ['status' => false, 'message' => 'failed to create invite in db', 'successful_emails' => $successful_emails];
            }

            $email = $participant_emails[$iter];
            $link = url('/register/' . $token);
            try {
//            send invite e-mail
                MailService::SendInviteEmail($email,$participant_first_names[$iter],$participant_last_names[$iter],$link);

                ClassRepository::UpdateParticipantInvite($result['value']);
            } catch (Exception $e) {
                return ['status' => false, 'message' => $e->getMessage() . ' in file: ' . $e->getFile() . ' at line: ' . $e->getLine() . ' \n' . $e->getTraceAsString()];
            }
        }
        return ['status' => true, 'message' => 'success'];

    }

    public static function GetClassCode($class_id)
    {
        return ClassRepository::GetClassCode($class_id);

    }

    public static function GetClassByCode($class_code)
    {
        return ClassRepository::GetClassByCode($class_code);
    }

    public static function GetAvailableQuizzesForClass($class_id, $user_id): \Illuminate\Support\Collection
    {

        return ClassRepository::GetAvailableQuizzesForClass($class_id, $user_id);
    }

    public static function GetParticipantClasses($user_id): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        return ClassRepository::GetParticipantClasses($user_id);
    }

    public static function JoinClassByCode($participant_id, $class_code): ?array
    {
        $class = self::GetClassByCode($class_code);


        if ($class !== null) {
            //check if user is already enrolled
            $enrollment = self::CheckParticipantClassEnrollment($class->id,$participant_id);
            if(!$enrollment) {
            return self::AddClassUsers($class->id, [$participant_id]);
            }
            else {
                return ['status' => false, 'message' => 'already enrolled'];
            }
        }
        return ['status' => false, 'message' => 'no class found'];
    }


    /**
     * @param $session_id
     * @return ClassRepository|\Illuminate\Database\Eloquent\Model|object|null
     */
    public static function GetClassBySession($session_id)
    {
        return ClassRepository::GetClassBySession($session_id);
    }
    public static function GetClassByQuiz($quiz_id)
    {
        return ClassRepository::GetClassByQuiz($quiz_id);
    }

    public static function GetAllParticipantClasses($user_id)
    {
        return ClassRepository::GetAllParticipantClasses($user_id);
    }

    private static function AddClassQuizzes($class_id, $quizzes_ds)
    {
        return ClassRepository::AddClassQuizzes($class_id, $quizzes_ds);
    }

    public static function AddClassUsers($class_id, $users_ids): array
    {
        return ClassRepository::AddClassUsers($class_id, $users_ids);
    }

    public static function GetClassIdByToken($token)
    {
        return ClassRepository::GetClassIdByToken($token);
    }

    private static function GenerateClassCode($limit)
    {
        return substr(base_convert(sha1(uniqid(mt_rand(), true)), 16, 36), 0, $limit);
    }

    public static function VerifyClassCodeUniqueness($code): bool
    {
        return ClassRepository::VerifyClassCodeUniqueness($code);
    }

    public static function CheckClassOwnership($class_id, $user_id): ?bool
    {

        $user_role = UserService::GetUserRole($user_id);

        if ($user_role === null) {
            return false;
        }
        $class = self::GetClassInfo($class_id);
        $class_participants = self::GetClassParticipantsIds($class_id)->toArray();
        return (($class_id !== null) && ($user_role === 2) && //class is valid and the user is a teacher
                (($class->user_id === $user_id) || in_array($user_id, $class_participants, false))) || //either the user is the creator of the class or he is a participant of the class (as a teacher)
            ($user_role === 3);
    }

    public static function RemoveParticipantInvite($token): bool
    {
        return ClassRepository::RemoveParticipantInvite($token);
    }
    public static function CheckParticipantClassEnrollment($class_id,$user_id) {
        return ClassRepository::CheckParticipantClassEnrollment($class_id,$user_id);
    }
    public static function DeleteClass($class_id)
    {
        $result = ['status' => true, 'message' => 'success'];

        try {
            //delete class
            $class_deletion_result = ClassRepository::DeleteClass($class_id);
            if($class_deletion_result['status'] == true) {
                DB::beginTransaction();
                //delete all related quizzes
            $quizzes = ClassRepository::GetClassQuizzesIds($class_id);
            foreach ($quizzes as $quiz) {
                $result = QuizRepository::DeleteQuiz($quiz, false);
            }
            //delete links between users and this class
            ClassRepository::DeleteClassUsersLinks($class_id);
            //delete class invites
            ClassRepository::DeleteClassInvites($class_id);
            DB::commit();
            }
            else {
                $result = $class_deletion_result;
            }
        } catch (\Exception $e) {
            DB::rollback();
            return ['status' => false, 'message' => $e->getMessage() . ' in file: ' . $e->getFile() . ' at line: ' . $e->getLine()];

        }
        return $result;
    }
    public static function GetClassAuthor($class_id) {
        return ClassRepository::GetClassAuthor($class_id);
    }
}
