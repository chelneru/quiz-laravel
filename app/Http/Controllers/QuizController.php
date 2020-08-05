<?php

namespace App\Http\Controllers;

use App\Exports\QuizResultsSessionExport;
use App\Exports\QuizSessionExport;
use App\Services\ClassService;
use App\Services\QuizExportService;
use App\Services\QuizService;
use App\Services\UserService;
use Carbon\Carbon;
use DB;
use Debugbar;
use Excel;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Auth;
use Config;
use Illuminate\Http\Response as ResponseAlias;
use Illuminate\View\View;
use Redirect;
use Session;
use stdClass;

class QuizController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return ResponseAlias
     */
    public function index(Request $request)
    {

        $user_id = Auth::user()->u_id;
        if (Auth::user()->u_role > 1) {//user is not a participant
            $class_filter = null;
            if ($request->has('class')) {
                $class_filter = $request->input('class');
            }

            $quizzes = QuizService::GetTeacherQuizzes($user_id, $class_filter);
            $classes = ClassService::GetTeacherClasses($user_id);
            return view('quizzes.index', ['quizzes' => $quizzes, 'classes' => $classes,
                'class_filter' => $class_filter]);
        } else {
            $quizzes = QuizService::GetParticipantQuizzes($user_id, true);
            return view('quizzes.index-participants', ['quizzes' => $quizzes]);

        }

    }

    /**
     * Show the form for creating a new resource.
     *
     * @param Request $request
     * @return Application|Factory|View
     */
    public function create(Request $request)
    {
        $class_id = null;
        if ($request->has('class_id')) {
            $class_id = $request->input('class_id');
        }

        $user_id = Auth::user()->u_id;


        if (UserService::IsTeacherQuizCreationLimitReached($user_id)) {
            $request->session()->flash('status', 'fail');
            $request->session()->flash('message', 'You reached the quiz limit. To create more quizzes wait for an administrator to approve your account.');
            return redirect(url()->previous());
        }

        $classes = ClassService::GetTeacherClasses($user_id);
        return view('quizzes.create-quiz-page', ['classes' => $classes, 'quiz' => new stdClass(), 'selected_class_id' => $class_id]);

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return false|ResponseAlias|string
     */
    public function store(Request $request)
    {
        if ($request->has('quiz_text') &&
            $request->has('quiz_questions') &&
            $request->has('quiz_description') &&
            Auth::user()
        ) {

            if (UserService::IsTeacherQuizCreationLimitReached(Auth::user()->u_id)) {
                return json_encode(['status' => false, 'message' => 'You reached the quiz limit. To create more quizzes wait for an administrator to approve your account.']);
            }

            $class_id = '';
            if ($request->has('quiz_class')) {
                $class_id = $request->input('quiz_class');
            }
            $result = QuizService::CreateNewQuizWithQuestions($request->input('quiz_text'),
                $request->input('quiz_description'),
                $request->input('quiz_questions'),
                Auth::user()->u_id,
                $class_id,
                $request->input('quiz_allow_anonymous'),
                $request->input('quiz_is_assessed'),
                false
            );
            if ($result['status'] === true) {
                $result['path'] = '/quiz/accompanying-questions/' . $result['quiz_id'];
                $request->session()->flash('status', 'success');
                $request->session()->flash('message', 'The new quiz has been created successfully.');
            } else {
                $request->session()->flash('status', 'fail');
                $request->session()->flash('message', 'An error occurred while creating the quiz.');
                $result['path'] = '/quiz/create-quiz';
            }


            return json_encode($result);
        }

        return json_encode(['status' => false, 'message' => 'missing parameters or user not logged in']);
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param Request $request
     * @param $id
     * @return RedirectResponse|ResponseAlias
     */
    public function edit(Request $request, $id)
    {
        if (QuizService::CheckQuizOwnership($id, Auth::user()->u_id)) {
            $user_id = Auth::user()->u_id;
            $classes = ClassService::GetTeacherClasses($user_id);
            $quiz_id = $id;
            $quiz_info = QuizService::GetQuizInfo($quiz_id);
            if ($quiz_info !== null) {
                $quiz_info->allow_anon = convertIntToBool($quiz_info->allow_anon);
                $quiz_info->is_assessed = convertIntToBool($quiz_info->is_assessed);
                $quiz_info->link = $request->getSchemeAndHttpHost() . '/direct-quiz/' . $quiz_id;
            }
            return view('quizzes.create-quiz-page', ['classes' => $classes, $quiz_info,
                'quiz' => $quiz_info]);
        }

        $request->session()->flash('status', 'fail');
        $request->session()->flash('message', 'Quiz not found.');
        return Redirect::route('quizzes');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @return array|RedirectResponse
     */
    public function update(Request $request)
    {
        if ($request->has('quiz_text') &&
            $request->has('quiz_id') &&
            $request->input('quiz_id') !== null &&
            $request->has('quiz_questions') &&
            $request->has('quiz_description') &&
            Auth::user()
        ) {

            if (QuizService::CheckQuizOwnership($request->input('quiz_id'), Auth::user()->u_id)) {


                $class_id = '';
                if ($request->has('quiz_class')) {
                    $class_id = $request->input('quiz_class');
                }
                $result = QuizService::UpdateQuiz($request->input('quiz_id'), $request->input('quiz_text'),
                    $request->input('quiz_questions'),
                    Auth::user()->u_id,
                    $class_id,
                    $request->input('quiz_allow_anonymous'),
                    $request->input('quiz_is_assessed'),
                    $request->input('quiz_description'));
                if ($result['status'] === true) {
                    $request->session()->flash('status', 'success');
                    $request->session()->flash('message', 'The quiz has been updated successfully.');
                    $result['path'] = route('accompanying-questions', $request->input('quiz_id'));
                } else {
                    $request->session()->flash('status', 'success');
                    $request->session()->flash('message', 'An error occurred while updating the quiz.');
                    $result['path'] = route('edit-quiz', $request->input('quiz_id'));

                }
                return json_encode($result);

            }

            return json_encode(['status' => false, 'message' => 'missing parameters or user not logged in']);
        }

        $request->session()->flash('status', 'fail');
        $request->session()->flash('message', 'Quiz not found.');
        return Redirect::route('quizzes');
    }

    public function duplicateWithEdit(Request $request, $id)
    {
        if (QuizService::CheckQuizOwnership($id, Auth::user()->u_id)) {
            $user_id = Auth::user()->u_id;

            if (UserService::IsTeacherQuizCreationLimitReached($user_id)) {
                $request->session()->flash('status', 'fail');
                $request->session()->flash('message', 'You reached the quiz limit. To create more quizzes wait for an administrator to approve your account.');
                return redirect(url()->previous());
            }

            $classes = ClassService::GetTeacherClasses($user_id);
            $quiz_id = $id;

            $result = QuizService::DuplicateQuiz($user_id, $id, true, true);
            if ($result['status'] == true) {
                $quiz_info = QuizService::GetQuizInfo($quiz_id);
                if ($quiz_info !== null) {
                    //remove the quiz id so we will create a new quiz not update an existing one.
                    $today = Carbon::now('Europe/Copenhagen')->toDateString();
                    $quiz_info->title = $quiz_info->title . ' - Copy ' . $today;
                    $quiz_info->link = $request->getSchemeAndHttpHost() . '/direct-quiz/' . $quiz_id;

                    return view('quizzes.create-quiz-page', ['classes' => $classes,
                        'quiz' => $quiz_info]);
                }
            } else {

                $request->session()->flash('status', 'fail');
                $request->session()->flash('message', 'An error occurred while duplicating the quiz.');

                return Redirect::route('quizzes');
            }


            $request->session()->flash('status', 'fail');
            $request->session()->flash('message', 'Quiz not found.');
            return Redirect::route('quizzes');

        }
        $request->session()->flash('status', 'fail');
        $request->session()->flash('message', 'Quiz not found.');
        return Redirect::route('quizzes');
    }

    public function getQuizInfo(Request $request, $id)
    {
        if (QuizService::CheckQuizOwnership($id, Auth::user()->u_id) || UserService::IsAdmin(Auth::user()->u_id)) {
            $quiz = QuizService::GetQuizInfo($id);

            $quiz->link = $request->getSchemeAndHttpHost() . '/direct-quiz/' . $id;

            $is_admin = UserService::IsAdmin(Auth::user()->u_id);
            return view('quizzes.quiz-details', ['quiz' => $quiz, 'is_admin' => $is_admin]);

        }

        $request->session()->flash('status', 'fail');
        $request->session()->flash('message', 'Quiz not found.');
        return Redirect::route('quizzes');

    }

    public function duplicate(Request $request, $id): ?RedirectResponse
    {
        if ($id !== null && QuizService::CheckQuizOwnership($id, Auth::user()->u_id)) {
            $user_id = Auth::user()->u_id;

            if (UserService::IsTeacherQuizCreationLimitReached($user_id)) {
                $request->session()->flash('status', 'fail');
                $request->session()->flash('message', 'You reached the quiz limit. To create more quizzes wait for an administrator to approve your account.');
                return redirect(url()->previous());
            }

            $result = QuizService::DuplicateQuiz($user_id, $id, true, true);
            if ($result['status'] === true) {
                $request->session()->flash('status', 'success');
                $request->session()->flash('message', 'The quiz has been duplicated successfully.');
                return Redirect::route('quiz-info', ['id' => $result['quiz_id']]);

            }
            $request->session()->flash('status', 'fail');
            $request->session()->flash('message', 'An error occurred while duplicating the quiz.');

            return Redirect::route('quizzes');
        }

        $request->session()->flash('status', 'fail');
        $request->session()->flash('message', 'Quiz not found.');
        return Redirect::route('quizzes');
    }


    public function deleteQuiz(Request $request)
    {

        if ($request->has('quiz_id') && QuizService::CheckQuizOwnership($request->input('quiz_id'), Auth::user()->u_id)) {
            $unlink = null;
            if ($request->has('just_unlink')) {
                $just_unlink = convertBoolStringToInt($request->input('just_unlink'));
            } else {
                $just_unlink = false;
            }
            $result = QuizService::DeleteQuiz($request->input('quiz_id'), $just_unlink);

            if ($result['status'] === true) {
                return json_encode(['status' => true, 'message' => 'success']);

            }

            return json_encode(['status' => false, 'message' => $result['message']]);
        }

        return json_encode(['status' => false, 'message' => 'quiz not found']);
    }

    public function GetTeacherQuizzesList(Request $request)
    {
        if ($request->has('class_id') && Auth::user() !== null) {
            $user_id = Auth::user()->u_id;
            $class_id = $request->input('class_id');
            $quizzes = ClassService::GetAvailableQuizzesForClass($class_id, $user_id);

            return json_encode(['quizzes' => $quizzes, 'message' => 'success']);
        }

        return json_encode(['quizzes' => [], 'message' => 'missing-params']);
    }

    public function QuizAccompanyingQuestionsPage(Request $request, $id)
    {
        if (strpos(url()->previous(), 'create-quiz') !== false) {
            $create_quiz_mode = true;
        } else {
            $create_quiz_mode = false;
        }
        if ($id !== null && QuizService::CheckQuizOwnership($id, Auth::user()->u_id)) {

            $quiz_info = QuizService::GetQuizInfo($id);
            $quiz_accompanying_questions = QuizService::GetQuizAccompanyingQuestions($id);

            //initially we load the defaults for the each type of accompanying questions and then we replace with existing accompanying questions for the quiz
            $accompanying_questions = QuizService::GetAccompanyingQuestionDefaults();

            foreach ($quiz_accompanying_questions as $quiz_accompanying_question) {
                switch ($quiz_accompanying_question->type) {
                    case 1:
                        $accompanying_questions->prep_question = $quiz_accompanying_question;
                        break;
                    case 2:
                        $accompanying_questions->conf_question = $quiz_accompanying_question;
                        break;
                    case 3:
                        $accompanying_questions->just_question = $quiz_accompanying_question;
                        break;
                    case 4:
                        //by default we will have only one empty other questions.
                        if (isset($accompanying_questions->other_questions)) {
                            $accompanying_questions->other_questions[] = $quiz_accompanying_question;
                        } else {
                            $accompanying_questions->other_questions = [$quiz_accompanying_question];
                        }

                        break;
                }
            }

            return view('quizzes.quiz-accompanying-questions', ['quiz' => $quiz_info, 'accompanying_questions' => $accompanying_questions, 'create_quiz_mode' => $create_quiz_mode]);
        }

        $request->session()->flash('status', 'fail');
        $request->session()->flash('message', 'Quiz not found.');
        return Redirect::route('quizzes');
    }

    public function QuizAccompanyingQuestionsAction(Request $request)
    {
        if ($request->has('quiz_id')) {
            if ($request->input('quiz_id') !== null && QuizService::CheckQuizOwnership($request->input('quiz_id'), Auth::user()->u_id)) {

                $result = QuizService::UpdateQuizAccompanyingQuestions($request->input('quiz_id'),
                    $request->input('prep_question'), 1);

                if ($result['status'] === false) {
                    return $result;
                }
                $result = QuizService::UpdateQuizAccompanyingQuestions($request->input('quiz_id'),
                    $request->input('conf_question'), 2);
                if ($result['status'] === false) {
                    return $result;
                }
                $result = QuizService::UpdateQuizAccompanyingQuestions($request->input('quiz_id'),
                    $request->input('just_question'), 3);
                if ($result['status'] === false) {
                    return $result;
                }
                $result = QuizService::UpdateQuizAccompanyingQuestions($request->input('quiz_id'),
                    $request->input('other_questions'), 4);
                $request->session()->flash('status', 'success');
                $request->session()->flash('message', 'The quiz\'s accompanying questions have been updated successfully.');

                if ($request->session()->has('create_quiz_mode') && $request->session()->get('create_quiz_mode') === true) {
                    $result['create_quiz_mode'] = true;
                } else {
                    $result['create_quiz_mode'] = false;
                }
                return json_encode($result);

            } else {
                return json_encode(['status' => false, 'message' => 'quiz not found']);

            }
        } else {
            return json_encode(['status' => false, 'message' => 'invalid params']);
        }
    }

    public function QuizAdditionalMessage(Request $request, $id)
    {
        if (strpos(url()->previous(), 'accompanying-questions') !== false) {
            $create_quiz_mode = true;
        } else {
            $create_quiz_mode = false;
        }
        if ($id !== null && QuizService::CheckQuizOwnership($id, Auth::user()->u_id)) {

            $quiz_info = QuizService::GetQuizInfo($id);
            $quiz_message_info = QuizService::GetQuizMessage($id);
            if ($quiz_message_info == null) {
                // fill with default
                $quiz_message_info = new stdClass;
                $quiz_message_info->message = '';
                $quiz_message_info->message_title = '';
            }

            return view('quizzes.quiz-additional-messages', ['quiz' => $quiz_info, 'quiz_message_info' => $quiz_message_info, 'create_quiz_mode' => $create_quiz_mode]);
        }

        $request->session()->flash('status', 'fail');
        $request->session()->flash('message', 'Quiz not found.');
        return Redirect::route('quizzes');
    }


    public function UpdateAdditionalMessage(Request $request)
    {

        if ($request->has('quiz_id') &&
            $request->has('message')) {
            if ($request->input('quiz_id') !== null && QuizService::CheckQuizOwnership($request->input('quiz_id'), Auth::user()->u_id)) {
                $result = QuizService::UpdateAdditionalMessage($request->input('quiz_id'), $request->input('message'), $request->input('message_title'));


                if ($result['status'] === true) {
                    $request->session()->flash('status', 'success');
                    $request->session()->flash('message', 'Quiz\'s starting message has been updated successfully.');
                } else {
                    $request->session()->flash('status', 'success');
                    $request->session()->flash('message', 'There was an error in updating the quiz\'s starting message.');
                }

                if ($request->session()->has('create_quiz_mode') && $request->session()->get('create_quiz_mode') === true) {
                    $result['create_quiz_mode'] = true;
                } else {
                    $result['create_quiz_mode'] = false;
                }

                return json_encode($result);
            }

            return json_encode(['status' => false, 'message' => 'quiz not found']);
        }

        return json_encode(['status' => false, 'message' => 'invalid params']);
    }

    public function QuizSchedulingPage(Request $request, $id)
    {
        if (strpos(url()->previous(), 'additional-messages') !== false) {
            $create_quiz_mode = true;
        } else {
            $create_quiz_mode = false;
        }
        if (QuizService::CheckQuizOwnership($id, Auth::user()->u_id)) {

            $quiz_info = QuizService::GetQuizSchedulingInfo($id);
            if ($quiz_info !== null) {
                $quiz_info->id = $id;
                $quiz_title = QuizService::GetQuizTitle($id);
                $quiz_info->title = $quiz_title->title ?? '';
            }

            return view('quizzes.quiz-scheduling', ['quiz' => $quiz_info, 'create_quiz_mode' => $create_quiz_mode]);
        }

        $request->session()->flash('status', 'fail');
        $request->session()->flash('message', 'Quiz not found.');
        return Redirect::route('quizzes');
    }

    public function QuizSchedulingAction(Request $request): RedirectResponse
    {
        if ($request->has('quiz_id') &&
            $request->has('quiz_participation_count') &&
            $request->has('quiz_availability')) {
            //establish participation count
            $participation_count = null;
            if ($request->input('quiz_participation_count') === 2) {
                if ($request->has('participation_input')) {
                    $participation_count = $request->input('participation_input');
                } else {
                    $request->session()->flash('status', 'fail');
                    $request->session()->flash('message', 'You need to mention a specific number of times a participant can take the quiz.');
                    return Redirect::route('quiz-scheduling', ['id' => $request->input('quiz_id')]);
                }
            } else {
                $participation_count = $request->input('quiz_participation_count');
            }
            //establish dates
            $dates = null;

            if ((int)$request->input('quiz_availability') === 2) {
                if ($request->has('initial_phase_start') &&
                    $request->has('initial_phase_end') &&
                    $request->has('revision_phase_start') &&
                    $request->has('revision_phase_end') &&
                    $request->has('reveal_answers_start') &&
                    $request->has('reveal_answers_end')) {
                    $dates = ['init_ph_start' => $request->input('initial_phase_start'),
                        'init_ph_end' => $request->input('initial_phase_end'),
                        'rev_ph_start' => $request->input('revision_phase_start'),
                        'rev_ph_end' => $request->input('revision_phase_end'),
                        'reveal_start' => $request->input('reveal_answers_start'),
                        'reveal_end' => $request->input('reveal_answers_end')
                    ];
                    //TODO validate date intervals
                }
            } else {
                $dates = false;
            }
            if ($participation_count !== null && $dates !== null) {
                //both sections are valid we can update
                $limit_update_result = QuizService::SetQuizParticipationLimit($request->input('quiz_id'), $participation_count);
                if ($dates === false) {
                    //make the dates inactive
                    QuizService::DeactivateQuizScheduling($request->input('quiz_id'));
                    $scheduling_update_result = ['status' => true];
                } else {
                    //update the quiz scheduling
                    $scheduling_update_result = QuizService::AddQuizScheduling($request->input('quiz_id'),
                        $dates['init_ph_start'], $dates['init_ph_end'],
                        $dates['rev_ph_start'], $dates['rev_ph_end'],
                        $dates['reveal_start'], $dates['reveal_end']);
                }
                if ($limit_update_result['status'] === false) {
                    $request->session()->flash('status', 'fail');
                    $request->session()->flash('message', 'An error occurred while updating the participation limit.');
                    return Redirect::route('quiz-scheduling', ['id' => $request->input('quiz_id')]);
                }
                if ($scheduling_update_result['status'] === false) {
                    $request->session()->flash('status', 'fail');
                    $request->session()->flash('message', 'An error occurred while updating the quiz scheduling.');
                    return Redirect::route('quiz-scheduling', ['id' => $request->input('quiz_id')]);
                }
                $request->session()->flash('status', 'success');
                $request->session()->flash('message', 'The quiz scheduling has been updated.');
                return Redirect::route('quiz-info', ['id' => $request->input('quiz_id')]);

            }

            if ($participation_count === null) {
                $request->session()->flash('status', 'fail');
                $request->session()->flash('message', 'You need to mention a specific number of times a participant can take the quiz.');
                return Redirect::route('quiz-scheduling', ['id' => $request->input('quiz_id')]);
            }
            if ($dates === null) {
                $request->session()->flash('status', 'fail');
                $request->session()->flash('message', 'You need to mention valid dates for scheduling.');
                return Redirect::route('quiz-scheduling', ['id' => $request->input('quiz_id')]);
            }

        }
        $request->session()->flash('status', 'fail');
        $request->session()->flash('message', 'Some parameters are missing.');
        return Redirect::route('quizzes');
    }

    public function QuizInProgress(Request $request, $quiz_id)
    {

        $is_acc_question = false;
        $result_question = null;
        $previous_answers = null;
        $accompanying_questions = null;
        $prep_question = null;
        $just_question = null;
        $layout = '';
        $conf_question = null;
        $other_questions = [];
        $participant_id = null;
        $progress = null;
        if ($request->session()->get('participant-id') === null && Auth::user() !== null) {
            //logged in user
            $user_id = Auth::user()->u_id;

            $progress = QuizService::GetParticipantCurrentQuizInformation($user_id, $quiz_id, true);
            if ($progress === null) {
                // the participant's progress is on a session that has been closed

                //create progress on the new session
                $result = QuizService::StartQuizProgress($user_id, $request->input('quiz_id'));
                if ($result['status'] === false) {
                    $request->session()->flash('status', 'fail');
                    $request->session()->flash('message', 'There has been an error starting the quiz.');
                }
                //retrieve the new progress
                $progress = QuizService::GetParticipantCurrentQuizInformation($user_id, $quiz_id, false);
                if ($progress === null) {
                    $request->session()->flash('status', 'fail');
                    $request->session()->flash('message', 'An error occurred while retrieving the current quiz progress');

                    return Redirect::route('home');
                }

            }

            $participant_id = $progress->id;
            $layout = 'layouts.app';

        } else {
            //anonymous user
            $session_id = $request->session()->get('session-id');
            $progress = QuizService::GetAnonParticipantCurrentQuizInformation($request->session()->get('participant-id'), $session_id, false);
            $participant_id = $request->session()->get('participant-id');
            $layout = 'layouts.app-no-menu';
            if ($progress === null) {
                $request->session()->flash('status', 'fail');
                $request->session()->flash('message', 'An error occurred while retrieving the current quiz progress');
                return Redirect::route('login');
            }
            $request->session()->flash('participant-id', $participant_id);
            $request->session()->flash('session-id', $progress->session_id);

        }

        $progress->quiz_data = json_decode($progress->quiz_data, true);
        //check if the quiz is open
        if ($progress->qs_stopped_at !== null) {
            $request->session()->flash('status', 'fail');
            $request->session()->flash('message', 'The quiz is closed.');
            if (Auth::user()) {

                return Redirect::route('home');
            }

            return Redirect::route('login');

        }
        if ($progress->phase > $progress->session_phase) {
            return view('quizzes.quiz-wait-next-phase', ['quiz_id' => $progress->quiz_id, 'layout' => $layout]);
        }
        $is_revision_phase = $progress->phase === Config::get('defines.QUIZ_REVISION_PHASE');

        $no_of_questions = QuizService::GetQuizQuestionsCount($progress->quiz_data);


        $no_of_other_questions_after_quiz = QuizService::GetQuizOtherQuestionsCount($progress->quiz_data['acc_questions'], 3);

        if ($is_revision_phase) {
            $question_id = null;
            foreach ($progress->quiz_data['questions'] as $iter => $iterValue) {
                if ($iter + 1 === $progress->index) {
                    $question_id = $progress->quiz_data['questions'][$iter]['id'];
                }
            }
            $previous_answers = QuizService::InitialPhaseResponse($progress->session_id, $question_id, $participant_id);
        }


        switch ($progress->index) {
            case 0:
                //display preparation question, if there is no accompanying question to display then display first question
                $prep_question = QuizService::GetQuizPreparationQuestion($progress->quiz_id);

                if ($prep_question !== null) {
                    $result_question = $prep_question;
                    $is_acc_question = true;
                } else {
                    $progress->index++;
                    $result_question = QuizService::GetQuizQuestionFromData($progress->index, $progress->quiz_data, $is_revision_phase);
                    if ($result_question !== null) {
                        $accompanying_questions = QuizService::GetQuestionAccompanyingQuestions($progress->quiz_data, $progress->session_id, $result_question['id'], $is_revision_phase);

                        foreach ($accompanying_questions as $accompanying_question) {
                            if ($accompanying_question['type'] === 1) {
                                $prep_question = (object)$accompanying_question;
                            } else if ($accompanying_question['type'] === 2) {
                                $conf_question = (object)$accompanying_question;
                            } else if ($accompanying_question['type'] === 3) {
                                $just_question = (object)$accompanying_question;
                                if (!is_array($just_question->feedback)) {
                                    $just_question->feedback = explode(',', $just_question->feedback);
                                }
                                if (!is_array($just_question->feedback)) {
                                    $just_question->feedback = explode(',', $just_question->positions);
                                }
                            } else if ($accompanying_question['type'] === 4) {
                                $other_questions[] = (object)$accompanying_question;

                            }
                        }
                    }
                }
                $result_question = json_decode(json_encode($result_question), true);
                break;

            case $progress->index < 0:
                //here are included other accompanying questions that are before the quiz.
                $is_acc_question = true;
                $result_question = QuizService::GetQuizAccQuestionFromData($progress->index, $progress->quiz_data, $progress->phase);
                break;
            case $progress->index > $no_of_questions && $progress->index <= $no_of_questions + $no_of_other_questions_after_quiz:
                //here we include other accompanying questions that are after the quiz. To display them we have to be in the revision phase
                if ($is_revision_phase) {
                    $is_acc_question = true;
                    $result_question = QuizService::GetQuizAccQuestionFromData($progress->index, $progress->quiz_data, $progress->phase);
                }
                break;
            default:
                //normal quiz question
                $result_question = QuizService::GetQuizQuestionFromData($progress->index, $progress->quiz_data, $is_revision_phase, $progress->session_id);
        }
        if ($result_question !== null) {
            $accompanying_questions = QuizService::GetQuestionAccompanyingQuestions($progress->quiz_data, $progress->session_id, $result_question['id'], $is_revision_phase);

            foreach ($accompanying_questions as $accompanying_question) {
                if ($accompanying_question['type'] === 1) {
                    $prep_question = (object)$accompanying_question;
                } else if ($accompanying_question['type'] === 2) {
                    $conf_question = (object)$accompanying_question;
                } else if ($accompanying_question['type'] === 3) {
                    $just_question = (object)$accompanying_question;
                    if (!is_array($just_question->feedback)) {
                        $just_question->feedback = explode(',', $just_question->feedback);
                    }
                    if (!is_array($just_question->feedback)) {
                        $just_question->feedback = explode(',', $just_question->positions);
                    }
                } else if ($accompanying_question['type'] === 4) {
                    $other_questions[] = (object)$accompanying_question;

                }
            }
        } else {

            //this is the session variables for the anon users
            if (Auth::user() === null) {
                $request->session()->flash('participant-id', $participant_id);
                $request->session()->flash('session-id', $progress->session_id);
            }
            $no_of_other_questions_after_quiz = QuizService::GetQuizOtherQuestionsCount($progress->quiz_data['acc_questions'], 3);
            if ($progress->phase == Config::get('defines.QUIZ_INITIAL_PHASE')) {
                if ($progress->index > $no_of_questions) {
                    //participant answered to all questions in first phase , we are ready for the next phase
                    $progress->phase = Config::get('defines.QUIZ_REVISION_PHASE');
                    $progress->index = 1;
                    QuizService::UpdateQuizProgress($participant_id, $progress->quiz_id, $progress->index, $progress->phase, true, false);

                    return view('quizzes.quiz-wait-next-phase', ['quiz_id' => $progress->quiz_id, 'layout' => $layout]);
                }
            } else if ($progress->index > $no_of_questions + $no_of_other_questions_after_quiz && $progress->phase == Config::get('defines.QUIZ_REVISION_PHASE')) {
                //we reached the end of the quiz
                if ($progress->reveal_answers === 1) {
                    //answers have been revealed, show results

                    QuizService::UpdateQuizProgress($participant_id, $progress->quiz_id, $progress->index, $progress->phase, true, true);
                    return Redirect::route('quiz-results', ['quiz_id' => $progress->quiz_id, 'progress_id' => $progress->id]);
                } else {
                    //wait for answers to be revealed
                    return view('quizzes.quiz-wait-reveal-answers', ['quiz_id' => $progress->quiz_id, 'layout' => $layout]);

                }
            }

        }
        if ($result_question === null) {
            //we reached the end of the quiz
            $request->session()->flash('status', 'fail');
            $request->session()->flash('message', 'An error occurred while fetching the question.');
            return Redirect::route('home');
        }

        //update progress with question id
        QuizService::UpdateQuizProgress($participant_id, $progress->quiz_id, $progress->index, $progress->phase, true, false);

        return view('quizzes.quiz-running', ['progress' => $progress, 'question' => $result_question, 'is_acc_question' => $is_acc_question, 'conf_question' => $conf_question,
            'just_question' => $just_question, 'prep_question' => $prep_question, 'other_questions' => $other_questions, 'previous_answers' => $previous_answers, 'layout' => $layout]);

    }

    public function GetQuizDashboardInfo(Request $request)
    {
        $user_id = Auth::user()->u_id;

        if ($request->has('quiz_id')) {

            $quiz_info = QuizService::GetDashboardQuizInfo($request->input('quiz_id'));
            if ($quiz_info !== null) {
                $quiz_sessions = QuizService::GetQuizSessions($request->input('quiz_id'), false);
                $session_id = null;
                $quiz_info->total_sessions = $quiz_sessions->total();
                if ($quiz_info->total_sessions > 0) {
                    $session_id = $quiz_sessions[0]->id;
                }
                if ($session_id !== null) {

                    $quiz_info->past_completed_participations = QuizService::GetParticipantPastAttendance($session_id, $user_id);

                    $quiz_info->status = true;
                    $quiz_info->ongoing_progress = QuizService::CheckOngoingProgressOnSession($user_id, $session_id);
                } else {
                    $quiz_info->status = false;

                }
                return json_encode($quiz_info);
            }

            return json_encode(['status' => false, 'message' => 'quiz info not found']);
        }

        return json_encode(['status' => false, 'message' => 'invalid params']);
    }

    public function QuizStartPage(Request $request, $quiz_id)
    {

        $quiz_info = QuizService::GetDashboardQuizInfo($quiz_id);
        if ($quiz_info !== null) {
            $quiz_sessions = QuizService::GetQuizSessions($quiz_id, false);
            $session_id = null;
            $quiz_info->total_sessions = $quiz_sessions->total();
            if ($quiz_info->total_sessions > 0) {
                $session_id = $quiz_sessions[0]->id;
            }
            if ($session_id !== null) {
                $quiz_info->status = true;

                if (Auth::user() !== null) {
                    $user_id = Auth::user()->u_id;
                    $quiz_info->ongoing_progress = QuizService::CheckOngoingProgressOnSession($user_id, $session_id);

                }
            } else {
                $quiz_info->status = false;
                $request->session()->flash('status', 'fail');
                $request->session()->flash('message', 'The quiz is closed.');
                return redirect()->route('home');
            }
            $participant_id = null;
            if (Auth::user() === null) {
                //user is not logged in

                $participant_id = Session::get('participant-id');
            }
        } else {
            $request->session()->flash('status', 'fail');
            $request->session()->flash('message', 'An error occurred while starting the quiz.');
            if (!Auth::user()) {

                return Redirect::route('login');
            }

            return Redirect::route('home');
        }

        if ($participant_id !== null) {
            $request->session()->flash('participant-id', $participant_id);
            $request->session()->flash('session-id', $session_id);

            $layout = 'layouts.app-no-menu';
        } else {
            $layout = 'layouts.app';

        }
        return view('quizzes.quiz-start-page', ['participant_id' => $participant_id, 'quiz' => $quiz_info, 'layout' => $layout, 'session_id' => $session_id]);
    }

    public function ParticipantStartQuiz(Request $request)
    {
        if ($request->input('anon_participation') !== true && $request->input('anon_participation') != 1) {
            //for logged in participation

            if ($request->has('quiz_id')) {
                $user_id = Auth::user()->u_id;

                $enrolled = QuizService::CheckParticipantQuizEnrollment($user_id, $request->input('quiz_id'));
                $quiz_status = QuizService::GetQuizStatus($request->input('quiz_id'));
                Debugbar::info('enrolled status : ' . $enrolled);
                if ($quiz_status === Config::get('defines.QUIZ_STATUS_RUNNING') && $enrolled === true) {
                    $result = QuizService::StartQuizProgress($user_id, $request->input('quiz_id'));
                    if ($result['status'] === true) {
                        if ($request->input('json_response') == true) {
                            return json_encode(['status' => true, 'message' => 'success', 'start' => true]);

                        }
                        return Redirect::to('/quiz/' . $request->input('quiz_id'));
                    }
                    if ($request->input('json_response') == true) {
                        return json_encode(['status' => false, 'message' => 'error', 'start' => false]);

                    }
                    $request->session()->flash('status', 'fail');
                    $request->session()->flash('message', 'An error occurred while starting the quiz.');
                    return Redirect::route('login');
                }
                if ($request->input('json_response') == true) {
                    return json_encode(['status' => true, 'message' => 'success', 'start' => true]);
                } else {
                    return Redirect::to('/quiz/' . $request->input('quiz_id'));
                }
            }

            return json_encode(['status' => false, 'message' => 'invalid params', 'start' => false]);
        } // for anonymous participation
        if ($request->input('participant_id') !== null &&
            ($request->input('anon_participation') === true || $request->input('anon_participation') == 1) &&
            $request->input('session_id') !== null &&
            $request->input('quiz_id') !== null
        ) {
            $result = QuizService::StartAnonQuizProgress($request->input('participant_id'), $request->input('session_id'));
            if ($result['status'] === true) {
                $request->session()->flash('participant-id', $request->input('participant_id'));
                $request->session()->flash('session-id', $request->input('session_id'));
                return Redirect::to('/quiz/' . $request->input('quiz_id'));

            }
            $request->session()->flash('status', 'fail');
            $request->session()->flash('message', 'An error occurred while starting the quiz.');
            return Redirect::route('login');
        }
        $request->session()->flash('status', 'fail');
        $request->session()->flash('message', 'Invalid request');
        return Redirect::route('login');
    }

    public function QuizSubmitAnswer(Request $request)
    {
        if (Auth::user() !== null) {
            $user_id = Auth::user()->u_id;
        } else {
            $user_id = null;
        }
        $is_acc_question = null;
        if ((($request->has('question_answer') && $request->has('question_id')) ||
                (($request->has('acc_question_answer') || $request->has('acc_question_answer_id')) && $request->has('acc_question_id'))) &&
            $request->has('response_duration') && $request->has('quiz_id')) {
            //get current progress
            $progress = QuizService::GetParticipantCurrentQuizInformation($user_id, $request->input('quiz_id'), true);
            if ($progress === null && $request->session()->get('participant-id') !== null && $request->session()->get('session-id') !== null) {
                //we have anonymous participation
                $progress = QuizService::GetAnonParticipantCurrentQuizInformation($request->session()->get('participant-id'), $request->session()->get('session-id'), true);

                if ($progress === null) {
                    //anonymous participation but cant find any progress, return to start quiz page
                    return Redirect::route('quiz-start-page', ['quiz_id' => $request->input('quiz_id')]);

                }

                $request->session()->flash('participant-id', $request->session()->get('participant-id'));
                $request->session()->flash('session-id', $request->session()->get('session-id'));
            } else if ($progress === null) {
                //logged in user but can't find progress
                return Redirect::route('home');
            }

            $participant_id = $progress->id;
            if ($request->has('acc_question_answer') || $request->has('acc_question_answer_id')) {
                $is_acc_question = true;
            } else {
                $is_acc_question = false;
            }
            if ($progress !== null) {
                $quiz_data = QuizService::GetSessionQuizData($progress->session_id);

                if ($is_acc_question === false) {
                    $result = QuizService::ParticipantSubmitQuestionAnswers($progress->session_id, $progress->phase,
                        $request->input('question_id'), $request->input('question_answer'),
                        $request->input('conf_question_id'), $request->input('conf_question_answer'),
                        $request->input('just_question_id'), $request->input('justification_answer'),
                        $request->input('other_question_ids'), $request->input('other_question_answers'), $request->input('response_duration'),
                        $participant_id, $quiz_data);
                } else {
                    $result = QuizService::ParticipantSubmitAccAnswers($progress->session_id,
                        $request->input('acc_question_id'), $request->input('acc_question_answer_id'),
                        $request->input('acc_question_answer'), $progress->phase, $participant_id, $quiz_data);
                }

                if ($result['status'] === true) {
                    $progress_question_id = $request->input('question_id');
                    if ($progress_question_id === null) {
                        $progress_question_id = $request->has('prep_question_id');
                    }
                    //Update Progress
                    $result = QuizService::UpdateQuizProgress($participant_id, $progress->quiz_id, $progress->index + 1,
                        $progress->phase, true, false);

                    if ($result['status'] === true) {
                        return Redirect::route('quiz-in-progress', ['quiz_id' => $request->input('quiz_id')]);
                    } else {
                        $request->session()->flash('status', 'fail');
                        $request->session()->flash('message', 'An error occurred while submitting the answer.');
                        return redirect(url()->previous());
                    }
                } else {
                    $request->session()->flash('status', 'fail');
                    $request->session()->flash('message', 'An error occurred while submitting the answer.');
                }
                return Redirect::route('quiz-in-progress', ['quiz_id' => $request->input('quiz_id')]);

//                return $result;
            }

            return "you haven't started this quiz yet. First start the quiz so you can submit answers";
        }

        return 'invalid params';
    }

    public function QuizMonitoringPage($id)
    {
        $quiz_info = QuizService::GetMonitoringInfo($id, false);

        return view('monitor_quiz.monitor-page', ['quiz' => $quiz_info]);
    }


    public function GetQuizPhase(Request $request)
    {
        if ($request->has('quiz_id')) {
            if (Auth::user() !== null) {
                $user_id = Auth::user()->u_id;
                $enrolled = QuizService::CheckParticipantQuizEnrollment($user_id, $request->input('quiz_id'));
                if ($enrolled === true) {
                    $result_quiz_phase = QuizService::GetQuizPhase($request->input('quiz_id'));

                    return json_encode(['status' => true, 'message' => 'success', 'phase' => $result_quiz_phase]);

                }
            } else if ($request->session()->get('participant-id') !== null && $request->session()->get('session-id') !== null) {
                $request->session()->flash('participant-id', $request->session()->get('participant-id'));
                $request->session()->flash('session-id', $request->session()->get('session-id'));
                $result_quiz_phase = QuizService::GetQuizPhase($request->input('quiz_id'));

                return json_encode(['status' => true, 'message' => 'success', 'phase' => $result_quiz_phase]);
            }


            return json_encode(['status' => true, 'message' => 'not enrolled', 'phase' => null]);
        }

        return json_encode(['status' => true, 'message' => 'invalid params', 'phase' => null]);
    }

    /**
     * @param Request $request
     * @return false|string
     */
    public function GetQuizRevealAnswersStatus(Request $request)
    {
        if ($request->has('quiz_id')) {
            if (Auth::user() !== null) {

                $user_id = Auth::user()->u_id;

                $enrolled = QuizService::CheckParticipantQuizEnrollment($user_id, $request->input('quiz_id'));

                if ($enrolled === true) {
                    $reveal_answers_status = QuizService::GetQuizRevealAnswersStatus($request->input('quiz_id'));

                    return json_encode(['status' => true, 'message' => 'success', 'reveal_answers_status' => $reveal_answers_status]);

                }
            }

            if ($request->session()->get('participant-id') !== null && $request->session()->get('session-id') !== null) {
                $request->session()->flash('participant-id', $request->session()->get('participant-id'));
                $request->session()->flash('session-id', $request->session()->get('session-id'));
                $reveal_answers_status = QuizService::GetQuizRevealAnswersStatus($request->input('quiz_id'));
                return json_encode(['status' => true, 'message' => 'success', 'reveal_answers_status' => $reveal_answers_status]);
            }
        }
        return json_encode(['status' => true, 'message' => 'invalid params', 'phase' => null]);
    }

    /**
     * @param Request $request
     * @return false|string|null
     */
    public function GetQuizRealTimeInfo(Request $request)
    {
        if ($request->has('quiz_id') && QuizService::CheckQuizOwnership($request->input('quiz_id'), Auth::user()->u_id)) {
            $quiz_info = QuizService::GetMonitoringInfo($request->input('quiz_id'), true);
            $quiz_info->scheduling = QuizService::GetQuizSchedulingInfo($request->input('quiz_id'));
            $quiz_info->status = true;
            return json_encode($quiz_info);
        }
        return json_encode(['status' => false, 'message' => 'missing quiz or user is not the quiz owner', 'quiz_id' => $request->input('quiz_id'), 'ownership' => QuizService::CheckQuizOwnership($request->input('quiz_id'), Auth::user()->u_id)]);
    }

    /**
     * @param Request $request
     * @return false|string
     */
    public function ModifyQuizStatus(Request $request)
    {
        if ($request->has('quiz_id') && $request->has('quiz_status')) {

            if (QuizService::CheckQuizOwnership($request->input('quiz_id'), Auth::user()->u_id)) {

                $result = QuizService::ModifyQuizStatus($request->input('quiz_id'), $request->input('quiz_status'));
                $result['quiz_status'] = $request->input('quiz_status');

                if ($result['status'] === true) {
                    if ($request->input('quiz_status') == Config::get('defines.QUIZ_STATUS_RUNNING')) {
                        $result['message'] =
                            'The quiz has been started.';
                    } else if ($request->input('quiz_status') == Config::get('defines.QUIZ_STATUS_STOPPED')) {
                        $result['message'] =
                            'The quiz has been stopped.';
                    }
                } else if ($request->input('quiz_status') == Config::get('defines.QUIZ_STATUS_RUNNING')) {
                    $result['message'] =
                        'An error occurred while starting the quiz.';
                } else if ($request->input('quiz_status') == Config::get('defines.QUIZ_STATUS_STOPPED')) {
                    $result['message'] =
                        'An error occurred while stopping the quiz.';
                }

                return json_encode($result);
            }

            return json_encode(['status' => false, 'message' => 'quiz not found']);

        }

        return json_encode(['status' => false, 'message' => 'invalid params']);
    }

    public function GetQuizStatus(Request $request)
    {
        if ($request->has('quiz_id') && $request->input('quiz_id') !== null &&
            QuizService::CheckQuizOwnership($request->input('quiz_id'), Auth::user()->u_id)) {
            $info = QuizService::GetQuizSessionInfo($request->input('quiz_id'));

            if ($info !== null) {

                $quiz_phase = $info->phase;
                $reveal_answers = $info->reveal_answers;
                $quiz_status = 1;
                return json_encode(['status' => $quiz_status, 'phase' => $quiz_phase, 'reveal_answers' => $reveal_answers]);

            } else {
                return json_encode(['status' => 2, 'phase' => 1, 'reveal_answers' => 0]);

            }
        }
        return json_encode(['status' => null]);
    }

    public function ModifyQuizActivePhase(Request $request)
    {
        if ($request->has('quiz_id') && $request->has('quiz_phase')) {

            if (QuizService::CheckQuizOwnership($request->input('quiz_id'), Auth::user()->u_id)) {

                $result = QuizService::ModifyQuizActivePhase($request->input('quiz_id'), $request->input('quiz_phase'));

                return json_encode($result);
            }

            return json_encode(['status' => false, 'message' => 'quiz not found']);

        }

        return json_encode(['status' => false, 'message' => 'invalid params']);
    }

    /**
     * @param Request $request
     * @return false|string
     */
    public
    function ModifyQuizRevealAnswersStatus(Request $request)
    {
        if ($request->has('quiz_id') && $request->has('quiz_reveal_answers_status')) {

            if (QuizService::CheckQuizOwnership($request->input('quiz_id'), Auth::user()->u_id)) {

                $result = QuizService::ModifyQuizRevealAnswersStatus($request->input('quiz_id'), $request->input('quiz_reveal_answers_status'));

                return json_encode($result);
            }

            return json_encode(['status' => false, 'message' => 'quiz not found']);

        }

        return json_encode(['status' => false, 'message' => 'invalid params']);
    }

    public
    function ExportQuizPage(Request $request, $quiz_id)
    {
        if (QuizService::CheckQuizOwnership($quiz_id, Auth::user()->u_id)) {

            $quiz_sessions = QuizService::GetQuizSessions($quiz_id, null);
            $quiz_info = QuizService::GetQuizTitle($quiz_id);
            return view('quizzes.export-quiz-page', ['quiz_id' => $quiz_id, 'sessions' => $quiz_sessions, 'quiz_title' => $quiz_info->title, 'quiz_description' => $quiz_info->description]);
        }

        $request->session()->flash('status', 'fail');
        $request->session()->flash('message', 'Quiz not found.');
        return Redirect::route('quizzes');
    }

    public
    function ExportQuizAction(Request $request)
    {
        $show_correctness = false;
        $exclude_incomplete = false;

        if ($request->has('show_correctness') && $request->input('show_correctness') === 'on') {
            $show_correctness = true;
        }

        if ($request->has('exclude_incomplete') && $request->input('exclude_incomplete') === 'on') {
            $exclude_incomplete = true;
        }
        if ($request->has('session_id')) {
            $quiz_id = QuizService::GetQuizIdFromSession($request->input('session_id'));
            if ($quiz_id !== null && QuizService::CheckQuizOwnership($quiz_id, Auth::user()->u_id)) {

                $class = ClassService::GetClassBySession($request->input('session_id'));
                if ($class !== null) {
                    $class->started_at = str_replace(' ', '_', $class->started_at);
                    $class->class_name = str_replace(' ', '_', $class->class_name);
                    $class->quiz_title = str_replace(' ', '_', $class->quiz_title);
                    if ($class->class_name == '') {
                        $file_name = substr($class->quiz_title, 0, 30) . '_' . $class->started_at;
                    } else {
                        $file_name = substr($class->class_name, 0, 30) . ' - ' . substr($class->quiz_title, 0, 30) . '_' . $class->started_at;

                    }
                    return Excel::download(new QuizSessionExport($request->input('session_id'), $show_correctness, $exclude_incomplete), $file_name . ".xlsx");

                }


            }

            $request->session()->flash('status', 'fail');
            $request->session()->flash('message', 'Quiz not found.');
            return Redirect::route('quizzes');
        }

        $request->session()->flash('status', 'fail');
        $request->session()->flash('message', 'Session id not found.');
        return Redirect::route('quizzes');

    }

    /**
     * @param $quiz_id
     * @param Request $request
     * @param null $progress_id
     * @return Factory|RedirectResponse|View
     */
    public
    function ResultsPage($quiz_id, Request $request, $progress_id = null)
    {
        if (Auth::user() !== null) {
            $layout = 'layouts.app';
            $user_id = Auth::user()->u_id;
            $enrolled = QuizService::CheckParticipantQuizEnrollment($user_id, $quiz_id);
            if ($enrolled) {
                $quiz_participants_sessions = QuizService::GetParticipantSessions($user_id, $quiz_id);
                if (count($quiz_participants_sessions) > 0) {

                    if ($progress_id === null) {
                        $progress_id = $quiz_participants_sessions[0]->progress_id;
                        $session_id = $quiz_participants_sessions[0]->session_id;
                    } else {
                        $session_id = QuizService::GetParticipantProgressSessionId($progress_id);
                    }
                } else {
                    return view('quizzes.participant-quiz-result', ['quiz_info' => null, 'results' => null, 'session_data' => null]);
                }
                $session_data = json_decode(QuizService::GetSessionQuizData($session_id), true);
                $session_data = QuizService::GetParticipantResponsesBySession($progress_id, $session_data, $session_id);
                $quiz_info = QuizService::GetQuizTitle($quiz_id);
                return view('quizzes.participant-quiz-result', ['quiz_info' => $quiz_info, 'session_id' => $session_id, 'session_data' => $session_data, 'sessions' => $quiz_participants_sessions, 'progress_id' => $progress_id, 'layout' => $layout]);
            }
        } else if ($request->session()->get('participant-id') !== null && $request->session()->get('session-id') !== null) {
            $layout = 'layouts.app-no-menu';

            //for anonymous participant

            $session_data = json_decode(QuizService::GetSessionQuizData($request->session()->get('session-id')), true);
            $session_data = QuizService::GetParticipantResponsesBySession($progress_id, $session_data, $request->session()->get('session-id'));
            $quiz_info = QuizService::GetQuizTitle($quiz_id);
            return view('quizzes.participant-quiz-result', ['quiz_info' => $quiz_info, 'session_id' => $request->session()->get('session-id'), 'session_data' => $session_data, 'sessions' => null, 'progress_id' => $progress_id, 'layout' => $layout]);

        }
        $request->session()->flash('status', 'fail');
        $request->session()->flash('message', 'Quiz not found.');
        return Redirect::route('login');
    }

    public
    function ExtendQuizScheduling(Request $request)
    {
        if ($request->has('quiz_id') && $request->has('phase') && $request->has('minutes_amount')) {
            $result = QuizService::ExtendQuizScheduling($request->input('quiz_id'), $request->input('phase'), $request->input('minutes_amount'));
        } else {
            $result = ['status' => false, 'message' => 'invalid-params'];
        }
        return json_encode($result);

    }

    public
    function QuizSessionPresentation(Request $request, $session_id)
    {
        $quiz_id = QuizService::GetQuizIdFromSession($session_id);
        if ($quiz_id != null && Auth::user() !== null && QuizService::CheckQuizOwnership($quiz_id, Auth::user()->u_id)) {
            $quiz_data = QuizService::GetQuizPresentationInfo($session_id);
            return view('quizzes.quiz-presentation', ['quiz' => $quiz_data]);
        } else {
            $request->session()->flash('status', 'fail');
            $request->session()->flash('message', 'Quiz not found.');
            return Redirect::route('home');
        }
    }

    public
    function CRONAutomaticQuizScheduling(Request $request)
    {
        $result = QuizService::AutomaticQuizScheduling();
        return $result;
    }

    public
    function GetSessionScores(Request $request, $session_id)
    {

        $quiz_id = QuizService::GetQuizIdFromSession($session_id);
        $quiz = QuizService::GetQuizTitle($quiz_id);
        $filename = '- Quiz Results.xlsx';
        if ($quiz !== null) {
            $filename = $quiz->title . $filename;
        }
        return Excel::download(new QuizResultsSessionExport($session_id), $filename);

    }

    public
    function GamificationPage(Request $request)
    {
        $user_id = Auth::user()->u_id;

        if(UserService::GetUserRole($user_id) >= Config::get('defines.TEACHER_ROLE')) {
        return view('quizzes.quiz-game');
        }
        else {
            $request->session()->flash('status', 'fail');
            $request->session()->flash('message', 'Page not available.');
            return Redirect::route('home');
        }
    }

    public function GamificationRun(Request $request)
    {
        if ($request->input('quizzes_ids') !== null && is_array($request->input('quizzes_ids'))
            && $request->input('group_names') !== null && is_array($request->input('group_names'))) {
            $user_id = Auth::user()->u_id;
            $response = QuizService::GetQuizzesResponses($request->input('quizzes_ids'), $user_id);

            if (is_array($response) && isset($response['value']) && count($response['value']) > 0) {
                //its important to keep the order of first come first assigned
                foreach ($request->input('group_names') as $index => $group_name) {
                    $response['value'][$index]['group_name'] = $group_name;
                    //add questions count to calculate total possible responses
                    if (is_array($response['value'][$index]) && isset($response['value'][$index]['id'])) {
                        $quiz_info = QuizService::GetQuizInfo($response['value'][$index]['id']);
                        if ($quiz_info !== null) {
                            $response['value'][$index]['questions_count'] = count($quiz_info->questions);

                        }
                    } else {
                        $request->session()->flash('status', 'fail');
                        $request->session()->flash('message', 'An error occurred while fetching quizzes\'s responses.');
                        return Redirect::route('quiz-game-page');
                    }
                }
                if ($response['status'] == true) {
                    return view('quizzes.quiz-game-run', ['responses' => $response]);
                } else {
                    $request->session()->flash('status', 'fail');
                    $request->session()->flash('message', 'An error occurred while fetching quizzes\'s responses.');
                    return Redirect::route('quizzes');
                }
            } else {
                $request->session()->flash('status', 'fail');
                $request->session()->flash('message', 'Missing quiz ids parameters');
                return Redirect::route('quiz-game-page');
            }
        } else {
            $request->session()->flash('status', 'fail');
            $request->session()->flash('message', 'An error occurred while fetching quizzes\'s responses.');
            return Redirect::route('quizzes');
        }
    }

    public function GetQuizSessionResponses(Request $request)
    {
        if ($request->has('quizzes_ids') && is_array($request->input('quizzes_ids'))) {
            $user_id = Auth::user()->u_id;
            $response = QuizService::GetQuizzesResponses($request->input('quizzes_ids'), $user_id);
            return json_encode($response);
        } else {
            return json_encode(['status' => false]);
        }
    }
}
