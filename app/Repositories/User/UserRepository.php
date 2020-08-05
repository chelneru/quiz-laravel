<?php
/**
 * Created by PhpStorm.
 * User: au614698
 * Date: 29-10-2018
 * Time: 13:33
 */

namespace App\Repositories\User;

use App\User;
use Carbon\Carbon;

use DB;
use Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Hash;

class UserRepository
{

    public static function CreateNormalUser($username, $password, $email, $first_name = null, $last_name = null, $department = null, $position = null, $role = 1)
    {
        $new_user = null;
        try {


            $new_user = new User();

            $new_user->name = $username;
            $new_user->password = Hash::make($password);
            $new_user->email = $email;
            $new_user->u_first_name = $first_name;
            $new_user->u_last_name = $last_name;
            $new_user->u_department = $department;
            $new_user->u_position = $position;
            $new_user->u_role = $role;

            $new_user->save();
        } catch (\Exception $e) {
            return null;

        }
        return $new_user;
    }

    public static function CreateTeacherUser($username, $password, $email, $first_name = null, $last_name = null, $department = null, $position = null)
    {
        $new_user = null;
        try {


            $new_user = new User();

            $new_user->name = $username;
            $new_user->password = Hash::make($password);
            $new_user->email = $email;
            $new_user->u_first_name = $first_name;
            $new_user->u_last_name = $last_name;
            $new_user->u_department = $department;
            $new_user->u_position = $position;
            $new_user->created_at = Carbon::now('Europe/Copenhagen')->toDateTimeString();
            $new_user->u_last_login = Carbon::now('Europe/Copenhagen')->toDateTimeString();
            $new_user->u_role = 2;
            $new_user->u_active = 0;

            $new_user->save();
        } catch (\Exception $e) {
            return null;

        }
        return $new_user;
    }

    public static function CreateAdminUser($username, $password, $email)
    {
        $data = [
            'name' => $username,
            'password' => Hash::make($password),
            'email' => $email,
            'u_role' => 3
        ];
        $result = User::insert($data['name'], $data['password'], $data['email'],
            $data['u_first_name']);
    }

    public static function GetUserRoleName($user_id)
    {

        try {
            $result = DB::table('roles')
                ->where('u_id', '=', $user_id)
                ->join('users', 'u_role', '=', 'r_id')
                ->select('r_name as name')
                ->first();

        } catch (\Exception $exception) {
            debug(['query exception' => $exception->getMessage(),
                'line' => $exception->getLine(),
                'file' => $exception->getFile()]);

            return 'No role found';
        }
        return $result->name;
    }

    public static function UpdateUserInfo($user_id, $user_email, $user_first_name, $user_last_name,
                                          $user_department = null, $user_position = null)
    {
        try {


            $user = User::find($user_id);
            $user->email = $user_email;
            $user->u_first_name = $user_first_name;
            $user->u_last_name = $user_last_name;
            $user->u_department = $user_department;
            $user->u_position = $user_position;

            $user->save();
            return ['status' => true, 'message' => 'success'];

        } catch (\Exception $e) {
            return ['status' => false, 'message' => $e->getMessage() . ' in file: ' . $e->getFile() . ' at line: ' . $e->getLine()];

        }
    }

    public static function GetTeacherParticipantsCount($user_id)
    {
        $participants = DB::table('classes')
            ->join('class_users', 'cu_class_id', '=', 'class_id')
            ->join('users', 'cu_user_id', '=', 'u_id')
            ->where('u_role', '=', 1)
            ->where("class_created_by", '=', $user_id)
            ->select('u_id as id', 'u_role as role', 'u_active as active')
            ->groupBy('id')
            ->get();

        $active_participants = $participants->where('active', '=', 1)
            ->count();
        $participants = $participants->count();

        $result = new \stdClass();
        $result->total = $participants;
        $result->active_participants = $active_participants;
        $result->inactive_participants = $participants - $active_participants;

        return $result;

    }

    public static function GetTeacherParticipants($user_id, $class_filter, $role_filter, $order_by_filter, $order_dir_filter, $participant_filter)
    {
        $participants = DB::table('classes')
            ->join('class_users', 'cu_class_id', '=', 'class_id')
            ->join('users', 'u_id', '=', 'cu_user_id')
            ->where('class_created_by', '=', $user_id);

        $class_names_query = "GROUP_CONCAT(class_name ORDER BY class_name SEPARATOR ', ')";
        $class_ids_query = "GROUP_CONCAT(class_id ORDER BY class_id SEPARATOR ', ')";
        $order_dir = null;
        $order_by = null;
        if ($order_dir_filter == 'asc' || $order_dir_filter == 'desc') {
            $order_dir = $order_dir_filter;
        }
        if ($order_by_filter == 'name' || $order_by_filter == 'email' || $order_by_filter == 'class_names') {
            $order_by = $order_by_filter;
        }

        if ($participant_filter !== null) {
            $participants = $participants->where(function ($query) use ($participant_filter) {

                $query->where(DB::raw('CONCAT(u_first_name, \' \', u_last_name)'), 'like', '%' . $participant_filter . '%')
                    ->orWhere('email', 'like', '%' . $participant_filter . '%');
            });
        }
        if ($order_dir !== null && $order_by !== null) {
            $participants = $participants
                ->orderBy($order_by, $order_dir);
        }
        if ($class_filter !== null) {
            $participants = $participants->having('class_ids', 'like', '%' . $class_filter . '%');

            $class_names_query = 'GROUP_CONCAT(class_name ORDER BY FIELD(class_id,' . (int)($class_filter) . ") DESC SEPARATOR ', ')";
            $class_ids_query = 'GROUP_CONCAT(class_id ORDER BY FIELD(class_id,' . (int)($class_filter) . ") DESC SEPARATOR ', ')";
        }
        if ($role_filter !== null && ($role_filter == 1 || $role_filter == 2)) {
            $participants = $participants
                ->where('u_role', '=', $role_filter);
        }

        $participants = $participants->select('u_id as id', 'u_role as role', 'u_active as active',
            DB::raw('CONCAT(u_first_name, \' \', u_last_name) as name'),
            DB::raw($class_names_query . ' as class_names'),
            DB::raw($class_ids_query . ' as class_ids'),
            'email');
        $participants = $participants
            ->groupBy('u_id')->get();


        $totalItems = $participants->count();
        $page = Request::get('page', 1);
        $perPage = 10;
        $offset = ($page * $perPage) - $perPage;
        //Instead of just using ->paginate(10) we have to create a paginator as the query from the function paginate() does not accept HAVING command.
        return new LengthAwarePaginator(array_slice($participants->all(), $offset, $perPage, true), count($participants), $perPage, $page, ['path' => \Request::url(), 'query' => \Request::query()]);
    }

    public static function GetTeacherAllParticipants($user_id, $active_filter, $role_filter)
    {
        $participants = DB::table('classes')
            ->join('class_users', 'cu_class_id', '=', 'class_id')
            ->join('users', 'u_id', '=', 'cu_user_id')
            ->where('class_created_by', '=', $user_id)
            ->select('u_id as id', 'u_role as role', 'u_active as active', DB::raw('CONCAT(u_first_name, \' \', u_last_name) as name'), 'email');

        if ($active_filter !== null && ($active_filter == 1 || $active_filter === 2)) {
            $participants = $participants
                ->where('u_active', '=', $active_filter - 1);
        }
        if ($role_filter !== null && ($role_filter == 1 || $role_filter == 2)) {
            $participants = $participants
                ->where('u_role', '=', $role_filter);
        }

        $participants = $participants
            ->groupBy('u_id')
            ->get();

        return $participants;
    }

    public static function ChangeUserStatus($user_id, $user_status)
    {
        DB::table('users')
            ->where("u_id", '=', $user_id)
            ->update([
                'u_active' => $user_status
            ]);
    }

    public static function GetUserRole($user_id)
    {
        try {
            $result = DB::table('users')
                ->where('u_id', '=', $user_id)
                ->select('u_role as id')
                ->first();

        } catch (\Exception $exception) {
            debug(['query exception' => $exception->getMessage(),
                'line' => $exception->getLine(),
                'file' => $exception->getFile()]);

            return null;
        }
        return $result->id;
    }

    public static function DismissParticipant($user_id, $class_ids, $teacher_id)
    {
        try {
            DB::table('class_users')
                ->join('classes', 'class_id', '=', 'cu_class_id')
                ->where('class_created_by', '=', $teacher_id)
                ->where('cu_user_id', '=', $user_id)
                ->whereIn('cu_class_id', $class_ids)
                ->delete();
            return ['status' => true];
        } catch (\Exception $e) {
            DB::rollback();
            return ['status' => false, 'message' => $e->getMessage() . ' in file: ' . $e->getFile() . ' at line: ' . $e->getLine()];

        }
    }

    public static function DeleteUserInfo($user_id)
    {
        try {
            DB::beginTransaction();
            $classes = DB::table('classes')
                ->where('class_created_by', '=', $user_id)
                ->select('class_id as id')
                ->pluck('id');

            foreach ($classes as $class) {
                DB::table('class_invites')
                    ->where('ci_class_id', '=', $class)
                    ->delete();
                DB::table('class_quizzes')
                    ->where('cq_class_id', '=', $class)
                    ->delete();
                DB::table('class_users')
                    ->where('cu_class_id', '=', $class)
                    ->delete();
                DB::table('classes')
                    ->where('class_id', '=', $class)
                    ->delete();
            }
            $quizzes = DB::table('quizzes')
                ->where('quiz_created_by', '=', $user_id)
                ->select('quiz_id as id')
                ->pluck('id');
            foreach ($quizzes as $quiz) {
                $questions = DB::table('quiz_questions')
                    ->where('qq_quiz_id', '=', $quiz)
                    ->select('qq_question_id as id')
                    ->pluck('id');
                DB::table('quiz_questions')
                    ->where('qq_quiz_id', '=', $quiz)
                    ->delete();
                //delete question answers
                DB::table('question_answers')
                    ->whereIn('qa_question_id', $questions)
                    ->delete();
                //delete acc questions
                $acc_questions = DB::table('accompanying_questions')
                    ->where('aq_quiz_id', '=', $quiz)
                    ->select('aq_id as id')
                    ->pluck('id');

                DB::table('accompanying_questions_answers')
                    ->whereIn('aqa_question_id', $acc_questions)
                    ->delete();
                DB::table('accompanying_questions_feedback')
                    ->whereIn('aqf_question_id', $acc_questions)
                    ->delete();
                DB::table('accompanying_questions_positions')
                    ->whereIn('aqp_question_id', $acc_questions)
                    ->delete();
                DB::table('accompanying_questions')
                    ->whereIn('aq_id', $acc_questions)
                    ->delete();
                // sessions
                $sessions = DB::table('quiz_session')
                    ->where('qs_quiz_id', '=', $quiz)
                    ->select('qs_id as id')
                    ->pluck('id');
                //delete participants progress
                DB::table('participants_progress')
                    ->whereIn('pr_session_id', $sessions)
                    ->delete();
                //delete responses
                DB::table('quiz_responses')
                    ->whereIn('qr_session_id', $sessions)
                    ->delete();
                DB::table('quiz_responses_acc_questions')
                    ->whereIn('qraq_session_id', $sessions)
                    ->delete();
                DB::table('quiz_session')
                    ->whereIn('qs_id', $sessions)
                    ->delete();
                //delete quiz scheduling, additional message
                DB::table('quiz_additional_messages')
                    ->where('qam_quiz_id', '=', $quiz)
                    ->delete();
                DB::table('quiz_scheduling')
                    ->where('qsch_quiz_id', '=', $quiz)
                    ->delete();
                DB::table('quizzes')
                    ->where('quiz_id', '=', $quiz)
                    ->delete();
            }
            DB::table('users')
                ->where('u_id', '=', $user_id)
                ->delete();
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return ['status' => false, 'message' => $e->getMessage() . ' in file: ' . $e->getFile() . ' at line: ' . $e->getLine()];

        }
        return ['status' => true, 'message' => 'success'];

    }

    public static function DeleteRelatedParticipantInfo($user_id)
    {
        DB::table('class_users')
            ->where('cu_user_id', '=', $user_id)
            ->delete();
        //delete all responses from this participant
//        $participant_ids = DB::table('participants_progress')
//            ->where('pr_user_id','=',$user_id)
//            ->select('pr_id as id')
//            ->pluck('id');
//        foreach ($participant_ids as $pid) {
//            //delete scores
//            DB::table('participant_scores')
//                ->where('ps_participant_id','=',$pid)
//                ->delete();
//            //delete responses
//            DB::table('quiz_responses')
//                ->where('qr_qp_id','=',$pid)
//                ->delete();
//            //delete acc questions responses
//            DB::table('quiz_responses_acc_questions')
//                ->where('qraq_qp_id','=',$pid)
//                ->delete();
//            //finally delete this participant id
//            DB::table('participants_progress')
//                ->where('pr_id','=',$pid)
//                ->delete();
//        }

        return true;
    }

    public static function CheckTemporaryParticipationId($temp_participation_id, $session_id)
    {
        $result = DB::table('participants_progress')
            ->where('pr_id', '=', $temp_participation_id)
            ->where('pr_session_id', '=', $session_id)
            ->select('pr_id as id')
            ->pluck('id')
            ->first();
        return $result;
    }

    public static function CreateTemporaryParticipant($session_id)
    {
        $now = Carbon::now('Europe/Copenhagen')->toDateTimeString();
        $result_id = DB::table('participants_progress')
            ->insertGetId([
                'pr_user_id' => 0,
                'pr_session_id' => $session_id,
                'pr_anonymous' => 1,
                'pr_index' => 0,
                'pr_phase' => 0,
                'pr_displayed_message' => 0,
                'pr_started_at' => $now,
                'pr_updated_at' => $now
            ]);
        return $result_id;
    }

    public static function GetTeacherParticipantsNames($teacher_id, $assessment_only)
    {
        $participants = DB::table('classes')
            ->join('class_users', 'cu_class_id', '=', 'class_id')
            ->join('users', 'u_id', '=', 'cu_user_id')
            ->where('class_created_by', '=', $teacher_id)
            ->select(DB::raw('CONCAT(u_first_name, \' \', u_last_name) as name'));
        if ($assessment_only == true) {
            $participants = $participants->join('class_quizzes', 'class_id', '=', 'cq_class_id')
                ->join('quizzes', 'quiz_id', '=', 'cq_quiz_id')
                ->where('quiz_is_assessed', '=', 1);
        }
        $participants = $participants->pluck('name');

        return $participants;
    }

    public static function GetTeacherParticipantsEmails($teacher_id, $assessment_only)
    {

        $participants = DB::table('classes')
            ->join('class_users', 'cu_class_id', '=', 'class_id')
            ->join('users', 'u_id', '=', 'cu_user_id')
            ->where('class_created_by', '=', $teacher_id)
            ->select('email');

        if ($assessment_only == true) {
            $participants = $participants->join('class_quizzes', 'class_id', '=', 'cq_class_id')
                ->join('quizzes', 'quiz_id', '=', 'cq_quiz_id')
                ->where('quiz_is_assessed', '=', 1);
        }
        $participants = $participants->pluck('email');
        return $participants;
    }

    public static function GetTeacherParticipantsUserIds($teacher_id)
    {
        $participants = DB::table('classes')
            ->join('class_users', 'cu_class_id', '=', 'class_id')
            ->join('users', 'u_id', '=', 'cu_user_id')
            ->where('class_created_by', '=', $teacher_id)
            ->select('u_id as user_id')
            ->pluck('user_id');
        return $participants;
    }

    public static function IsAdmin($user_id)
    {
        $role = DB::table('users_admins')
            ->where('ua_u_id', '=', $user_id)
            ->first();
        return $role !== null;
    }

    public static function GetUserInfo($user_id)
    {
        $result = DB::table('users')
            ->where('u_id', '=', $user_id)
            ->select('u_id as id', 'email', 'u_first_name as first_name', 'u_last_name as last_name', 'u_role as role',
                'u_department as department', 'u_position as position')
            ->first();
        return $result;
    }

    public static function GetTeacherActiveStatus($user_id)
    {
        $result = DB::table('users')
            ->where('u_id', '=', $user_id)
            ->select('u_active')
            ->first();
        return $result->u_active == 1;

    }

}
