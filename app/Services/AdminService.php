<?php


namespace App\Services;



use App\Repositories\Admin\AdminRepository;
use App\Repositories\User\UserRepository;
use App\User;
use Config;
use Hash;
use Illuminate\Support\Str;

class AdminService
{

    public static function GetUsersForAdminUsersPage( $role_filter,  $order_by_filter, $order_dir_filter,$user_filter)
    {
        return AdminRepository::GetUsersForAdminUsersPage($role_filter,$order_by_filter,$order_dir_filter,$user_filter);
    }

    public static function GetUsersDropdownInfo()
    {
        return AdminRepository::GetUsersDropdownInfo();
    }
    public static function GetClassesForAdminClassesPage($order_by_filter, $order_dir_filter,$user_filter)
    {
        return AdminRepository::GetClassesForAdminClassesPage($order_by_filter, $order_dir_filter,$user_filter);
    }

    public static function UpdateUser($id, $email, $first_name, $last_name, $role_id, $department, $position)
    {
        return AdminRepository::UpdateUser($id, $email, $first_name, $last_name, $role_id, $department, $position);
    }

    public static function CreateUser($email, $first_name, $last_name, $role, $department, $position)
    {
        try {
            $user = new User();
            $user->password = Hash::make(Str::random(16));
            $user->name = 'username';
            $user->email = $email;
            $user->u_first_name = $first_name;
            $user->u_last_name = $last_name;
            $user->u_role = $role;
            $user->u_department = $department;
            $user->u_position = $position;
            $user->save();

            $token = app('auth.password.broker')->createToken($user);
            $link = route('password.reset', ['token' => $token,'new_account'=>true]);
            MailService::SendNewUserMail($email,$first_name,$last_name,$link);
        } catch (\Exception $e) {
            return ['status' => false, 'message' => $e->getMessage() . ' in file: ' . $e->getFile() . ' at line: ' . $e->getLine()];

        }
        return ['status' => true, 'message' => 'success','id'=>$user->id];
    }

    public static function ValidateUserInfo($email, $first_name, $last_name, $role)
    {
        //validate email
        if ($email != null && strlen(trim($email)) > 0) {
            $email = filter_var($email, FILTER_SANITIZE_EMAIL);
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                return false;
            }
        } else {
            return false;
        }

        if($first_name == null || strlen(trim($first_name)) < 1) {
            return false;
        }
        if($last_name == null || strlen(trim($last_name)) < 1) {
            return false;
        }
        if($role != Config::get('defines.PARTICIPANT_ROLE') && $role != Config::get('defines.TEACHER_ROLE') &&
            $role != Config::get('defines.ADMINISTRATOR_ROLE')) {
            return false;
        }
        return true;
    }

    public static function DeleteUser($user_id)
    {
        $user = User::where('u_id','=',$user_id)->first();
        if($user !== null) {
            return UserRepository::DeleteUserInfo($user_id);
        }
        return ['status'=>false,'message'=>'user not found'];
    }

    public static function GetUserViewInfo($user_id)
    {
        $result = AdminRepository::GetUserViewInfo($user_id);
        // group quizzes by class
        foreach ($result->classes as &$class) {
            $class->quizzes = [];
            foreach ($result->quizzes as $quiz) {
                if ($quiz->class_id == $class->id) {
                    $class->quizzes[] = $quiz;
                }
            }
        }

        unset($class);
        return $result;
    }

    public static function GetQuizzesForAdminQuizzesPage($order_by_filter, $order_dir_filter, $user_filter)
    {
        return AdminRepository::GetQuizzesForAdminQuizzesPage($order_by_filter, $order_dir_filter, $user_filter);
    }

    public static function GetTeachersForValidationPage()
    {
        return AdminRepository::GetTeachersForValidationPage();
    }

    public static function MakeTeacherActive($teacher_id)
    {
        return AdminRepository::MakeTeacherActive($teacher_id);

    }

}
