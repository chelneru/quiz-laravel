<?php

namespace App\Http\Controllers;

use App\Imports\UserImport;
use App\Imports\UsersImport;
use App\Services\ClassService;
use App\Services\HelperService;
use App\Services\UserService;
use Carbon\Carbon;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Routing\Redirector;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Redirect;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Mail;

class ClassController extends Controller
{
    /**
     * This is the index page for classes where teachers can see all their classes.
     * @return Factory|View
     */
    public function index()
    {
        $user_id = Auth::user()->u_id;
        if (Auth::user()->u_role > 1) {//user is not a participant
            $classes = ClassService::GetTeacherClassesInfo($user_id);
            return view('classes.index', ['classes' => $classes]);
        } else {
            $classes = ClassService::GetParticipantClasses($user_id);
            return view('classes.index-participants', ['classes' => $classes]);
        }

    }

    /**
     * This is the page for teachers to create a class
     *
     * @param Request $request
     * @return Application|RedirectResponse|Redirector
     */
    public function create(Request $request)
    {

        if (UserService::IsTeacherClassCreationLimitReached(Auth::user()->u_id)) {
            $request->session()->flash('status', 'fail');
            $request->session()->flash('message', 'You reached the class limit. To create more classes wait for an administrator to approve your account.');
            return redirect(url()->previous());
        }
        return view('classes.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function store(Request $request)
    {
        if (Auth::user()->u_role > 1 && $request->has('class_name')) {
            $user_id = Auth::user()->u_id;

            if (UserService::IsTeacherClassCreationLimitReached($user_id)) {
                $request->session()->flash('status', 'fail');
                $request->session()->flash('message', 'You reached the classes limit. To create more classes wait for an administrator to approve your account.');
                return redirect(url()->previous());
            }

            $class_description = $request->has('class_description') ? $request->input('class_description') : '';
            $result = ClassService::CreateClass($request->input('class_name'), $class_description, $user_id);
            if ($result['status'] === true) {
                $request->session()->flash('status', 'success');
                $request->session()->flash('message', 'Class ' . $request->input('class_name') . ' has been created.');
                return Redirect::route('class-additional-info', ['id' => $result['value']]);

            } else {
                $request->session()->flash('status', 'fail');
                $request->session()->flash('message', 'There has been an error in creating the class.');
                return Redirect::route('create-class');

            }

        } else {
            $request->session()->flash('status', 'fail');
            $request->session()->flash('message', 'There has been an error in creating the class.The class has invalid parameters.');
            return Redirect::route('create-class');
        }
    }

    public function copyClassPage(Request $request, $class_id)
    {
        if (ClassService::CheckClassOwnership($class_id, Auth::user()->u_id)) {
            $user_id = Auth::user()->u_id;

            if (UserService::IsTeacherClassCreationLimitReached($user_id)) {
                $request->session()->flash('status', 'fail');
                $request->session()->flash('message', 'You reached the classes limit. To create more classes wait for an administrator to approve your account.');
                return redirect(url()->previous());
            }

            $class_info = ClassService::GetClassInfo($class_id);
            $today = Carbon::now('Europe/Copenhagen')->toDateString();

            $new_class_name = substr($class_info->name,0,170). ' - Copy ' . $today;
            return view('classes.copy-class-page', ['new_class_name' => $new_class_name, 'class_id' => $class_id]);
        } else {
            $request->session()->flash('status', 'fail');
            $request->session()->flash('message', 'Class not found.');
            return Redirect::route('classes');
        }
    }

    public function afterCreationPage($id)
    {
        $class_info = ClassService::GetClassInfo($id);
        return view('classes.after-creation-page', ['class_info' => $class_info]);
    }

    public function copyClassAction(Request $request)
    {
        if (Auth::user()->u_role > 1 && $request->has('original_class_id')) {
            $user_id = Auth::user()->u_id;

            if (UserService::IsTeacherClassCreationLimitReached($user_id)) {
                $request->session()->flash('status', 'fail');
                $request->session()->flash('message', 'You reached the classes limit. To create more classes wait for an administrator to approve your account.');
                return redirect(url()->previous());
            }

            $copy_participants = $request->input('copy_participants') == "on" ? true : false;
            $copy_teachers = $request->input('copy_teachers') == "on" ? true : false;
            $copy_quizzes = $request->input('copy_quizzes') == "on" ? true : false;

            $result = ClassService::CopyClass($request->input('original_class_id'),
                $request->input('class_name'), $copy_participants,
                $copy_teachers, $copy_quizzes, $user_id);

            if ($result['status'] === true) {
                $request->session()->flash('status', 'success');
                $request->session()->flash('message', 'The new class has been created successfully.');
            } else {
                $request->session()->flash('status', 'fail');
                $request->session()->flash('message', 'An error occurred while duplicating the class '.shortenedString($request->input('class_name'),20,true).'.');
            }
            return redirect('classes');
        } else {
            return "TODO";
        }
    }

    /**
     * @param $class_id
     * @return Factory|View
     */
    public function inviteParticipants($class_id = null)
    {

        $user_id = Auth::user()->u_id;
        $classes = ClassService::GetTeacherClasses($user_id);

        if ($class_id == null) {
            return view('classes.invite-participants', ['class_id' => $class_id, 'class_code' => null, 'classes' => $classes]);

        } else {

            $class_info = ClassService::GetClassInfo($class_id);
            return view('classes.invite-participants', ['class_id' => $class_id, 'class_code' => $class_info->code]);

        }
    }

    public function inviteParticipantsAction(Request $request)
    {
        if ($request->has('invite_method')) {
            $method = $request->input('invite_method');
            switch ($method) {
                case 'manual' :
                    if ($request->has('class_id') &&
                        $request->has('participant_email') &&
                        $request->has('participant_first_name') &&
                        $request->has('participant_last_name')
                    ) {
                        $result = ClassService::ManuallyInviteParticipants($request->input('class_id'),
                            $request->input('participant_first_name'), $request->input('participant_last_name'), $request->input('participant_email'));
                        if ($result['status'] == true) {
                            $request->session()->flash('status', 'success');
                            $request->session()->flash('message', 'The invitations have been sent successfully.');
                        } else {
                            $request->session()->flash('status', 'fail');
                            $request->session()->flash('message', 'An error occurred while sending the invitations.');
                        }

                    } else {
                        $request->session()->flash('status', 'fail');
                        $request->session()->flash('message', 'Invalid parameters');
                        return redirect(url()->previous());
                    }
                    break;
                case 'file_import' :

                    if (count($request->file()) > 0) {
                        try {
                            Excel::import(new UserImport($request->input('class_id')), request()->file()['file']);
                        } catch (Exception $e) {
                            return ['status' => false, 'message' => $e->getMessage() . ' in file: ' . $e->getFile() . ' at line: ' . $e->getLine()];

                        }
                        $request->session()->flash('status', 'success');
                        $request->session()->flash('message', 'The invitations have been sent.');

                        return 'success';
                    } else {
                        return 'fail';
                    }
                    break;
                default:
                    break;
            }

        } else {
            return "invalid parameters";
        }
        return Redirect::route('classes');

    }

    public function getClassInfo(Request $request, $id)
    {

        if (ClassService::CheckClassOwnership($id, Auth::user()->u_id)) {

            $class_info = ClassService::GetClassInfo($id);

            $class_quizzes = ClassService::GetClassQuizzes($id);
            $class_participants = ClassService::GetClassParticipants($id);

            return view('classes.class-details', ['class' => $class_info, 'class_quizzes' => $class_quizzes,
                'class_participants' => $class_participants]);
        } else {
            $request->session()->flash('status', 'fail');
            $request->session()->flash('message', 'Class not found .');
            return Redirect::route('classes');
        }
    }

    public function editClassPage(Request $request, $id)
    {

        if (ClassService::CheckClassOwnership($id, Auth::user()->u_id)) {


            $class_info = ClassService::GetClassInfo($id);

            $class_quizzes = ClassService::GetClassQuizzes($id);
            $class_participants = ClassService::GetClassParticipants($id);

            return view('classes.edit-class', ['class' => $class_info, 'class_quizzes' => $class_quizzes,
                'class_participants' => $class_participants]);
        } else {
            $request->session()->flash('status', 'fail');
            $request->session()->flash('message', 'Class not found.');
            return Redirect::route('classes');
        }
    }

    public function editClassAction(Request $request)
    {
        if ($request->has('class_id')) {
            if (ClassService::CheckClassOwnership($request->input('class_id'), Auth::user()->u_id)) {
                $result = ClassService::UpdateClassInfo($request->input('class_id'), $request->input('class_name'), $request->input('class_description'));

                if ($result['status'] == true) {
                    $request->session()->flash('status', 'success');
                    $request->session()->flash('message', 'The class has been updated.');
                } else {
                    $request->session()->flash('status', 'fail');
                    $request->session()->flash('message', 'An error occurred while updating the class.');
                }

                return Redirect::route('classes');
            } else {
                $request->session()->flash('status', 'fail');
                $request->session()->flash('message', 'Class not found.');
                return Redirect::route('classes');
            }
        }

    }

    public function CsvInvitesAction(Request $request)
    {
        $file = $request->file('file');

    }

    public function getClassCode(Request $request)
    {
        if ($request->has('class_id')) {
            $class_code = ClassService::GetClassCode($request->input('class_id'));
            return json_encode(['class_code' => $class_code]);
        }
    }

    public function AddClassQuizzesAction(Request $request)
    {
        if ($request->has('class_id') && $request->has('quizzes_ids') && Auth::user() !== null) {
            $user_id = Auth::user()->u_id;

            $result = null;
            foreach ($request->input('quizzes_ids') as $quiz_id) {
                $result = ClassService::ImportQuizToClass($user_id, $quiz_id, $request->input('class_id'));
            }
            $request->session()->flash('status', 'success');
            $request->session()->flash('message', 'The quizzes have been added successfully.');
            return back();
        } else {
            $request->session()->flash('status', 'fail');
            $request->session()->flash('message', 'The quizzes have not been added.');
            return back();
        }
    }

    public function JoinClass(Request $request)
    {
        if ($request->has('class_code')) {
            $participant_id = Auth::user()->u_id;
            $result = ClassService::JoinClassByCode($participant_id, $request->input('class_code'));

            return json_encode($result);
        } else {
            json_encode(['status' => false, 'message' => 'invalid params']);
        }
    }

    public function DeleteClass(Request $request)
    {
        if ($request->has('class_id')) {
            $class_id = $request->input('class_id');
            $teacher_id = Auth::user()->u_id;
            if (Auth::user()->u_role == 2 && Auth::user()->u_id == ClassService::GetClassAuthor($class_id)) {
                $result = ClassService::DeleteClass($class_id);
                return json_encode($result);
            }
        }
        return json_encode(['status' => false, 'message' => 'invalid params']);
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return Response
     */
    public function destroy($id)
    {
        //
    }
}
