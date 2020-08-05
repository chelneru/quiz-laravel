<?php


namespace App\Repositories\Admin;


use App\Repositories\User\UserRepository;
use Carbon\Carbon;
use DB;
use Illuminate\Pagination\LengthAwarePaginator;
use Request as Request;
use Config;
use Symfony\Component\HttpKernel\Event\RequestEvent;


class AdminRepository
{
    public static function GetUsersForAdminUsersPage($role_filter, $order_by_filter, $order_dir_filter, $user_filter)
    {
        $users = DB::table('users');

        if ($role_filter !== null && ($role_filter == 1 || $role_filter == 2)) {
            $users = $users
                ->where('u_role', '=', $role_filter);
        }
        if ($user_filter !== null) {
            $user_filter = strtolower($user_filter);
            $users = $users
                ->whereRaw('LOWER(CONCAT(u_first_name, \' \', u_last_name)) like (?)', ["%{$user_filter}%"])
                ->orWhereRaw('LOWER(email) like (?)', ["%{$user_filter}%"]);
        }
        $users = $users->select('u_id as id', 'u_role as role', 'u_active as active',
            DB::raw('LOWER(CONCAT(u_first_name, \' \', u_last_name)) as name'),
            DB::RAW('LOWER(email) as email'), 'u_last_login as last_login', 'created_at'
        );
        $users = $users
            ->groupBy('u_id')->get();

        foreach ($users as $participant) {
            $activity = DB::table('class_users')
                ->leftJoin('class_quizzes', 'cu_class_id', '=', 'cq_class_id')
                ->select('cu_id as id', DB::raw('COUNT(cq_id) as quiz_count'))
                ->groupBy('cu_class_id')
                ->where('cu_user_id', '=', $participant->id)->get();
            $participant->class_count = $activity->count();
            $quiz_count = 0;
            foreach ($activity as $class_activity) {
                $quiz_count += $class_activity->quiz_count;
            }
            $participant->quiz_count = $quiz_count;
        }
        //apply ordering and filtering

        $order_dir = null;
        $order_by = null;
        if ($order_dir_filter == 'asc' || $order_dir_filter == 'desc') {
            $order_dir = $order_dir_filter;
        }
        if (count($users) > 0 && property_exists($users[0], $order_by_filter)) {
            $order_by = $order_by_filter;
        }
        if ($order_dir !== null && $order_by !== null) {
            if ($order_dir == 'asc') {
                $users = $users
                    ->sortBy($order_by, SORT_NATURAL);
            } else {
                $users = $users
                    ->sortByDesc($order_by, SORT_NATURAL);
            }
        }

        $totalItems = $users->count();
        $page = Request::get('page', 1);
        $perPage = 10;
        $offset = ($page * $perPage) - $perPage;
        //Instead of just using ->paginate(10) we have to create a paginator as the query from the function paginate() does not accept HAVING command.
        return new LengthAwarePaginator(array_slice($users->all(), $offset, $perPage, true), count($users), $perPage, $page, ['path' => Request::url(), 'query' => Request::query()]);

    }

    public static function GetUsersDropdownInfo()
    {
        return DB::table('users')
            ->select(DB::raw('CONCAT(u_first_name, \' \', u_last_name) as name'), 'email')->get();
    }

    public static function GetClassesForAdminClassesPage($order_by_filter, $order_dir_filter, $user_filter)
    {
        $classes = DB::table('classes');

        $classes = $classes->select('class_id as id',
            DB::raw('LOWER(class_name) as name'), DB::raw('MAX(cu_created_on) as last_enrollment'), DB::raw('CONCAT(u_first_name, \' \', u_last_name) as author'), 'u_id as author_id'
        )
            ->leftJoin('class_users', 'class_id', '=', 'cu_class_id')
            ->leftJoin('users', 'u_id', '=', 'class_created_by')
            ->groupBy('class_id');

        //apply ordering and filtering

        $order_dir = null;
        $order_by = null;
        if ($order_dir_filter == 'asc' || $order_dir_filter == 'desc') {
            $order_dir = $order_dir_filter;
        }
        if ($user_filter !== null) {
            $user_filter = strtolower($user_filter);
            $classes = $classes
                ->whereRaw('LOWER(CONCAT(u_first_name, \' \', u_last_name)) like (?)', ["%{$user_filter}%"])
                ->orWhereRaw('LOWER(email) like (?)', ["%{$user_filter}%"]);
        }
        $classes = $classes->get();
        if (count($classes) > 0 && property_exists($classes[0], $order_by_filter)) {
            $order_by = $order_by_filter;
        }
        if ($order_dir !== null && $order_by !== null) {
            if ($order_dir == 'asc') {
                $classes = $classes
                    ->sortBy($order_by, SORT_NATURAL);
            } else {
                $classes = $classes
                    ->sortByDesc($order_by, SORT_NATURAL);
            }
        }

        $totalItems = $classes->count();
        $page = Request::get('page', 1);
        $perPage = 10;
        $offset = ($page * $perPage) - $perPage;
        //Instead of just using ->paginate(10) we have to create a paginator as the query from the function paginate() does not accept HAVING command.
        return new LengthAwarePaginator(array_slice($classes->all(), $offset, $perPage, true), count($classes), $perPage, $page, ['path' => Request::url(), 'query' => Request::query()]);

    }

    public static function AddAdmin($user_id)
    {
        try {
            DB::table('users_admins')
                ->insert(['ua_u_id' => $user_id,
                    'ua_super_admin' => 0,
                    'ua_created_at' => Carbon::now('Europe/Copenhagen')->toDateTimeString()]);

        } catch (\Exception $e) {
            return ['status' => false, 'message' => $e->getMessage() . ' in file: ' . $e->getFile() . ' at line: ' . $e->getLine()];

        }
        return ['status' => true, 'message' => 'success'];
    }

    public static function RemoveAdmin($user_id)
    {
        try {
            DB::table('users_admins')
                ->where('ua_u_id', 'u', $user_id)
                ->delete();
        } catch (\Exception $e) {
            return ['status' => false, 'message' => $e->getMessage() . ' in file: ' . $e->getFile() . ' at line: ' . $e->getLine()];
        }
        return ['status' => true, 'message' => 'success'];
    }

    public static function UpdateUser($id, $email, $first_name, $last_name, $role_id, $department, $position)
    {
        try {

            $update_array = [
                'email' => $email,
                'u_first_name' => $first_name,
                'u_last_name' => $last_name,
                'u_role' => $role_id
            ];

            $existing_role = UserRepository::GetUserRole($id);
            if ($existing_role == 3 && $role_id < 3) {
                // remove admin rights
                self::RemoveAdmin($id);
            } else if ($existing_role < 3 && $role_id == 3) {
                //add admin rights
                self::AddAdmin($id);
            }
            if ($role_id == Config::get('defines.TEACHER_ROLE')) {
                $update_array['u_department'] = $department;
                $update_array['u_position'] = $position;
            }

            DB::table('users')
                ->where('u_id', '=', $id)
                ->update($update_array);
        } catch (\Exception $e) {
            return ['status' => false, 'message' => $e->getMessage() . ' in file: ' . $e->getFile() . ' at line: ' . $e->getLine()];

        }
        return ['status' => true, 'message' => 'success'];
    }

    public static function GetUserViewInfo($user_id)
    {
        $result = DB::table('users')
            ->where('u_id', '=', $user_id)
            ->select('u_id as id', 'u_first_name as first_name', 'u_last_name as last_name',
                'email', 'u_role as role')
            ->first();
        if ($result !== null) {
            $result->classes = DB::table('classes')
                ->join('class_users', 'cu_class_id', '=', 'class_id')
                ->where('cu_user_id', '=', $user_id)
                ->select('class_id as id', 'class_name as name')
                ->groupBy('class_id')
                ->orderBy('class_name')
                ->get();
            $class_ids = $result->classes->pluck('id');
            foreach ($result->classes as $class) {
                $class->participants_count = DB::table('class_users')
                    ->where('cu_class_id', '=', $class->id)
                    ->select('cu_class_id', DB::raw('COUNT(cu_id) as participants_count'))
                    ->groupBy('cu_class_id')
                    ->pluck('participants_count')
                    ->first();
                $class->quizzes_count = DB::table('class_quizzes')
                    ->where('cq_class_id', '=', $class->id)
                    ->select('cq_class_id',
                        DB::raw('COUNT(cq_id) as quizzes_count'))
                    ->groupBy('cq_class_id')
                    ->pluck('quizzes_count')
                    ->first();

            }
            $result->quizzes = DB::table('quizzes')
                ->leftJoin('class_quizzes', 'cq_quiz_id', '=', 'quiz_id')
                ->leftJoin('classes', 'class_id', '=', 'cq_class_id')
                ->join('quiz_questions', 'qq_quiz_id', '=', 'quiz_id')
                ->whereIn('cq_class_id', $class_ids)
                ->select(DB::raw('COUNT(qq_id) as questions_count'),
                    'quiz_title as name', 'class_name', 'quiz_id as id', 'class_id')
                ->groupBy('id')
                ->orderBy('class_name')
                ->get();
            foreach ($result->quizzes as $quiz) {
                $quiz->sessions_count = DB::table('quiz_session')
                    ->where('qs_quiz_id', '=', $quiz->id)
                    ->select(DB::raw('COUNT(qs_id) as sessions_count'))
                    ->pluck('sessions_count')
                    ->first();
            }
        }
        return $result;
    }

    public static function GetQuizzesForAdminQuizzesPage($order_by_filter, $order_dir_filter, $user_filter)
    {
        $quizzes = DB::table('quizzes');
        $sessions = DB::table('quiz_session')
            ->select('qs_id', 'qs_quiz_id')
            ->whereNull('qs_stopped_at')
            ->orderBy('qs_id', 'desc');
        $quizzes = $quizzes->select('quiz_id as id',
            DB::raw('LOWER(quiz_title) as name'),
            DB::raw('CONCAT(u_author.u_first_name, \' \', u_author.u_last_name) as author'),
            DB::raw('u_author.u_id as author_id'), DB::raw('IF(`qs_id` IS NOT NULL, `qs_id`, 0)as session_id'))
            ->leftJoin('users as u_author', DB::raw('u_author.u_id'), '=', 'quiz_created_by')
            ->leftJoinSub($sessions, 'sessions', 'qs_quiz_id', '=', 'quiz_id')
            ->groupBy('quiz_id');

        //apply ordering and filtering

        $order_dir = null;
        $order_by = null;
        if ($order_dir_filter == 'asc' || $order_dir_filter == 'desc') {
            $order_dir = $order_dir_filter;
        }
        if ($user_filter !== null) {
            $user_filter = strtolower($user_filter);
            $quizzes = $quizzes
                ->leftJoin('users as u', DB::raw('u.u_id'), '=', 'quiz_created_by')
                ->whereRaw('LOWER(CONCAT(u.u_first_name, \' \', u.u_last_name)) like (?)', ["%{$user_filter}%"])
                ->orWhereRaw('LOWER(u.email) like (?)', ["%{$user_filter}%"]);
        }
        $quizzes = $quizzes->get();
        if (count($quizzes) > 0 && property_exists($quizzes[0], $order_by_filter)) {
            $order_by = $order_by_filter;
        }
        //
        if ($order_by_filter == 'status') {
            if (count($quizzes) > 0 && property_exists($quizzes[0], 'session_id')) {
                $order_by = $order_by_filter;
            }
        }
        if ($order_dir !== null && $order_by !== null) {

            if ($order_by !== 'status') {
                if ($order_dir == 'asc') {
                    $quizzes = $quizzes
                        ->sortBy($order_by, SORT_NATURAL);
                } else {
                    $quizzes = $quizzes
                        ->sortByDesc($order_by, SORT_NATURAL);
                }
            } else {

                if ($order_dir == 'asc') {
                    $quizzes = $quizzes->sortBy(function ($quiz) {
                        return [
                            $quiz->session_id,
                            $quiz->name
                        ];
                    });
                } else {
                    $quizzes = $quizzes->sortBy(function ($quiz) {
                        return [
                            -$quiz->session_id,
                            $quiz->name
                        ];
                    });
                }
            }
        }

        $totalItems = $quizzes->count();
        $page = Request::get('page', 1);
        $perPage = 10;
        $offset = ($page * $perPage) - $perPage;
        //Instead of just using ->paginate(10) we have to create a paginator as the query from the function paginate() does not accept HAVING command.
        return new LengthAwarePaginator(array_slice($quizzes->all(), $offset, $perPage, true), count($quizzes), $perPage, $page, ['path' => Request::url(), 'query' => Request::query()]);


    }

    public static function GetTeachersForValidationPage()
    {
        $result = DB::table('users')
            ->where('u_role', '=', 2)
            ->select('u_id as id', 'email', 'u_first_name as first_name', 'u_last_name as last_name', 'created_at', 'u_last_login as last_login','u_active as status')
            ->orderBy('u_active','asc')
            ->orderBy('created_at','desc')
            ->get();
        return $result;
    }

    public static function MakeTeacherActive($teacher_id)
    {
        try {
            DB::table('users')
                ->where('u_id', '=', $teacher_id)
                ->update([
                    'u_active' => 1
                ]);

            return ['status' => true, 'message' => 'success'];
        } catch (\Exception $e) {
            return ['status' => false, 'message' => $e->getMessage() . ' in file: ' . $e->getFile() . ' at line: ' . $e->getLine()];

        }
    }
}
