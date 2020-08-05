<?php
/**
 * Created by PhpStorm.
 * User: au614698
 * Date: 16-11-2018
 * Time: 14:06
 */

namespace App\Repositories\ClassRoom;


use Carbon\Carbon;
use DB;

class ClassRepository
{
    public static function GetTeacherClasses($user_id, $paginate)
    {
        $result = DB::table('classes')
            ->select('class_name as name', 'class_id as id')
            ->where('class_created_by', '=', $user_id)
            ->orderBy('name', 'asc');
        if ($paginate !== null) {
            $result = $result->limit($paginate);
        }
        $result = $result->get();

        return $result;
    }


    public static function GetTeacherDashboardClasses($user_id)
    {
        $result = DB::table('classes')
            ->select('class_name as name', 'class_id as id', 'class_description as description')
            ->where('class_created_by', '=', $user_id)
            ->groupBy('class_id')
            ->orderBy('name')
            ->get();

        foreach ($result as &$class) {
            $class->quizzes = DB::table('class_quizzes')
                ->select(db::raw('count(Distinct quiz_id) as quizzes'))
                ->join('quizzes', 'quiz_id', '=', 'cq_quiz_id')
                ->where('cq_class_id', '=', $class->id)
                ->pluck('quizzes')
                ->first();

            $class->participants = DB::table('class_users')
                ->select(db::raw('count(Distinct cu_user_id) as participants'))
                ->where('cu_class_id', '=', $class->id)
                ->pluck('participants')
                ->first();
        }

        return $result;
    }

    public static function GetTeacherClassesInfo($user_id)
    {
        $result = DB::table('classes')
            ->select('class_name as name',
                'class_description as description',
                'class_id as id',
                DB::raw('DATE_FORMAT(class_created_on, \'%d-%m-%Y, %H:%i\') as date'))
            ->where('class_created_by', '=', $user_id);
        $result = $result->paginate(10);
        foreach ($result as &$class) {
            $class->participants = DB::table('class_users')
                ->select(DB::raw('COUNT(DISTINCT cu_user_id) as participants'))
                ->where('cu_class_id', '=', $class->id)
                ->join('users', 'u_id', 'cu_user_id')
                ->where('u_role', '=', 1)
                ->pluck('participants')
                ->first();

            $class->quizzes = DB::table('class_quizzes')
                ->select(DB::raw('COUNT(DISTINCT cq_quiz_id) as quizzes'))
                ->where('cq_class_id', '=', $class->id)
                ->pluck('quizzes')
                ->first();
        }

        return $result;
    }

    public static function AddQuizToClass($new_quiz_id, $class_id): array
    {
        $now = Carbon::now('Europe/Copenhagen')->toDateTimeString();

        try {
            $result_id = DB::table('class_quizzes')
                ->insertGetId([
                    'cq_quiz_id' => $new_quiz_id,
                    'cq_class_id' => $class_id,
                    'cq_created_on' => $now
                ]);
        } catch (\Exception $e) {
            return ['status' => false, 'message' => $e->getMessage() . ' in file: ' . $e->getFile() . ' at line: ' . $e->getLine()];

        }
        return ['status' => true, 'message' => 'success', 'value' => $result_id];

    }

    public static function CreateClass($class_name, $class_description, $user_id, $class_code): array
    {
        $now = Carbon::now('Europe/Copenhagen')->toDateTimeString();
        $data = [
            'class_name' => $class_name,
            'class_description' => $class_description,
            'class_code' => $class_code,
            'class_created_by' => $user_id,
            'class_created_on' => $now,
        ];
        try {

            $result_id = DB::table('classes')
                ->insertGetId($data);
        } catch (\Exception $e) {
            return ['status' => false, 'message' => $e->getMessage() . ' in file: ' . $e->getFile() . ' at line: ' . $e->getLine()];

        }
        return ['status' => true, 'message' => 'success', 'value' => $result_id];
    }

    public static function AddUserToClass($class_id, $user_id): array
    {
        $data = [
            'cu_class_id' => $class_id,
            'cu_user_id' => $user_id
        ];
        try {

            $result_id = DB::table('class_users')
                ->insertGetId($data);
        } catch (\Exception $e) {
            return ['status' => false, 'message' => $e->getMessage() . ' in file: ' . $e->getFile() . ' at line: ' . $e->getLine()];

        }
        return ['status' => true, 'message' => 'success', 'value' => $result_id];
    }

    public static function RemoveUserFromClass($class_id, $user_id): void
    {
        DB::table('class_users')
            ->where('cu_class_id', '=', $class_id)
            ->where('cu_user_id', '=', $user_id)
            ->delete();

    }

    public static function GetClassInfo($class_id)
    {
        try {
            $result = DB::table('classes')
                ->where('class_id', '=', $class_id)
                ->select('class_name as name', 'class_code as code',
                    'class_description as description', 'class_created_by as user_id',
                    'class_id as id',
                    'class_created_by as created_by')
                ->first();
        } catch (\Exception $e) {

            debug(['message' => $e->getMessage() . ' in file: ' . $e->getFile() . ' at line: ' . $e->getLine()]);
            return null;
        }
        return $result;
    }

    public static function GetClassQuizzesIds($class_id)
    {
        try {

            $result = DB::table('class_quizzes')
                ->where('cq_class_id', '=', $class_id)
                ->select('cq_quiz_id as id')
                ->pluck('id');


        } catch (\Exception $e) {

            debug(['message' => $e->getMessage() . ' in file: ' . $e->getFile() . ' at line: ' . $e->getLine()]);
            return null;
        }
        return $result;
    }

    public static function GetClassQuizzes($class_id)
    {
        try {

            $result = DB::table('class_quizzes')
                ->join('quizzes', 'quiz_id', '=', 'cq_quiz_id')
                ->where('cq_class_id', '=', $class_id)
                ->select('quiz_id as id', 'quiz_title as title', 'quiz_description as description')
                ->get();


        } catch (\Exception $e) {

            debug(['message' => $e->getMessage() . ' in file: ' . $e->getFile() . ' at line: ' . $e->getLine()]);
            return null;
        }
        return $result;
    }

    public static function GetClassParticipantsIds($class_id)
    {
        try {

            $result = DB::table('class_users')
                ->join('users', 'u_id', '=', 'cu_user_id')
                ->where('cu_class_id', '=', $class_id)
                ->where('u_role', '=', 1)
                ->select('cu_user_id as id')
                ->pluck('id');

        } catch (\Exception $e) {

            debug(['message' => $e->getMessage() . ' in file: ' . $e->getFile() . ' at line: ' . $e->getLine()]);
            return null;
        }
        return $result;
    }

    public static function GetClassParticipants($class_id)
    {
        try {

            $result = DB::table('class_users')
                ->join('users', 'u_id', '=', 'cu_user_id')
                ->where('cu_class_id', '=', $class_id)
                ->select('u_id as id', DB::raw('CONCAT(u_first_name, \' \', u_last_name) as name'), 'u_role as role')
                ->orderBy('u_role', 'desc')
                ->orderBy('name', 'asc')
                ->groupBy('u_id')
                ->get();

        } catch (\Exception $e) {

            debug(['message' => $e->getMessage() . ' in file: ' . $e->getFile() . ' at line: ' . $e->getLine()]);
            return null;
        }
        return $result;
    }

    public static function GetClassTeachers($class_id)
    {
        try {

            $result = DB::table('class_users')
                ->join('users', 'u_id', '=', 'cu_user_id')
                ->where('cu_class_id', '=', $class_id)
                ->where('u_role', '=', 2)
                ->select('cu_user_id as id')
                ->pluck('id');


        } catch (\Exception $e) {
            return ['status' => false, 'message' => $e->getMessage() . ' in file: ' . $e->getFile() . ' at line: ' . $e->getLine() . ' \n' . $e->getTraceAsString(), 'result' => []];

        }
        return ['status' => true, 'message' => 'success', 'result' => $result];
    }

    public static function AddClassQuizzes($class_id, $quizzes_ids): ?array
    {
        try {
            $now = Carbon::now('Europe/Copenhagen')->toDateTimeString();
            foreach ($quizzes_ids as $quiz_id) {
                $data_array = [
                    'cq_class_id' => $class_id,
                    'cq_quiz_id' => $quiz_id,
                    'cq_created_on' => $now
                ];
                DB::table('class_quizzes')
                    ->insert($data_array);
            }


        } catch (\Exception $e) {
            return ['status' => false, 'message' => $e->getMessage() . ' in file: ' . $e->getFile() . ' at line: ' . $e->getLine() . ' \n' . $e->getTraceAsString()];
        }
        return ['status' => true, 'message' => 'success'];
    }


    public static function AddClassUsers($class_id, $users_ids): array
    {
        try {
            $now = Carbon::now('Europe/Copenhagen')->toDateTimeString();
            foreach ($users_ids as $user_id) {
                $data_array = [
                    'cu_class_id' => $class_id,
                    'cu_user_id' => $user_id,
                    'cu_created_on' => $now
                ];

                DB::table('class_users')
                    ->insert($data_array);

            }

        } catch (\Exception $e) {
            return ['status' => false, 'message' => $e->getMessage() . ' in file: ' . $e->getFile() . ' at line: ' . $e->getLine() . ' \n' . $e->getTraceAsString()];
        }
        return ['status' => true, 'message' => 'success'];
    }

    public static function VerifyClassCodeUniqueness($code): bool
    {
        $result = DB::table('classes')
            ->where('class_code', '=', $code)
            ->first();

        return $result === null;
    }

    public static function GetClassCode($class_id)
    {
        $result = DB::table('classes')
            ->select('class_code')
            ->where('class_id', '=', $class_id)
            ->pluck('class_code')
            ->first();
        return $result;
    }

    public static function GetAvailableQuizzesForClass($class_id, $user_id)
    {
        $quizzes = DB::table('quizzes')
            ->leftJoin('class_quizzes', static function ($join) use ($class_id) {
                $join->on('quiz_id', '=', 'cq_quiz_id');
                $join->on('cq_class_id', '=', DB::raw($class_id));

            })
            ->where('quiz_created_by', '=', $user_id)
            ->whereNull('cq_class_id')
            ->select('quiz_id as id',
                'quiz_title as title',
                'quiz_description as description')
            ->orderBy('quiz_created_on')
            ->get();
        return $quizzes;
    }

    public static function GetParticipantClasses($user_id): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        $result = DB::table('classes')
            ->where('cu_user_id', '=', $user_id)
            ->join('class_users', 'cu_class_id', '=', 'class_id')
            ->select('class_id as id', 'class_name as name', 'class_description as description')
            ->paginate(10);

        foreach ($result as &$class) {
            $class->quizzes = DB::table('class_quizzes')
                ->select(DB::raw('COUNT(DISTINCT cq_quiz_id) as quizzes'))
                ->where('cq_class_id', '=', $class->id)
                ->pluck('quizzes')
                ->first();
        }

        return $result;
    }

    public static function GetAllParticipantClasses($user_id)
    {
        $result = DB::table('classes')
            ->where('cu_user_id', '=', $user_id)
            ->join('class_users', 'cu_class_id', '=', 'class_id')
            ->select('class_id as id', 'class_name as name')
            ->get();

        return $result;
    }

    public static function GetClassByCode($class_code)
    {
        $result = DB::table('classes')
            ->where('class_code', '=', trim($class_code))
            ->select('class_id as id')
            ->first();
        return $result;
    }

    public static function GetClassBySession($session_id)
    {
        $result = DB::table('quiz_session')
            ->join('quizzes', 'quiz_id', '=', 'qs_quiz_id')
            ->leftJoin('class_quizzes', 'quiz_id', '=', 'cq_quiz_id')
            ->leftJoin('classes', 'class_id', '=', 'cq_class_id')
            ->where('qs_id', '=', $session_id)
            ->select('class_name', 'qs_started_at as started_at', 'quiz_title')
            ->first();
        return $result;
    }

    public static function GetClassByQuiz($quiz_id)
    {
        $result = DB::table('class_quizzes')
            ->where('cq_quiz_id', '=', $quiz_id)
            ->select('cq_class_id')
            ->pluck('cq_class_id')
            ->first();
        return $result;
    }

    public static function UpdateClassInfo($class_id, $class_name, $class_desc): array
    {
        $update_array = ['class_name' => $class_name];
        if ($class_desc !== null) {
            $update_array['class_description'] = $class_desc;
        }
        try {

            DB::table('classes')
                ->where('class_id', '=', $class_id)
                ->update($update_array);

        } catch (\Exception $e) {
            return ['status' => false, 'message' => $e->getMessage() . ' in file: ' . $e->getFile() . ' at line: ' . $e->getLine() . ' \n' . $e->getTraceAsString()];
        }
        return ['status' => true, 'message' => 'sucess'];
    }

    public static function AddParticipantInvite($class_id, $token): ?array
    {
        try {

            $result_id = DB::table('class_invites')
                ->insertGetId([
                    'ci_class_id' => $class_id,
                    'ci_token' => $token,
                    'ci_date' => Carbon::now('Europe/Copenhagen')->toDateTimeString()
                ]);
            return ['status' => true, 'value' => $result_id];

        } catch (\Exception $e) {
            return ['status' => false, 'message' => $e->getMessage() . ' in file: ' . $e->getFile() . ' at line: ' . $e->getLine() . ' \n' . $e->getTraceAsString()];
        }
    }

    public static function RemoveParticipantInvite($token): bool
    {
        DB::table('class_invites')
            ->where('ci_token', '=', $token)
            ->delete();
        return true;
    }

    public static function UpdateParticipantInvite($invite_id): void
    {
        DB::table('class_invites')
            ->where('ci_id', '=', $invite_id)
            ->update([
                'ci_sent' => 1
            ]);
    }

    public static function GetClassIdByToken($token)
    {
        $class_id = DB::table('class_invites')
            ->where('ci_token', '=', $token)
            ->select('ci_class_id as class_id')
            ->pluck('class_id')->first();
        return $class_id;
    }

    public static function CheckParticipantClassEnrollment($class_id, $user_id)
    {
        $result = DB::table('class_users')
            ->where('cu_class_id', '=', $class_id)
            ->where('cu_user_id', '=', $user_id)
            ->select('cu_id')
            ->pluck('cu_id')
            ->first();

        return $result !== null;
    }

    public static function DeleteClass($class_id)
    {
        $class = DB::table('classes')
            ->where('class_id', '=', $class_id)
            ->first();
        if ($class !== null) {
            DB::table('classes')
                ->where('class_id', '=', $class_id)
                ->delete();
            return ['status' => true, 'message' => 'success'];
        }
        return ['status' => false, 'message' => 'not found'];

    }

    public static function DeleteClassUsersLinks($class_id)
    {
        DB::table('class_users')
            ->where('cu_class_id', '=', $class_id)
            ->delete();
    }

    public static function DeleteClassInvites($class_id)
    {
        DB::table('class_invites')
            ->where('ci_class_id', '=', $class_id)
            ->delete();
    }

    public static function GetClassAuthor($class_id)
    {
        $result = DB::table('classes')
            ->where('class_id','=',$class_id)
            ->select('class_created_by as author')
            ->pluck('author')
            ->first();
        return $result;
    }

}
