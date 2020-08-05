<?php

namespace App\Http\Controllers;

use App\Services\AdminService;
use App\Services\ClassService;
use App\Services\MailService;
use App\Services\QuizService;

use Illuminate\Contracts\View\Factory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\View\View;
use Redirect;
use stdClass;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('admin-access');
    }


    /**
     * This is the Users page for Admins.
     * @param Request $request
     * @return Factory|View
     */
    public function Users(Request $request)
    {

        $role_filter = null;
        $order_by_filter = null;
        $order_dir_filter = null;
        $user_filter = null;

        if ($request->has('role_filter')) {
            $role_filter = $request->input('role_filter');
        }
        if ($request->has('order_by_filter')) {
            $order_by_filter = $request->input('order_by_filter');
        }
        if ($request->has('order_dir_filter')) {
            $order_dir_filter = $request->input('order_dir_filter');
        }
        if ($request->has('user_filter')) {
            $user_filter = $request->input('user_filter');
        }

        $users = AdminService::GetUsersForAdminUsersPage($role_filter, $order_by_filter, $order_dir_filter, $user_filter);
        $users_dropdown_info = AdminService::GetUsersDropdownInfo();
        $users_names = $users_dropdown_info->pluck('name');
        $users_emails = $users_dropdown_info->pluck('email');

        $users_autocomplete = array_merge($users_names->toArray(), $users_emails->toArray());

        if ($role_filter !== null) {
            $users->appends(['role_filter' => $role_filter]);
        }
        if ($order_by_filter !== null) {
            $users->appends(['order_by_filter' => $order_by_filter]);
        }
        if ($order_dir_filter !== null) {
            $users->appends(['order_dir_filter' => $order_dir_filter]);
        }
        if ($order_dir_filter !== null) {
            $users->appends(['user_filter' => $user_filter]);
        }
        return view('admin.users', [
            'users' => $users,
            'role_filter' => $role_filter,
            'order_by_filter' => $order_by_filter,
            'order_dir_filter' => $order_dir_filter,
            'user_filter' => $user_filter,
            'users_autocomplete' => $users_autocomplete
        ]);

    }

    /**
     *This is the route for the page where
     * admins can either edit or create a new user.
     *
     * @param Request $request
     * @param null $user_id
     * @return Factory|RedirectResponse|View
     */
    public function ManageUserPage(Request $request, $user_id = null)
    {
        if ($user_id !== null) {
            $user_info = UserController::GetUserInfo($user_id);
            if ($user_info === NULL) {
                $request->session()->flash('status', 'fail');
                $request->session()->flash('message', 'User not found.');
                return Redirect::route('admin-users');
            }
        } else {
            $user_info = new stdClass();
            $user_info->first_name = '';
            $user_info->last_name = '';
            $user_info->email = '';
            $user_info->id = null;
            $user_info->department = '';
            $user_info->position = '';
            $user_info->role = 1;

        }
        return view('admin.manage-user', ['user_info' => $user_info]);
    }

    /**
     *
     * POST request to apply changes made from the ManageUsers page
     * @param Request $request
     * @return RedirectResponse
     */
    public function ManageUserAction(Request $request)
    {
        if (AdminService::ValidateUserInfo($request->input('email'),
            $request->input('first_name'), $request->input('last_name'), $request->input('role'))) {
            if ($request->has('id') && $request->input('id') !== null) {
                $result = AdminService::UpdateUser($request->input('id'), $request->input('email'),
                    $request->input('first_name'), $request->input('last_name'), $request->input('role'),
                    $request->input('department'), $request->input('position'));
                if ($result['status'] == true) {
                    $request->session()->flash('status', 'success');
                    $request->session()->flash('message', 'The user has been updated successfully.');
                } else {
                    $request->session()->flash('status', 'fail');
                    $request->session()->flash('message', 'There has been an error in updating the user.');
                }
                return redirect()->route('admin-manage-user', ['user_id' => $request->input('id')]);
            } else {
                $result = AdminService::CreateUser($request->input('email'), $request->input('first_name'),
                    $request->input('last_name'), $request->input('role'), $request->input('department'), $request->input('position'));
                if ($result['status'] == true) {
                    $request->session()->flash('status', 'success');
                    $request->session()->flash('message', 'The user has been created successfully.');
                    return redirect()->route('admin-manage-user', ['user_id' => $result['id']]);

                } else {
                    $request->session()->flash('status', 'fail');
                    $request->session()->flash('message', 'There has been an error in creating the user.');
                    return Redirect::route('admin-users');

                }
            }
        } else {
            $request->session()->flash('status', 'fail');
            $request->session()->flash('message', 'Invalid request. Missing parameters.');
            return Redirect::route('admin-users');
        }
    }

    /**
     * POST Request to reset an user's password. This is done on the admin Users page by admins.
     * @param Request $request
     * @return RedirectResponse|Redirector|string
     */
    public function ResetUserPassword(Request $request)
    {
        if ($request->has('email')) {
            if ($request->input('email') != null && strlen(trim($request->input('email'))) > 0) {
                $email = filter_var($request->input('email'), FILTER_SANITIZE_EMAIL);
                if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $result = MailService::SendResetPasswordMail($email);

                    if ($result['status'] == true) {
                        $request->session()->flash('status', 'success');
                        $request->session()->flash('message', 'The password has been reset successfully.');
                    } else {
                        $request->session()->flash('status', 'fail');
                        $request->session()->flash('message', 'There has been an error in resetting the password.');
                    }
                    return redirect(url()->previous());
                }
            }
        }
        $request->session()->flash('status', 'fail');
        $request->session()->flash('message', 'The user has an invalid e-mail address.');
        return redirect()->getUrlGenerator()->previous();

    }

    /**
     * POST Request to delete an user.This action can be done from admin users page.
     *
     * @param Request $request
     * @return RedirectResponse|Redirector
     */
    public function DeleteUser(Request $request)
    {
        if ($request->has('user_id') && $request->input('user_id') !== null) {
            $result = AdminService::DeleteUser($request->input('user_id'));
            if ($result['status'] == true) {
                $request->session()->flash('status', 'success');
                $request->session()->flash('message', 'The user has been deleted successfully.');
            } else {
                $request->session()->flash('status', 'fail');
                $request->session()->flash('message', 'There has been an error in deleting the user.');
            }
            return redirect(url()->previous());
        } else {
            $request->session()->flash('status', 'fail');
            $request->session()->flash('message', 'Missing parameters');
            return redirect(url()->previous());

        }
    }

    /**
     *
     * This page can be accessed from the admin users page.
     * @param Request $request
     * @param $user_id
     * @return Factory|RedirectResponse|Redirector|View
     */
    public function UserView(Request $request, $user_id)
    {
        if ($user_id !== null) {
            $user_info = AdminService::GetUserViewInfo($user_id);
            return view('admin.admin-user-view', ['user' => $user_info]);
        } else {
            $request->session()->flash('status', 'fail');
            $request->session()->flash('message', 'No user id provided.');
            return redirect(url()->previous());

        }
    }

    /**
     * This page is the admin classes page.
     * Here admins can have an overview of all existing classes.
     * @param Request $request
     * @return Factory|View
     */
    public function Classes(Request $request)
    {
        $order_by_filter = null;
        $order_dir_filter = null;
        $user_filter = null;


        if ($request->has('order_by_filter')) {
            $order_by_filter = $request->input('order_by_filter');
        }
        if ($request->has('order_dir_filter')) {
            $order_dir_filter = $request->input('order_dir_filter');
        }
        if ($request->has('user_filter')) {
            $user_filter = $request->input('user_filter');
        }

        $classes = AdminService::GetClassesForAdminClassesPage($order_by_filter, $order_dir_filter, $user_filter);
        $users_dropdown_info = AdminService::GetUsersDropdownInfo();
        $users_names = $users_dropdown_info->pluck('name');
        $users_emails = $users_dropdown_info->pluck('email');

        $users_autocomplete = array_merge($users_names->toArray(), $users_emails->toArray());

        if ($order_by_filter !== null) {
            $classes->appends(['order_by_filter' => $order_by_filter]);
        }
        if ($order_dir_filter !== null) {
            $classes->appends(['order_dir_filter' => $order_dir_filter]);
        }
        if ($order_dir_filter !== null) {
            $classes->appends(['user_filter' => $user_filter]);
        }

        return view('admin.classes', [
            'classes' => $classes,
            'order_by_filter' => $order_by_filter,
            'order_dir_filter' => $order_dir_filter,
            'user_filter' => $user_filter,
            'users_autocomplete' => $users_autocomplete
        ]);

    }

    /**
     * Edit class page for admins.It is accesssed from the Admin classes pages.
     * @param Request $request
     * @param $class_id
     * @return Factory|RedirectResponse|View
     */
    public function AdminEditClassPage(Request $request, $class_id)
    {
        if ($class_id !== null) {
            $class_info = ClassService::GetClassInfo($class_id);
            if ($class_info === NULL) {
                $request->session()->flash('status', 'fail');
                $request->session()->flash('message', 'Class not found.');
                return Redirect::route('admin-classes');
            } else {
                return view('admin.admin-edit-class', ['class_info' => $class_info]);
            }
        } else {
            $request->session()->flash('status', 'fail');
            $request->session()->flash('message', 'Missing class id.');
            return Redirect::route('admin-classes');

        }
    }

    /**
     * POST Request to apply changes from the Edit Class page for the admins.
     * @param Request $request
     * @return RedirectResponse|Redirector
     */
    public function AdminEditClassAction(Request $request)
    {
        if ($request->has('id') && $request->has('name')) {
            $result = ClassService::UpdateClassInfo($request->input('id'), $request->input('name'));
            if ($result['status'] === true) {
                $request->session()->flash('status', 'success');
                $request->session()->flash('message', 'The class has been updated successfully.');
                return Redirect::route('admin-classes');
            } else {
                $request->session()->flash('status', 'fail');
                $request->session()->flash('message', 'There has been an error in updating the class.');
                return redirect(url()->previous());
            }
        }
        $request->session()->flash('status', 'fail');
        $request->session()->flash('message', 'Missing parameters');
        return redirect(url()->previous());
    }

    /**
     * POST Request for action to delete a class. This action is triggered from the Admin classes page.
     * @param Request $request
     * @return RedirectResponse|Redirector
     */
    public function AdminDeleteClassAction(Request $request)
    {
        if ($request->has('class_id') && $request->input('class_id') !== null) {
            $result = ClassService::DeleteClass($request->input('class_id'));
            if ($result['status'] == true) {
                $request->session()->flash('status', 'success');
                $request->session()->flash('message', 'The class has been deleted successfully.');
            } else {
                $request->session()->flash('status', 'fail');
                $request->session()->flash('message', 'There has been an error in deleting the class.');
            }
            return redirect(url()->previous());
        } else {
            $request->session()->flash('status', 'fail');
            $request->session()->flash('message', 'Missing parameters');
            return redirect(url()->previous());

        }
    }

    /**
     * This is the Admin Quizzes page.
     * @param Request $request
     * @return Factory|View
     */
    public function Quizzes(Request $request)
    {
        $role_filter = null;
        $order_by_filter = null;
        $order_dir_filter = null;
        $user_filter = null;

        if ($request->has('role_filter')) {
            $role_filter = $request->input('role_filter');
        }
        if ($request->has('order_by_filter')) {
            $order_by_filter = $request->input('order_by_filter');
        }
        if ($request->has('order_dir_filter')) {
            $order_dir_filter = $request->input('order_dir_filter');
        }
        if ($request->has('user_filter')) {
            $user_filter = $request->input('user_filter');
        }

        $quizzes = AdminService::GetQuizzesForAdminQuizzesPage($order_by_filter, $order_dir_filter, $user_filter);
        $users_dropdown_info = AdminService::GetUsersDropdownInfo();
        $users_names = $users_dropdown_info->pluck('name');
        $users_emails = $users_dropdown_info->pluck('email');

        $users_autocomplete = array_merge($users_names->toArray(), $users_emails->toArray());

        if ($order_by_filter !== null) {
            $quizzes->appends(['order_by_filter' => $order_by_filter]);
        }
        if ($order_dir_filter !== null) {
            $quizzes->appends(['order_dir_filter' => $order_dir_filter]);
        }
        if ($order_dir_filter !== null) {
            $quizzes->appends(['user_filter' => $user_filter]);
        }
        return view('admin.quizzes', [
            'quizzes' => $quizzes,
            'role_filter' => $role_filter,
            'order_by_filter' => $order_by_filter,
            'order_dir_filter' => $order_dir_filter,
            'user_filter' => $user_filter,
            'users_autocomplete' => $users_autocomplete
        ]);
    }

    /**
     * This is the Quiz Edit page for admins.Accessed from the Admin quizzes page.
     * @param Request $request
     * @param $quiz_id
     * @return Factory|RedirectResponse|View
     */
    public function AdminEditQuizPage(Request $request, $quiz_id)
    {
        if ($quiz_id !== null) {
            $quiz_info = QuizService::GetQuizInfo($quiz_id);
            if ($quiz_info === NULL) {
                $request->session()->flash('status', 'fail');
                $request->session()->flash('message', 'Quiz not found.');
                return Redirect::route('admin-quizzes');
            } else {
                return view('admin.admin-edit-quiz', ['quiz_info' => $quiz_info]);
            }
        } else {
            $request->session()->flash('status', 'fail');
            $request->session()->flash('message', 'Missing quiz id.');
            return Redirect::route('admin-quizzes');

        }
    }

    /**
     * POST Request to apply changes for a quiz. It's accessed from Edit Quiz page for admins.
     * @param Request $request
     * @return RedirectResponse|Redirector
     */
    public function AdminEditQuizAction(Request $request)
    {
        if ($request->has('id') && $request->has('name')) {
            $result = QuizService::UpdateQuizTitle($request->input('id'), $request->input('name'));
            if ($result['status'] === true) {
                $request->session()->flash('status', 'success');
                $request->session()->flash('message', 'The quiz has been updated successfully.');
                return Redirect::route('admin-quizzes');
            } else {
                $request->session()->flash('status', 'fail');
                $request->session()->flash('message', 'There has been an error in updating the quiz.');
                return redirect(url()->previous());
            }
        }
        $request->session()->flash('status', 'fail');
        $request->session()->flash('message', 'Missing parameters');
        return redirect(url()->previous());
    }

    /**
     * POST Request to delete a quiz. This action is only accessible for admins from the Admin Quizzes page.
     * @param Request $request
     * @return RedirectResponse|Redirector
     */
    public function AdminDeleteQuizAction(Request $request)
    {
        if ($request->has('quiz_id') && $request->input('quiz_id') !== null) {
            $result = QuizService::DeleteQuiz($request->input('quiz_id'), false);
            if ($result['status'] == true) {
                $request->session()->flash('status', 'success');
                $request->session()->flash('message', 'The quiz has been deleted successfully.');
            } else {
                $request->session()->flash('status', 'fail');
                $request->session()->flash('message', 'There has been an error in deleting the quiz.');
            }
            return redirect(url()->previous());
        } else {
            $request->session()->flash('status', 'fail');
            $request->session()->flash('message', 'Missing parameters');
            return redirect(url()->previous());

        }
    }
    public function ValidateTeachersPage(Request $request) {

        $inactive_teachers = AdminService::GetTeachersForValidationPage();
        return view('admin.validate-teachers',['teachers'=>$inactive_teachers]);
    }
    public function ValidateTeachersAction(Request $request) {
        if($request->has('user_id')) {
            $teacher_id = $request->input('user_id');
            $result = AdminService::MakeTeacherActive($teacher_id);
            if ($result['status']) {
                return json_encode(['status' => $result['status'], 'message' => 'success']);
            } else {
                return json_encode(['status' => $result['status'], 'message' => $result['message']]);
            }
        } else {
            return json_encode(['status'=>false,'message'=>'invalid params']);

        }
    }

}
