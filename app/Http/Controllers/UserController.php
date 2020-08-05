<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateUserRequest;
use App\Repositories\User\UserRepository;
use App\Services\ClassService;
use App\Services\LeaderboardService;
use App\Services\QuizService;
use App\Services\UserService;
use App\User;
use Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class UserController extends Controller
{
    public static function GetUserInfo($user_id)
    {
        return UserRepository::GetUserInfo($user_id);
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */


    public function index(Request $request)
    {
        $class_filter = null;
        $role_filter = null;
        $order_by_filter = null;
        $order_dir_filter = null;
        $participant_filter = null;
        if ($request->has('class_filter')) {
            $class_filter = $request->input('class_filter');
        }
        if ($request->has('role_filter')) {
            $role_filter = $request->input('role_filter');
        }
        if ($request->has('order_by_filter')) {
            $order_by_filter = $request->input('order_by_filter');
        }
        if ($request->has('order_dir_filter')) {
            $order_dir_filter = $request->input('order_dir_filter');
        }
        if ($request->has('participant_filter')) {
            $participant_filter = $request->input('participant_filter');
        }
        $teacher_id = Auth::user()->u_id;

        $participants = UserService::GetTeacherParticipantsForParticipantsIndexPage($teacher_id, $class_filter, $role_filter, $order_by_filter, $order_dir_filter, $participant_filter);
        $participants_names = UserService::GetTeacherParticipantsNames($teacher_id);
        $participants_emails = UserService::GetTeacherParticipantsEmails($teacher_id);


        $participants_autocomplete = array_merge($participants_names->toArray(), $participants_emails->toArray());
        if ($class_filter !== null) {
            $participants->appends(['class_filter' => $class_filter]);
        }
        if ($role_filter !== null) {
            $participants->appends(['role_filter' => $role_filter]);
        }
        if ($order_by_filter !== null) {
            $participants->appends(['order_by_filter' => $order_by_filter]);
        }
        if ($order_dir_filter !== null) {
            $participants->appends(['order_dir_filter' => $order_dir_filter]);
        }
        if ($order_dir_filter !== null) {
            $participants->appends(['participant_filter' => $participant_filter]);
        }
        $classes = ClassService::GetTeacherClasses($teacher_id);
        return view('user.index', [
            'participants' => $participants,
            'class_filter' => $class_filter,
            'role_filter' => $role_filter,
            'order_by_filter' => $order_by_filter,
            'order_dir_filter' => $order_dir_filter,
            'participant_filter' => $participant_filter,
            'classes' => $classes,
            'participants_autocomplete' => $participants_autocomplete
        ]);
    }

    public function LeaderBoardsPage(Request $request, $class_id = null)
    {

        $class_filter = null;
        if ($class_id !== null) {
            $class_filter = $class_id;
        }
        $user_id = Auth::user()->u_id;
        if (\Auth::user()->u_role == 1) {
            $classes = ClassService::GetAllParticipantClasses($user_id);
            if ($class_filter == null && count($classes) > 0) {
                $class_filter = $classes[0]->id;
            }
            if ($class_filter !== null) {
                $answers = LeaderboardService::GetTotalNumberOfAnswers($class_filter);
                $participants = LeaderboardService::GetTotalOfParticipantsWhoAnswered($class_filter);
                $info = LeaderboardService::GetLeaderboardinfo($class_id);
                $current_participant_info = LeaderboardService::GetParticipantLeaderboardInfo($class_filter, $user_id);

                return view('user.leaderboard', ['no_of_answers' => $answers, 'user_info' => $current_participant_info, 'no_of_participants' => $participants, 'info' => $info, 'classes' => $classes, 'class_filter' => $class_filter]);
            } else {
                return view('user.leaderboard', ['no_of_answers' => null, 'no_of_participants' => null, 'user_info' => null, 'info' => null]);

            }
        } else {
            $request->session()->flash('status', 'fail');
            $request->session()->flash('message', 'The leaderboard page is only for participants');
            return \Redirect::route('home');


        }

    }

    /**
     * Display the specified resource.
     *
     * @return View
     */
    public function show()
    {
        $user = Auth::user();
        $role_name = UserService::GetUserRoleName($user->u_id);
        return view('user.profile',
            ['user' => $user,
                'role' => $role_name]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return View
     */
    public function edit()
    {
        $user = Auth::user();
        $role_name = UserService::GetUserRoleName($user->u_id);
        return view('user.edit-profile',
            ['user' => $user,
                'role' => $role_name]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request)
    {
        $user_id = Auth::user()->u_id;
        $email = $request->input('email');
        $first_name = $request->input('first_name');
        $last_name = $request->input('last_name');
        $department = $request->input('department');
        $position = $request->input('position');
        $password_update_result = null;
        $update_info_result = null;

        if (UserService::ValidateUserInfoForUpdate($email, $first_name, $last_name) && $request->has('new_password') && trim($request->input('new_password')) != '') {
            if ($request->has('existing_password') && $request->has('confirm_new_password')) {
                if ($request->input('new_password') == $request->input('confirm_new_password')) {

                    $user = User::find($user_id);
                    if (Hash::check($request->input('existing_password'), $user->password)) {
                        $password_update_result = UserService::UpdatePassword($user_id, $request->input('new_password'));

                    } else {
                        $request->session()->flash('status', 'fail');
                        $request->session()->flash('message', 'The password in the existing password field is incorrect.');
                        $password_update_result = ['status' => false];
                    }
                } else {
                    $request->session()->flash('status', 'fail');
                    $request->session()->flash('message', 'Both new password field and confirm password field need to have the same value.');
                    $password_update_result = ['status' => false];
                }
            } else {
                $request->session()->flash('status', 'fail');
                $request->session()->flash('message', 'An error occurred while updating the profile.');
                $password_update_result = ['status' => false];
            }
        } else {
            $request->session()->flash('status', 'fail');
            $request->session()->flash('message', 'An error occurred while updating the profile.');
            $password_update_result = ['status' => true];
        }
        $update_info_result = UserService::UpdateUserInfo($user_id,
            $email, $first_name, $last_name,
            $department, $position);

        if ($update_info_result['status'] !== false || $password_update_result['status'] !== false) {
            $request->session()->flash('status', 'success');
            $request->session()->flash('message', 'The profile has been updated successfully.');
        }

        return Redirect::route('edit-profile');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function delete()
    {
        $user = User::find(Auth::user()->u_id);
        $user_id = Auth::user()->u_id;
        if ($user) {
            Auth::logout();
            UserService::DeleteRelatedParticipantInfo($user_id);
            $destroy = User::destroy($user->u_id);
            return Redirect::route('login');

        } else {
            return Redirect::route('login');
        }

    }

    public function deletePage()
    {
        return view('user.delete-account');
    }

    public function DismissUserFromClassAction(Request $request)
    {
        if ($request->has('user_id') && $request->has('class_ids')) {
            $teacher_id = Auth::user()->u_id;
            $user_id = $request->input('user_id');
            $class_ids = $request->input('class_ids');
            $result = UserService::DismissParticipant($user_id, $class_ids, $teacher_id);

            if ($request->has('ajax_call')) {
                return json_encode($result);
            }

            if ($result['status'] == true) {
                $request->session()->flash('status', 'success');
                $request->session()->flash('message', 'The participant has successfully been dismissed.');

            } else {
                $request->session()->flash('status', 'fail');
                $request->session()->flash('message', 'There was a problem in dismissing the participant.');

            }

            return Redirect::route('participants');
        } else {
            $request->session()->flash('status', 'fail');
            $request->session()->flash('message', 'There was a problem in dismissing the participant.');
            return Redirect::route('participants');

        }
    }

    public function GetTeacherUsersList(Request $request)
    {
        if ($request->has('status_filter') && Auth::user()->u_role > 1) {
            $teacher_id = Auth::user()->u_id;
            $status_filter = $request->input('status_filter');
            $users = UserService::GetTeacherAllParticipants($teacher_id, $status_filter, 1);
            return json_encode(['users' => $users, 'status' => 'success']);
        } else {
            return json_encode(['users' => null, 'status' => 'fail']);
        }
    }

    public function SetAnonymousCredentials(Request $request)
    {
        if ($request->has('quiz_id') && $request->has('direct_link')) {
            $sessions = QuizService::GetQuizSessions($request->input('quiz_id'), false);
            $session_id = null;
            if (count($sessions) > 0) {
                $session_id = $sessions[0]->id;
            }
            if ($session_id !== null) {
                $participant_id = UserService::CreateTemporaryParticipant($session_id);
                $request->session()->flash('participant-id', $participant_id);
                $request->session()->flash('session-id', $session_id);
            } else {
                $request->session()->flash('status', 'fail');
                $request->session()->flash('message', 'The quiz is currently closed.');
                return Redirect::route('login');
            }
            return \redirect()->to($request->input('direct_link'));
        }

        $request->session()->flash('status', 'fail');
        $request->session()->flash('message', 'An error occurred while starting the quiz.');
        return Redirect::route('login');
    }

    public function store(CreateUserRequest $request) {
        $validated = $request->validated();
        $new_user = User::create($validated);
    }
}
