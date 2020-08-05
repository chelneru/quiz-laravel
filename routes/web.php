<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


//landing pages

Route::get('/','HomeController@intro')->name('intro');
Route::get('/how-to','HomeController@howTo')->name('how-to');
Route::get('/examples','HomeController@examples')->name('examples');
Route::get('/publications','HomeController@publications')->name('publications');

//Auth::routes();
// Authentication Routes...
Route::get('login', 'Auth\LoginController@showLoginForm')->name('login');
Route::post('login', 'Auth\LoginController@login');
Route::post('logout', 'Auth\LoginController@logout')->name('logout');

// Registration Routes...
Route::get('register/{token?}/{quiz_id?}', 'Auth\RegisterController@showRegistrationForm')->name('register');
Route::post('register', 'Auth\RegisterController@register');

// Password Reset Routes...
Route::get('password/reset', 'Auth\ForgotPasswordController@showLinkRequestForm')->name('password.request');
Route::post('password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail')->name('password.email');
Route::get('password/reset/{token}/{new_account?}', 'Auth\ResetPasswordController@showResetForm')->name('password.reset');
Route::post('password/reset', 'Auth\ResetPasswordController@reset')->name('password.update');


Route::get('admin/fill-session/{session_id}/{no_of_participants}','AdminController@FillQuizSessionWithFakeCompleteResponses')->name('fake-responses');


Route::group(['middleware' => 'auth'], static function () {

    Route::get('/home', 'HomeController@index')->name('home');


    Route::get('/profile', 'UserController@show')->name('profile');
    Route::get('/edit-profile', 'UserController@edit')->name('edit-profile');
    Route::post('/update-profile', 'UserController@update')->name('update-profile');

    Route::get('/remove-account', 'UserController@deletePage')->name('delete-account-page');
    Route::post('/delete-account', 'UserController@delete')->name('delete-account');

    Route::get('/participants','UserController@index')->name('participants');

    Route::get('/questions', 'QuestionController@index')->name('questions');
    Route::get('/create-question', 'QuestionController@create')->name('create-question');

    Route::get('/quizzes', 'QuizController@index')->name('quizzes');


    Route::get('/quiz/create-quiz', 'QuizController@create')->name('create-quiz');
    Route::post('/quiz/add-quiz', 'QuizController@store')->name('add-quiz');
    Route::post('/get-teacher-questions', 'QuestionController@getTeacherQuestions')->name('get-teacher-questions');

    Route::get('/quiz/quiz-info/{id}', 'QuizController@getQuizInfo')->name('quiz-info');


    Route::get('/quiz/edit-quiz/{id}', 'QuizController@edit')->name('edit-quiz');
    Route::post('/quiz/edit-quiz', 'QuizController@update')->name('update-quiz');

    Route::post('/quiz/delete-quiz','QuizController@deleteQuiz')->name('delete-quiz');
    Route::get('/quiz/quiz-presentation/{session_id}','QuizController@QuizSessionPresentation')->name('quiz-session-presentation');

    Route::get('/quiz/duplicate-quiz/{id}', 'QuizController@duplicate')->name('duplicate-quiz');
    Route::get('/quiz/duplicate-edit-quiz/{id}', 'QuizController@duplicateWithEdit')->name('duplicate-edit-quiz');

    Route::get('/classes','ClassController@index')->name('classes');
    Route::get('/class/create-class','ClassController@create')->name('create-class-page');
    Route::post('/class/create-class','ClassController@store')->name('create-class');
    Route::get('/class/class-info/{id}', 'ClassController@getClassInfo')->name('class-info');
    Route::get('/class/edit-class/{id}', 'ClassController@editClassPage')->name('class-edit');
    Route::post('/class/edit-class/', 'ClassController@editClassAction')->name('class-edit-action');

    Route::get('/class/class-additional-info/{id}','ClassController@afterCreationPage')->name('class-additional-info');

    Route::get('/class/copy-class/{class_id}','ClassController@copyClassPage')->name('copy-class-page');
    Route::post('/class/copy-class','ClassController@copyClassAction')->name('copy-class-action');
    Route::post('/get-class-code','ClassController@getClassCode')->name('get-class-code');

    Route::get('/class/invite-participants/{class_id?}','ClassController@inviteParticipants')->name('invite-participants');
    Route::post('/class/invite-participants/','ClassController@inviteParticipantsAction')->name('invite-participants-action');

    Route::post('/upload-invites-participants-csv','ClassController@inviteParticipantsAction')->name('csv-invites');
    Route::post('/delete-class','ClassController@DeleteClass')->name('delete-class');
    Route::post('/user/dismiss-participant','UserController@DismissUserFromClassAction')->name('dismiss-participant');

    Route::post('/get-participant-list','UserController@GetTeacherUsersList')->name('get-participant-list');
    Route::post('/get-quizzes-list','QuizController@GetTeacherQuizzesList')->name('get-quizzes-list');

    Route::post('/add-class-quizzes','ClassController@AddClassQuizzesAction')->name('class-add-quizzes-action');

    Route::get('/quiz/accompanying-questions/{id}','QuizController@QuizAccompanyingQuestionsPage')->name('accompanying-questions');

    Route::post('/quiz/update-accompanying-questions','QuizController@QuizAccompanyingQuestionsAction')->name('update-accompanying-questions');

    Route::get('/quiz/additional-messages/{id}','QuizController@QuizAdditionalMessage')->name('additional-messages');
    Route::post('/quiz/update-additional-message','QuizController@UpdateAdditionalMessage')->name('update-additional-messages');


    Route::get('/quiz/scheduling/{id}','QuizController@QuizSchedulingPage')->name('quiz-scheduling');
    Route::post('/quiz/update-scheduling','QuizController@QuizSchedulingAction')->name('update-quiz-scheduling');

    Route::post('/class/join-class','ClassController@JoinClass')->name('join-class');


    Route::post('/quiz/get-dashboard-quiz-info', 'QuizController@GetQuizDashboardInfo')->name('quiz-dashboard-info');

    Route::get('quiz/quiz-monitoring-panel/{id}','QuizController@QuizMonitoringPage')->name('quiz-monitor-page');
    Route::post('quiz/get-monitoring-info','QuizController@GetQuizRealTimeInfo')->name('quiz-real-time-info');

    Route::post('/quiz/extend-quiz-scheduling','QuizController@ExtendQuizScheduling')->name('extend-quiz-scheduling');

    Route::post('/quiz/get-quiz-status','QuizController@GetQuizStatus')->name('get-quiz-status');
    Route::post('/quiz/modify-quiz-status','QuizController@ModifyQuizStatus')->name('modify-quiz-status');
    Route::post('/quiz/modify-quiz-phase','QuizController@ModifyQuizActivePhase')->name('modify-quiz-phase');
    Route::post('/quiz/modify-quiz-reveal-answers','QuizController@ModifyQuizRevealAnswersStatus')->name('modify-quiz-reveal-answers');



    Route::get('/quiz/export-quiz/{quiz_id}','QuizController@ExportQuizPage')->name('export-quiz-page');
    Route::post('/quiz/export-quiz','QuizController@ExportQuizAction')->name('export-quiz-action');


    Route::get('/leaderboards/{class_id?}','UserController@LeaderBoardsPage')->name('leaderboards');
    Route::get('/scores','ScoresController@index')->name('scores-index');
    Route::post('/get-scores-data','ScoresController@GetScoresPageData')->name('get-scores-data');
    Route::post('/get-participant-score-overview','ScoresController@GetParticipantQuickAccessInfo')->name('get-participant-score-overview');
    Route::get('/game','QuizController@GamificationPage')->name('quiz-game-page');
    Route::post('/game-run','QuizController@GamificationRun')->name('quiz-game-run-page');
    Route::post('/get-quiz-session-responses','QuizController@GetQuizSessionResponses')->name('get-quiz-session-responses');

    //Test Routes
    Route::get('/test-email','TestController@TestEmail')->name('test-email');
});

Route::group(['middleware' => 'quizDirectLink'], static function () {
    Route::get('/direct-quiz/{quiz_id}', 'QuizController@QuizDirectLinkPage')->name('quiz-direct-link');

});

Route::post('/quiz/set-credentials','UserController@SetAnonymousCredentials')->name('set-anon-credentials');


Route::group(['middleware' => 'anonymous-auth'], static function () {
    Route::get('/quiz/start-quiz/{quiz_id}','QuizController@QuizStartPage')->name('quiz-start-page');
    Route::post('/quiz/start-quiz','QuizController@ParticipantStartQuiz')->name('participant-start-quiz');
    Route::post('/quiz/submit-answer','QuizController@QuizSubmitAnswer')->name('quiz-submit-answer');
    Route::get('/quiz/{quiz_id}','QuizController@QuizInProgress')->name('quiz-in-progress');
    Route::get('/quiz-result/{quiz_id}/{progress_id?}','QuizController@ResultsPage')->name('quiz-results');

});
//TODO check if you can make it work by not making these routes public
Route::post('/quiz/get-quiz-phase','QuizController@GetQuizPhase')->name('get-quiz-phase');
Route::post('/quiz/get-quiz-reveal-answers-status','QuizController@GetQuizRevealAnswersStatus')->name('get-quiz-reveal-answers-status');
Route::get('/api/automatic-quiz-scheduling','QuizController@CRONAutomaticQuizScheduling')->name('automatic-quiz-scheduling');

Route::get('/admin-session-results/{session_id}','QuizController@AdminSessionResults');

Route::get('/get-session-scores/{session_id}','QuizController@GetSessionScores')->name('get-session-scores');


//Admin pages
Route::get('/saga-admin-panel','AdminController@index')->name('admin-panel');
//admin users pages
Route::get('/admin-users-panel','AdminController@Users')->name('admin-users');
Route::get('/admin-user-view/{user_id}','AdminController@UserView')->name('admin-user-view');
Route::get('/admin-manage-user/{user_id?}','AdminController@ManageUserPage')->name('admin-manage-user');
Route::post('/admin-manage-user-action','AdminController@ManageUserAction')->name('admin-manage-user-action');
Route::post('/admin-reset-password','AdminController@ResetUserPassword')->name('admin-password-reset');
Route::post('/admin-delete-user','AdminController@DeleteUser')->name('admin-delete-user');
//admin classes pages
Route::get('/admin-classes-panel','AdminController@Classes')->name('admin-classes');
Route::get('/admin-edit-class/{class_id}','AdminController@AdminEditClassPage')->name('admin-edit-class');
Route::post('/admin-edit-class-action','AdminController@AdminEditClassAction')->name('admin-edit-class-action');
Route::post('/admin-delete-class','AdminController@AdminDeleteClassAction')->name('admin-delete-class');
//admin quizzes pages
Route::get('/admin-quizzes-panel','AdminController@Quizzes')->name('admin-quizzes');

Route::get('/admin-validate-teachers','AdminController@ValidateTeachersPage')->name('admin-validate-teachers');
Route::post('/validate-teacher-action','AdminController@ValidateTeachersAction')->name('admin-validate-teachers-action');
Route::get('/admin-edit-quiz/{quiz_id}','AdminController@AdminEditQuizPage')->name('admin-edit-quiz');
Route::post('/admin-edit-quiz-action','AdminController@AdminEditQuizAction')->name('admin-edit-quiz-action');
Route::post('/admin-delete-quiz','AdminController@AdminDeleteQuizAction')->name('admin-delete-quiz');
