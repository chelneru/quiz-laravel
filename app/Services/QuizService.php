<?php
/**
 * Created by PhpStorm.
 * User: au614698
 * Date: 12-11-2018
 * Time: 13:02
 */

namespace App\Services;

use App\Repositories\Quiz\QuizRepository;
use Carbon\Carbon;
use Config;
use DB;
use Debugbar;
use Exception;
use Log;
use Redirect;
use stdClass;

class QuizService
{


    /**
     * @param $quiz_name
     * @param $quiz_questions
     * @param $user_id
     * @param $class_id
     * @param $allow_anon
     * @param $is_assessed
     * @param string $quiz_desc
     * @param bool $create_from_duplication
     * @return array
     * @throws Exception
     */
    public static function CreateNewQuizWithQuestions($quiz_name, $quiz_desc, $quiz_questions, $user_id, $class_id,
                                                      $allow_anon, $is_assessed, $create_from_duplication)
    {
        $validation_result = self::ValidateQuiz($quiz_name, $user_id);
        if ($validation_result['status']) {
            $result = QuizRepository::CreateNewQuizWithQuestions($quiz_name, $quiz_desc, $quiz_questions, $user_id, $class_id,
                $allow_anon, $is_assessed, $create_from_duplication);


        } else {
            $result = ['status' => false, 'message' => 'invalid-quiz'];
        }
        return $result;
    }

    private static function ValidateQuiz($quiz_name, $user_id)
    {
        $message = '';
        $valid = true;

        if ($quiz_name == '' || $quiz_name == null) {
            $valid = false;
            $message = 'quiz-invalid-text';
        } else if (!is_numeric($user_id)) {
            $valid = false;
            $message = 'quiz-invalid-user-id';
        }

        return ['status' => $valid, 'message' => $message];
    }

    public static function GetTeacherQuizzes($user_id, $class_id = null)
    {
        $result = QuizRepository::GetTeacherQuizzes($user_id, $class_id);

        return $result;
    }

    public static function GetParticipantQuizzes($user_id, $paginate = false)
    {
        $result = QuizRepository::GetParticipantQuizzes($user_id, $paginate);
        return $result;
    }

    public static function GetDashboardTeacherQuizzes($user_id, $class_id = null)
    {
        $result = QuizRepository::GetDashboardTeacherQuizzes($user_id, $class_id);

        return $result;
    }

    public static function GetTeacherQuizzesDropdown($user_id, $class_id = null)
    {
        $result = QuizRepository::GetTeacherQuizzesDropdown($user_id, $class_id);

        return $result;
    }


    public static function GetQuizInfo($quiz_id)
    {
        $result = QuizRepository::GetQuizInfo($quiz_id);
        return $result;
    }

    public static function GetQuizIdFromSession($session_id)
    {
        return QuizRepository::GetQuizIdFromSession($session_id);
    }

    public static function UpdateQuiz($quiz_id, $quiz_text, $questions, $u_id, $class_id, $allow_anon, $is_assessed, $quiz_description)
    {
        $quiz_validation_result = self::ValidateQuiz($quiz_text, $u_id);
        if ($quiz_validation_result['status'] == false) {
            return $quiz_validation_result;
        }
        if (is_array($questions)) {

            foreach ($questions as $question) {


                $answers_validation_result = QuestionAnswerService::ValidateQuestionAnswer($question['question_answers']);
                if ($answers_validation_result['status'] == false) {
                    return $answers_validation_result;
                }
            }
        } else {
            $answers_validation_result = QuestionAnswerService::ValidateQuestionAnswer($questions['question_answers']);
            if ($answers_validation_result['status'] == false) {
                return $answers_validation_result;
            }
        }
        $result = QuizRepository::UpdateQuiz($quiz_id, $quiz_text, $questions, $u_id, $class_id, $allow_anon, $is_assessed, $quiz_description);

        return $result;
    }

    public static function DuplicateQuiz($user_id, $quiz_id, $date_title = true, $preserve_class = true)
    {
        $quiz = self::GetQuizInfo($quiz_id);

        if ($quiz !== null) {
            //convert from object to assoc. array
            $questions_array = json_decode(json_encode($quiz->questions), True);

            $today = Carbon::now('Europe/Copenhagen')->toDateString();

            if ($date_title) {
                $new_quiz_title = $quiz->title . ' - Copy ' . $today;

            } else {
                $new_quiz_title = $quiz->title;

            }
            $accompanying_questions = self::GetQuizAccompanyingQuestions($quiz_id);
            try {
                DB::beginTransaction();
                $result = self::CreateNewQuizWithQuestions($new_quiz_title,
                    $quiz->description,
                    $questions_array,
                    $user_id,
                    $preserve_class === true ? $quiz->class_id : false,
                    $quiz->allow_anon, $quiz->is_assessed,true);

                if (isset($result['result']) && $result['result']['status'] === true) {
                    $question_ids = $result['question_ids'];
                    $result = $result['result'];
                    foreach ($accompanying_questions as $accompanying_question) {
                        $acc_result = ['status' => false];
                        switch ($accompanying_question->type) {
                            case 1:
                                $accompanying_question_object = [
                                    'question_title' => $accompanying_question->title,
                                    'question_type' => $accompanying_question->structure,
                                    'question_explanation' => $accompanying_question->explanation,
                                    'question_options' => $accompanying_question->options->toArray(),
                                ];
                                $index = 1;
                                foreach ($accompanying_question_object['question_options'] as &$option) {
                                    $new_option = ['text' => $option->text, 'index' => $index];
                                    $option = $new_option;
                                    $index++;
                                }
                                unset($option);
                                $acc_result = self::UpdateQuizPrepQuestion($result['quiz_id'], $accompanying_question_object);
                                break;
                            case 2:
                                $accompanying_question_object = [
                                    'question_title' => $accompanying_question->title,
                                    'question_type' => $accompanying_question->structure,
                                    'question_explanation' => $accompanying_question->explanation,
                                    'question_options' => $accompanying_question->options->toArray(),
                                ];
                                $index = 1;

                                foreach ($accompanying_question_object['question_options'] as &$option) {
                                    $new_option = ['text' => $option->text, 'index' => $index];
                                    $option = $new_option;
                                    $index++;
                                }
                                unset($option);

                                $accompanying_question_object['init_ph_display'] = $accompanying_question->feedback;
                                $accompanying_question_object['rev_ph_display'] = $accompanying_question->positions;

                                //update the questions ids with the new question ids
                                foreach ($accompanying_question_object['init_ph_display'] as &$question_id) {
                                    $question_id = $question_ids[$question_id];
                                }
                                unset($question_id);
                                foreach ($accompanying_question_object['rev_ph_display'] as &$question_id) {
                                    $question_id = $question_ids[$question_id];
                                }
                                unset($question_id);

                                $acc_result = self::UpdateQuizConfQuestion($result['quiz_id'], $accompanying_question_object);
                                break;
                            case 3:
                                $accompanying_question_object = [
                                    'question_title' => $accompanying_question->title,
                                    'question_type' => $accompanying_question->type,
                                    'question_explanation' => $accompanying_question->explanation,

                                ];
                                $accompanying_question_object['init_ph_display'] = $accompanying_question->feedback;
                                $accompanying_question_object['rev_ph_display'] = $accompanying_question->positions;
                                //update the questions ids with the new question ids
                                foreach ($accompanying_question_object['init_ph_display'] as &$question_id) {
                                    $question_id = $question_ids[$question_id];
                                }
                                unset($question_id);
                                foreach ($accompanying_question_object['rev_ph_display'] as &$question_id) {
                                    $question_id = $question_ids[$question_id];
                                }
                                unset($question_id);
                                $acc_result = self::UpdateQuizJustQuestion($result['quiz_id'], $accompanying_question_object);

                                break;
                            case 4:
                                $accompanying_question_object = [
                                    'question_title' => $accompanying_question->title,
                                    'question_name' => $accompanying_question->name,
                                    'question_type' => $accompanying_question->structure,
                                    'question_position' => $accompanying_question->position,
                                    'question_explanation' => $accompanying_question->explanation,
                                    'initial_phase_display' => $accompanying_question->init_display,
                                    'revision_phase_display' => $accompanying_question->rev_display,
                                ];
                                if ($accompanying_question_object['question_position'] == 2) {
                                    $accompanying_question_object['init_ph_display'] = $accompanying_question->feedback;
                                    $accompanying_question_object['rev_ph_display'] = $accompanying_question->positions;

                                    //update the questions ids with the new question ids
                                    foreach ($accompanying_question_object['init_ph_display'] as &$question_id) {
                                        $question_id = $question_ids[$question_id];
                                    }
                                    unset($question_id);
                                    foreach ($accompanying_question_object['rev_ph_display'] as &$question_id) {
                                        $question_id = $question_ids[$question_id];
                                    }
                                    unset($question_id);
                                }
                                if ($accompanying_question_object['question_type'] == 2) {
                                    $accompanying_question_object['question_options'] = $accompanying_question->options->toArray();
                                    $index = 1;

                                    foreach ($accompanying_question_object['question_options'] as &$option) {
                                        $new_option = ['text' => $option->text, 'index' => $index];
                                        $option = $new_option;
                                        $index++;
                                    }
                                    unset($option);

                                }
                                $acc_result = QuizRepository::CreateNewOtherAccompanyingQuestion($accompanying_question_object, $result['quiz_id']);
                                break;
                        }
                        if ($acc_result['status'] == false) {
                            DB::rollback();
                            return $acc_result;
                        }
                    }

                    //duplicate starting message
                    $starting_message = self::GetQuizMessage($quiz_id);
                    if ($starting_message !== null) {
                        $result_starting_message = self::UpdateAdditionalMessage($result['quiz_id'], $starting_message->message, $starting_message->message_title);
                    }
                    //duplicate scheduling
                    $quiz_scheduling = self::GetQuizSchedulingInfo($quiz_id);
                    if ($quiz_scheduling !== null) {
                        if ($quiz_scheduling->active == 1) {
                            $result_quiz_scheduling = self::AddQuizScheduling($result['quiz_id'], $quiz_scheduling->init_start,
                                $quiz_scheduling->init_end,
                                $quiz_scheduling->rev_start,
                                $quiz_scheduling->rev_end,
                                $quiz_scheduling->ans_start,
                                $quiz_scheduling->ans_end
                            );
                        }
                        self::SetQuizParticipationLimit($result['quiz_id'], $quiz_scheduling->participation_limit);

                    }

                } else {
                    DB::rollback();
                    return $result;

                }
                DB::commit();
                return ['status' => true, 'message' => 'success', 'quiz_id' => $result['quiz_id']];

            } catch (\Exception $e) {
                DB::rollback();
                return ['status' => false, 'message' => $e->getMessage() . ' in file: ' . $e->getFile() . ' at line: ' . $e->getLine()];
            }
        } else {
            return ['status' => false, 'message' => 'quiz not found'];

        }
    }

    public static function DeleteQuiz($quiz_id, $just_unlink)
    {
        return QuizRepository::DeleteQuiz($quiz_id, $just_unlink);
    }

    public static function CheckQuizOwnership($quiz_id, $user_id)
    {

        $user_role = UserService::GetUserRole($user_id);

        if ($user_role == null) {
            return false;
        }

        $quiz_result = QuizRepository::GetQuizCreator($quiz_id);
        $result = true;
        if (count($quiz_result) == 0) {
            return false;
        }
        foreach ($quiz_result as $quiz) {
            if (($quiz_id === null || $user_role < 2 || $quiz->user_id != $user_id)) {
                $result = false;

            }
        }
        return $result;
    }

    public static function GetQuizTitle($quiz_id)
    {
        return QuizRepository::GetQuizTitle($quiz_id);
    }


    public static function UpdateQuizAccompanyingQuestions($quiz_id, $acc_question, $acc_questions_type)
    {
        $result = ['status' => true, 'message' => 'success'];

        if ($acc_question !== null) {
            switch ($acc_questions_type) {
                case 1:
                    $result = self::UpdateQuizPrepQuestion($quiz_id, $acc_question);
                    break;
                case 2:
                    if (!isset($acc_question['init_ph_display'])) {
                        $acc_question['init_ph_display'] = [];
                    }
                    if (!isset($acc_question['rev_ph_display'])) {
                        $acc_question['rev_ph_display'] = [];
                    }
                    $result = self::UpdateQuizConfQuestion($quiz_id, $acc_question);
                    break;
                case 3:
                    if (!isset($acc_question['init_ph_display'])) {
                        $acc_question['init_ph_display'] = [];
                    }
                    if (!isset($acc_question['rev_ph_display'])) {
                        $acc_question['rev_ph_display'] = [];
                    }
                    $result = self::UpdateQuizJustQuestion($quiz_id, $acc_question);
                    break;
                case 4:
                    foreach ($acc_question as $other_question) {
                        if ($other_question['question_position'] == 2) {
                            if (!isset($other_question['init_ph_display'])) {
                                $other_question['init_ph_display'] = [];
                            }
                            if (!isset($other_question['rev_ph_display'])) {
                                $other_question['rev_ph_display'] = [];
                            }
                        }
                    }
                    $result = self::UpdateQuizOtherQuestions($quiz_id, $acc_question);
                    break;
            }
        } else {
            //check if there was a previous acc question and now it has been removed
            $acc_question_ids = QuizRepository::CheckQuizHasAccompanyingQuestion($quiz_id, $acc_questions_type);

            if (count($acc_question_ids) > 0) {
                $result = QuizRepository::RemoveAccompanyingQuestion($acc_question_ids, $acc_questions_type);
            }
        }

        return $result;
    }

    private static function UpdateQuizPrepQuestion($quiz_id, $prep_question)
    {

        $validation = self::ValidatePrepQuestion($prep_question);

        if ($validation['status'] == true && is_numeric($quiz_id)) {

            $result = QuizRepository::UpdateQuizPrepQuestion($quiz_id, $prep_question);

            return $result;
        } else {
            return ['status' => false, 'message' => 'prep-acc-question-validation-failed'];
        }
    }

    private static function UpdateQuizConfQuestion($quiz_id, $conf_question)
    {
        $validation = self::ValidateConfQuestion($conf_question);

        if ($validation['status'] == true && is_numeric($quiz_id)) {

            $result = QuizRepository::UpdateQuizConfQuestion($quiz_id, $conf_question);

            return $result;
        } else {
            return $validation;
        }
    }

    private static function UpdateQuizJustQuestion($quiz_id, $just_question)
    {
        $validation = self::ValidateJustQuestion($just_question);

        if ($validation['status'] == true && is_numeric($quiz_id)) {

            $result = QuizRepository::UpdateQuizJustQuestion($quiz_id, $just_question);

            return $result;
        } else {
            return ['status' => false, 'message' => 'just-acc-question-validation-failed'];
        }
    }

    private static function UpdateQuizOtherQuestions($quiz_id, $other_questions)
    {
        $result = null;
        if (is_array($other_questions) && is_numeric($quiz_id)) {

            foreach ($other_questions as $other_question) {

                $validation = self::ValidateQuizOtherQuestion($other_question);

                if ($validation['status'] != true) {

                    return ['status' => false, 'message' => 'other-acc-question-validation-failed ' . $validation['message']];

                }
            }
            $result = QuizRepository::UpdateQuizOtherQuestion($quiz_id, $other_questions);

            if ($result['status'] != true) {
                return $result;
            }
            return $result;
        } else {
            return ['status' => false, 'message' => 'other-acc-question-invalid-format'];

        }


    }

    private static function ValidatePrepQuestion($prep_question)
    {
        //validate input
        if ($prep_question['question_title'] == '' || $prep_question['question_title'] == null) {
            $status = false;
            $message = 'missing-prep-question-title';
            return ['status' => $status, 'message' => $message];
        }

        if ($prep_question['question_type'] == '' || $prep_question['question_type'] == null || !is_numeric($prep_question['question_type'])) {
            $status = false;
            $message = 'missing-prep-question-type';
            return ['status' => $status, 'message' => $message];

        }
        if (($prep_question['question_type'] == '1' || $prep_question['question_type'] == '2') &&
            (!is_array($prep_question['question_options']) && count($prep_question['question_options']) < 1)) {
            $status = false;
            $message = 'missing-prep-question-options';
            return ['status' => $status, 'message' => $message];

        }
        $options_count = count($prep_question['question_options']);
        for ($ans_iter = 0; $ans_iter < $options_count; $ans_iter++) {
            if ($prep_question['question_options'][$ans_iter]['text'] === null || $prep_question['question_options'][$ans_iter]['text'] === '') {
                $message = 'invalid-prep-question-' . ($ans_iter + 1) . '-option';
                $status = false;
                return ['status' => $status, 'message' => $message];

            }
        }
        return ['status' => true, 'message' => 'valid'];

    }

    private static function ValidateConfQuestion($conf_question)
    {
        //validate input
        if ($conf_question['question_title'] == '' || $conf_question['question_title'] == null) {
            $status = false;
            $message = 'missing-conf-question-title';
            return ['status' => $status, 'message' => $message];
        }

        if ($conf_question['question_type'] == '' || $conf_question['question_type'] == null || !is_numeric($conf_question['question_type'])) {
            $status = false;
            $message = 'missing-conf-question-type';
            return ['status' => $status, 'message' => $message];

        }
        if (($conf_question['question_type'] == '1' || $conf_question['question_type'] == '2') &&
            (!is_array($conf_question['question_options']) && count($conf_question['question_options']) < 1)) {
            $status = false;
            $message = 'missing-conf-question-options';
            return ['status' => $status, 'message' => $message];

        }

        $options_count = count($conf_question['question_options']);
        for ($ans_iter = 0; $ans_iter < $options_count; $ans_iter++) {
            if ($conf_question['question_options'][$ans_iter]['text'] == null || $conf_question['question_options'][$ans_iter]['text'] == '') {
                $message = 'invalid-conf-question-' . ($ans_iter + 1) . '-option';
                $status = false;
                return ['status' => $status, 'message' => $message];

            }
        }

        if (isset($conf_question['init_ph_display']) && is_array($conf_question['init_ph_display']) && count($conf_question['init_ph_display']) > 0) {
            $init_display_questions_count = count($conf_question['init_ph_display']);
            for ($feed_iter = 0; $feed_iter < $init_display_questions_count; $feed_iter++) {
                if ($conf_question['init_ph_display'][$feed_iter] == null || $conf_question['init_ph_display'][$feed_iter] == '') {
                    $message = 'invalid-conf-question-' . ($feed_iter + 1) . '-feedback-question';
                    $status = false;
                    return ['status' => $status, 'message' => $message];
                }
            }
        }

        if (isset($conf_question['rev_ph_display']) && is_array($conf_question['rev_ph_display']) && count($conf_question['rev_ph_display']) > 0) {
            $erv_display_questions_count = count($conf_question['rev_ph_display']);
            for ($feed_iter = 0; $feed_iter < $erv_display_questions_count; $feed_iter++) {
                if ($conf_question['rev_ph_display'][$feed_iter] == null || $conf_question['rev_ph_display'][$feed_iter] == '') {
                    $message = 'invalid-conf-question-' . ($feed_iter + 1) . '-feedback-location';
                    $status = false;
                    return ['status' => $status, 'message' => $message];
                }
            }
        }


        return ['status' => true, 'message' => 'valid'];

    }

    private static function ValidateJustQuestion($just_question)
    {
        //validate input

        if ($just_question['question_title'] == '' || $just_question['question_title'] == null) {
            $status = false;
            $message = 'missing-just-question-title';
            return ['status' => $status, 'message' => $message];
        }

        if ($just_question['question_type'] == '' || $just_question['question_type'] == null || !is_numeric($just_question['question_type'])) {
            $status = false;
            $message = 'missing-just-question-type';
            return ['status' => $status, 'message' => $message];
        }

        if (isset($just_question['init_ph_display']) && is_array($just_question['init_ph_display']) && count($just_question['init_ph_display']) > 0) {
            $init_display_questions_count = count($just_question['init_ph_display']);
            for ($feed_iter = 0; $feed_iter < $init_display_questions_count; $feed_iter++) {
                if ($just_question['init_ph_display'][$feed_iter] == null || $just_question['init_ph_display'][$feed_iter] == '') {
                    $message = 'invalid-just-question-' . ($feed_iter + 1) . '-feedback-question';
                    $status = false;
                    return ['status' => $status, 'message' => $message];
                }
            }
        }

        if (isset($just_question['rev_ph_display']) && is_array($just_question['rev_ph_display']) && count($just_question['rev_ph_display']) > 0) {
            $erv_display_questions_count = count($just_question['rev_ph_display']);
            for ($feed_iter = 0; $feed_iter < $erv_display_questions_count; $feed_iter++) {
                if ($just_question['rev_ph_display'][$feed_iter] == null || $just_question['rev_ph_display'][$feed_iter] == '') {
                    $message = 'invalid-just-question-' . ($feed_iter + 1) . '-feedback-location';
                    $status = false;
                    return ['status' => $status, 'message' => $message];
                }
            }
        }

        return ['status' => true, 'message' => 'valid'];
    }

    private static function ValidateQuizOtherQuestion($other_question)
    {
        //validate input

        if ($other_question['question_title'] == '' || $other_question['question_title'] == null) {
            $status = false;
            $message = 'missing-other-question-title';
            return ['status' => $status, 'message' => $message];
        }

        if ($other_question['question_name'] == '' || $other_question['question_name'] == null) {
            $status = false;
            $message = 'missing-other-question-name';
            return ['status' => $status, 'message' => $message];
        }

        if ($other_question['question_type'] == '' || $other_question['question_type'] == null || !is_numeric($other_question['question_type'])) {
            $status = false;
            $message = 'missing-other-question-type';
            return ['status' => $status, 'message' => $message];
        }

        if (($other_question['question_type'] == '1' || $other_question['question_type'] == '2') &&
            (!is_array($other_question['question_options']) && count($other_question['question_options']) < 1)) {
            $status = false;
            $message = 'missing-other-question-options';
            return ['status' => $status, 'message' => $message];

        }
        if ($other_question['question_position'] == '' || $other_question['question_position'] == null || !is_numeric($other_question['question_position'])) {
            $status = false;
            $message = 'missing-other-question-position';
            return ['status' => $status, 'message' => $message];
        }

        if ($other_question['question_type'] == '1') {
            $options_count = count($other_question['question_options']);
            for ($ans_iter = 0; $ans_iter < $options_count; $ans_iter++) {
                if ($other_question['question_options'][$ans_iter]['text'] == null || $other_question['question_options'][$ans_iter]['text'] == '') {
                    $message = 'invalid-other-question-' . ($ans_iter + 1) . '-option';
                    $status = false;
                    return ['status' => $status, 'message' => $message];
                }
            }
        } else if ($other_question['question_type'] == '2') {
            $options_count = count($other_question['question_options']);

            for ($ans_iter = 0; $ans_iter < $options_count; $ans_iter++) {
                if ($other_question['question_options'][$ans_iter]['text'] == null ||
                    $other_question['question_options'][$ans_iter]['text'] == '' ||
                    !is_numeric($other_question['question_options'][$ans_iter]['text'])) {
                    $message = 'invalid-other-question-' . ($ans_iter + 1) . '-option';
                    $status = false;
                    return ['status' => $status, 'message' => $message];
                }
            }
        }


        if ($other_question['question_position'] == "2") {
            //we have feedback and locations option only if this accompanying questions is inside the quiz

            if (isset($other_question['init_ph_display'])) {
                if (is_array($other_question['init_ph_display']) && count($other_question['init_ph_display']) > 0) {
                    $init_display_questions_count = count($other_question['init_ph_display']);
                    for ($feed_iter = 0; $feed_iter < $init_display_questions_count; $feed_iter++) {
                        if ($other_question['init_ph_display'][$feed_iter] == null || $other_question['init_ph_display'][$feed_iter] == '') {
                            $message = 'invalid-other-question-' . ($feed_iter + 1) . '-feedback-question';
                            $status = false;
                            return ['status' => $status, 'message' => $message];
                        }
                    }
                }
            }
            if (isset($other_question['rev_ph_display'])) {

                if (is_array($other_question['rev_ph_display']) && count($other_question['rev_ph_display']) > 0) {
                    $rev_display_questions_count = count($other_question['rev_ph_display']);
                    for ($feed_iter = 0; $feed_iter < $rev_display_questions_count; $feed_iter++) {
                        if ($other_question['rev_ph_display'][$feed_iter] == null || $other_question['rev_ph_display'][$feed_iter] == '') {
                            $message = 'invalid-other-question-' . ($feed_iter + 1) . '-feedback-location';
                            $status = false;
                            return ['status' => $status, 'message' => $message];
                        }
                    }
                }
            }
            //we have phase display option only if this accompanying questions is inside the quiz
            if (!checkBool($other_question['initial_phase_display']) || !checkBool($other_question['revision_phase_display'])) {
                $message = 'invalid-other-question-phase-display';
                $status = false;
                return ['status' => $status, 'message' => $message];
            }
        }

        return ['status' => true, 'message' => 'valid'];
    }

    public static function GetQuizAccompanyingQuestions($id)
    {
        return QuizRepository::GetQuizAccompanyingQuestions($id);
    }

    public static function UpdateAdditionalMessage($quiz_id, $message, $message_title)
    {
        return QuizRepository::UpdateAdditionalMessage($quiz_id, $message, $message_title);
    }

    public static function GetQuizMessage($id)
    {
        return QuizRepository::GetQuizMessage($id);
    }

    public static function GetSessionQuizData($session_id)
    {
        return QuizRepository::GetSessionQuizData($session_id);
    }

    public static function GetParticipantCurrentQuizInformation($user_id, $quiz_id, $unfinished = false)
    {
        return QuizRepository::GetParticipantCurrentQuizInformation($user_id, $quiz_id, $unfinished);
    }

    public static function GetAnonParticipantCurrentQuizInformation($participant_id, $session_id, $unfinished = false)
    {
        return QuizRepository::GetAnonParticipantCurrentQuizInformation($participant_id, $session_id, $unfinished);

    }

    public static function GetQuizPreparationQuestion($quiz_id)
    {
        return QuizRepository::GetQuizPreparationQuestion($quiz_id);
    }

    public static function GetQuizOtherQuestionsCount($acc_questions, $position): int
    {
        $count = 0;
        foreach ($acc_questions as $question) {
            if ($question['type'] == 4 && $question['other_question_position'] == $position) {
                $count++;
            }
        }
        return $count;
    }

    public static function CheckParticipantQuizEnrollment($user_id, $quiz_id)
    {
        $user_session_quiz_id = session('enrolled-quiz-id', null);
        if ($user_session_quiz_id == $quiz_id) {
            return true;
        }

        return QuizRepository::CheckParticipantQuizEnrollment($user_id, $quiz_id);
    }

    public static function StartQuizProgress($user_id, $quiz_id)
    {
        //create quiz progress entry for participant
        //check if there is previous progress
        $existing_progress = self::GetParticipantCurrentQuizInformation($user_id, $quiz_id, true);
        if ($existing_progress === null) {
            $quiz_sessions = self::GetQuizSessions($quiz_id);
            $session_id = null;
            if ($quiz_sessions->total() > 0) {
                $session_id = $quiz_sessions[0]->id;
            }

            if ($session_id !== null) {
                $session_data = self::GetSessionQuizData($session_id);

                if ($session_data !== null) {
                    $session_data = json_decode($session_data, true);

                    $start_index = self::GetQuizStartIndex($quiz_id, $session_data);

                    $result = QuizRepository::StartQuizProgress($user_id, $quiz_id, $start_index);
                    if ($result['status'] === true) {
                        self::AddParticipantScore($user_id, Config::get('defines.SCORE_START_QUIZ'));
                        session()->forget('enrolled-quiz-id');

                        return ['status' => true, 'message' => 'success', 'start' => true];
                    }
                }
            }
            return ['status' => false, 'message' => 'unable to get session data', 'start' => true, 'reason' => null];
        }

        //we have progress for the logged in user
        return ['status' => true, 'message' => 'success', 'start' => true];
    }

    public static function StartAnonQuizProgress($participant_id, $session_id)
    {

        if ($session_id !== null) {
            $session_data = self::GetSessionQuizData($session_id);

            if ($session_data !== null) {

                $session_data = json_decode($session_data, true);
                $start_index = self::GetQuizStartIndex($session_data['id'], $session_data);

                $result = QuizRepository::StartAnonQuizProgress($participant_id, $session_data['id'], $start_index);
                return $result;

            }
        }
        return ['status' => false, 'message' => 'error while starting the quiz'];
    }

    /**
     * get question from quiz data by question index
     * @param $question_index
     * @param $quiz_data
     * @param $is_revision_phase
     * @param null $session_id
     * @return null
     */
    public static function GetQuizQuestionFromData($question_index, $quiz_data, $is_revision_phase, $session_id = null)
    {
        $current_index = 1;

        foreach ($quiz_data['questions'] as &$question) {
            if ($current_index == $question_index) {
                $question['index'] = $current_index;
                if ($is_revision_phase == true) {
                    $total_responses = self::GetQuestionInitialPhaseResponses($session_id, $question['id']);

                    foreach ($question['answers'] as $key => &$answer) {
                        $answer_responses = self::GetQuestionAnswerInitialPhaseResponses($session_id, $question['id'], $key + 1);
                        if ($total_responses == 0) {
                            $answer['responses_percentage'] = 0;
                        } else {
                            $answer['responses_percentage'] = $answer_responses / $total_responses * 100;
                        }
                    }
                }
                return $question;
            }
            $current_index++;
        }
        return null;
    }

    public static function GetQuizAccQuestionFromData($index, $quiz_data,$phase)
    {
        $no_of_questions = self::GetQuizQuestionsCount($quiz_data);

        if ($index < 0) {
            //we need a 'before' acc question
            //determine how many before questions we have
            $no_of_before_questions = 0;
            foreach ($quiz_data['acc_questions'] as $question) {
                if ($question['type'] == 4 && $question['other_question_position'] == 1) {
                    $no_of_before_questions++;
                }
            }

            $index = $no_of_before_questions + $index + 1;
            $current_index = 1;

            foreach ($quiz_data['acc_questions'] as $question) {
                if ($question['type'] == 4 && $question['other_question_position'] == 1) {
                    if ($current_index == $index) {
                        return $question;
                    }
                    $current_index++;
                }
            }
        } else if ($index > $no_of_questions && $phase == Config::get('defines.QUIZ_REVISION_PHASE')) {
            //we need an after question  (only if we are in the second phase)

            $current_index = 1;

            $index -= $no_of_questions;
            foreach ($quiz_data['acc_questions'] as $question) {
                if ($question['type'] == 4 && $question['other_question_position'] == 3) {
                    if ($current_index == $index) {
                        return $question;
                    }
                    $current_index++;
                }
            }

        }
        return null;
    }

    public static function GetDashboardQuizInfo($quiz_id)
    {
        return QuizRepository::GetDashboardQuizInfo($quiz_id);
    }

    public static function GetQuizStatus($quiz_id)
    {
        return QuizRepository::GetQuizStatus($quiz_id);
    }

    public static function GetQuizSessionInfo($quiz_id) {
        return QuizRepository::GetQuizSessionInfo($quiz_id);
    }
    public static function UpdateQuizProgress($participant_id, $quiz_id, $index, $phase, $displayed_message, $finished)
    {
        if ($finished == 1) {
            self::AddParticipantScore($participant_id, Config::get('defines.SCORE_FINISH_QUIZ'));
        }
        return QuizRepository::UpdateQuizProgress($participant_id, $quiz_id, $index, $phase, $displayed_message, $finished);
    }

    public static function GetQuestionAccompanyingQuestions($quiz_data, $session_id, $question_id, $is_revision_phase)
    {
        $result_acc_questions = $quiz_data['acc_questions'];
        if ($is_revision_phase == true) {
            //add responses for each answer

            foreach ($result_acc_questions as &$acc_question) {
                if ($acc_question['type'] == 3) {
                    //we have just question
                    $acc_question['responses'] = QuizRepository::GetAccQuestionTextResponsesFromInitialPhase($session_id, $question_id, $acc_question['id']);
                } else if ($acc_question['type'] == 1) {
                    // we have prep question
                    $acc_question['responses'] = QuizRepository::GetAccQuestionAverageResponsesFromInitialPhase($session_id, $question_id, $acc_question['id'], true);
                } else if ($acc_question['type'] == 2) {
                    // we have conf question
                    $acc_question['responses'] = QuizRepository::GetAccQuestionAverageResponsesFromInitialPhase($session_id, $question_id, $acc_question['id'], false);
                } else if ($acc_question['type'] == 4) {
                    // we have other question
                    $no_responses_associated = $acc_question['other_question_position'] == 1 || $acc_question['other_question_position'] == 3;
                    if ($acc_question['structure'] == 3) {
                        // we have other question with text responses
                        $acc_question['responses'] = QuizRepository::GetAccQuestionTextResponsesFromInitialPhase($session_id, $question_id, $acc_question['id'], $no_responses_associated);
                    } else {
                        // we have other question with options responses
                        $acc_question['responses'] = QuizRepository::GetAccQuestionAverageResponsesFromInitialPhase($session_id, $question_id, $acc_question['id'], $no_responses_associated);

                    }
                }
            }
            unset($acc_question);
        }
        return $result_acc_questions;
    }


    public static function GetAccompanyingQuestionDefaults()
    {
        $defaults = new stdClass();
        $defaults->prep_question = new stdClass();
        $defaults->prep_question->id = null;
        $defaults->prep_question->title = 'How much time did you spend preparing for today\'s class?';
        $defaults->prep_question->explanation = 'Use a scale from "1: Not  at all" to "5: I have read it thoroughly". Please, remember that all your responses are anonymous and will not affect your assessment in this course.';
        $defaults->prep_question->type = 1;
        $defaults->prep_question->structure = 2;
        $defaults->prep_question->options = [(object)['id' => null, 'text' => 1], (object)['id' => null, 'text' => 2], (object)['id' => null, 'text' => 3], (object)['id' => null, 'text' => 4], (object)['id' => null, 'text' => 5]];

        $defaults->conf_question = new stdClass();
        $defaults->conf_question->id = null;
        $defaults->conf_question->title = 'How confident are you that you have selected the correct answer?';
        $defaults->conf_question->explanation = 'The average confidence score of participants that selected each option.';
        $defaults->conf_question->type = 2;
        $defaults->conf_question->structure = 2;
        $defaults->conf_question->options = [(object)['id' => null, 'text' => 1], (object)['id' => null, 'text' => 2], (object)['id' => null, 'text' => 3], (object)['id' => null, 'text' => 4], (object)['id' => null, 'text' => 5]];
        $defaults->conf_question->feedback = null;
        $defaults->conf_question->positions = null;

        $defaults->just_question = new stdClass();
        $defaults->just_question->id = null;
        $defaults->just_question->title = 'Please justify your answer';
        $defaults->just_question->explanation = 'The justifications of participants that selected each option.';
        $defaults->just_question->type = 3;
        $defaults->just_question->structure = 3;
        $defaults->just_question->options = null;
        $defaults->just_question->feedback = null;
        $defaults->just_question->positions = null;


        return $defaults;
    }

    public static function GetQuizQuestionsCount($quiz_data): int
    {
        if (isset($quiz_data['questions'])) {
            return count($quiz_data['questions']);
        }
        return 0;
    }

    public static function GetMonitoringInfo($quiz_id, $is_update)
    {
        $quiz_data = QuizRepository::GetMonitoringInfo($quiz_id,$is_update);
        if ($quiz_data !== null) {
            if ($quiz_data->phase !== null) {
                //quiz has a session started
                $quiz_data = self::PrepareResponsesForMonitoringPage($quiz_data,$is_update);
            }
            $quiz_data->scheduling = self::GetQuizSchedulingInfo($quiz_id);

            if ($is_update === false) {
                //this is page load event so we also need the initial information about the quiz
                //check the case when the quiz is stopped, it has scheduling, and the last phase end date is already past
                if ($quiz_data->status === 2 &&
                    $quiz_data->scheduling !== null &&
                    $quiz_data->scheduling->active === 1 &&
                    strtotime($quiz_data->scheduling->ans_end) < strtotime(date('m/d/Y h:i:s a', time()))) {
                    $quiz_data->overdue_start = true;
                } else {
                    $quiz_data->overdue_start = false;
                }
            }
        }
        return $quiz_data;
    }

    public static function ModifyQuizStatus($quiz_id, $quiz_status)
    {

            return QuizRepository::ModifyQuizStatus($quiz_id, $quiz_status);

    }

    public static function ModifyQuizActivePhase($quiz_id, $quiz_phase)
    {
        return QuizRepository::ModifyQuizActivePhase($quiz_id, $quiz_phase);

    }

    public static function ModifyQuizRevealAnswersStatus($quiz_id, $quiz_reveal_answers_status)
    {
        return QuizRepository::ModifyQuizRevealAnswersStatus($quiz_id, $quiz_reveal_answers_status);

    }

    private static function PrepareResponsesForMonitoringPage($quiz,$is_update)
    {
        $new_quiz_object = new stdClass();
        $new_quiz_object->questions = [];
        $new_quiz_object->id = $quiz->id;
        $new_quiz_object->session_id = $quiz->session_id;
        if($is_update === false) {
        $new_quiz_object->title = $quiz->title;
            $new_quiz_object->status = $quiz->status;
            $new_quiz_object->reveal_answers = $quiz->reveal_answers;

        }
        $new_quiz_object->phase = $quiz->phase;

        $new_quiz_object->active_participants_count = $quiz->active_participants_count;
        $new_quiz_object->active_anon_participants_count = $quiz->active_anon_participants_count;
        $new_quiz_object->enrolled_participants_count = $quiz->enrolled_participants_count;
        //prepare the regular questions

        foreach ($quiz->responses as $response) {
            foreach ($quiz->questions as &$question) {
                if (!isset($question['responses'])) {
                    $question['responses'] = [];
                    $question['responses'][1] = [];
                    $question['responses'][2] = [];
                }
                //create arrays for each possible answer of a question
                if (isset($question['answers'])) {

                foreach ($question['answers'] as $index => $answer) {
                    if (!isset($question['responses'][1]['answers_values'][$index + 1])) {
                        $question['responses'][1]['answers_values'][$index + 1] = 0;

                    }

                    if (!isset($question['responses'][2]['answers_values'][$index + 1])) {
                        $question['responses'][2]['answers_values'][$index + 1] = 0;

                    }

                }
            }
                if (!isset($question['responses'][1]['total_responses'])) {
                    $question['responses'][1]['total_responses'] = 0;
                }

                if (!isset($question['responses'][2]['total_responses'])) {
                    $question['responses'][2]['total_responses'] = 0;
                }


                if ($response->question_id == $question['id']) {
                    //initialize/update total responses field
                    $question['responses'][$response->phase]['total_responses']++;

                    //update answer responses field
                    if (isset($question['responses'][$response->phase]['answers_values'][$response->answer_index])) {
                        $question['responses'][$response->phase]['answers_values'][$response->answer_index]++;
                    } else {
                        $question['responses'][$response->phase]['answers_values'][$response->answer_index] = 1;
                    }

                }
                if($is_update === false) {
                //reorder answers for good display
                if (isset($question['responses'][1]['answers_values'])) {
                    $question['responses'][1]['answers_values'] = array_values($question['responses'][1]['answers_values']);
                    array_unshift($question['responses'][1]['answers_values'], 0);
                    unset($question['responses'][1]['answers_values'][0]);
                }

                if (isset($question['responses'][2]['answers_values'])) {
                    $question['responses'][2]['answers_values'] = array_values($question['responses'][2]['answers_values']);
                    array_unshift($question['responses'][2]['answers_values'], 0);
                    unset($question['responses'][2]['answers_values'][0]);
                }

                }
            }
        }
        unset($question);

        $new_quiz_object->questions = $quiz->questions;
        //acc questions
        foreach ($quiz->acc_questions as $acc_question) {
            $acc_question = json_decode(json_encode($acc_question), 1);
            $acc_question['responses'] = [];
            foreach ($quiz->acc_questions_responses as &$acc_response) {
                $acc_response = json_decode(json_encode($acc_response), 1);

                if ($acc_response['acc_question_id'] == $acc_question['id']) {

                    if ($acc_question['structure'] != 3) {
                        //we have answers choices

                        if ($acc_question['type'] != 1 &&
                            !($acc_question['type'] == 4 && ($acc_question['other_question_position'] == 1 || $acc_question['other_question_position'] == 3) && $acc_question['structure'] == 2)) {
                            //anything except prep question or other question outside of quiz with choices
                            if (isset($acc_question['responses'][$acc_response['phase']][$acc_response['question_id']][$acc_response['q_answer_index']][$acc_response['answer_index']])) {
                                $acc_question['responses'][$acc_response['phase']][$acc_response['question_id']][$acc_response['q_answer_index']][$acc_response['answer_index']]++;
                            } else {
                                $acc_question['responses'][$acc_response['phase']][$acc_response['question_id']][$acc_response['q_answer_index']][$acc_response['answer_index']] = 1;
                            }
                        }
                    } else {
                        //we have text responses inside quiz (just question or other question)

                        if ($acc_question['type'] == 4 && ($acc_question['other_question_position'] == 1 || $acc_question['other_question_position'] == 3)) {
                            //outside other questions are text so we dont need to make any modifications.
                            continue;
                        }
                        if (isset($acc_question['responses'][$acc_response['question_id']][$acc_response['q_answer_index']])) {
                            $acc_question['responses'][$acc_response['question_id']][$acc_response['q_answer_index']][] = $acc_response['answer_content'];
                        } else {
                            $acc_question['responses'][$acc_response['question_id']][$acc_response['q_answer_index']] = [$acc_response['answer_content']];
                        }
                    }

                }


            }
            unset($acc_response);

            switch ($acc_question['type']) {
                case 1:
                    $new_quiz_object->prep_question = [];
                    foreach ($quiz->acc_questions_responses as $acc_response) {
                        if ($acc_response['response_id'] == null && $acc_question['type'] == 1 && $acc_response['acc_question_id'] == $acc_question['id']) {

                            //prep question response
                            if (isset($new_quiz_object->prep_question[$acc_response['answer_index']])) {
                                $new_quiz_object->prep_question[$acc_response['answer_index']]++;
                            } else {
                                $new_quiz_object->prep_question[$acc_response['answer_index']] = 1;
                            }
                        }
                    }

                    break;
                case 2:
                    //prepare conf_question
                    $conf_question = [];

                    foreach ($new_quiz_object->questions as $question) {
                        foreach ($acc_question['responses'] as $phase => $question_id) {
                                foreach ($question_id as $question_id_key => $answers_index) {

                                    $total_correct = 0;
                                    $total_correct_count = 0;
                                    $total_incorrect = 0;
                                    $total_incorrect_count = 0;

                                    //get correct index
                                    if ($question_id_key == $question['id']) {
                                        foreach ($answers_index as $answer => $conf_answers) {

                                            if ($answer == $question['right_answer']) {
                                                foreach ($conf_answers as $conf_answer_index => $conf_answer) {
                                                    $total_correct += $conf_answer_index * $conf_answer;
                                                    $total_correct_count += $conf_answer;

                                                }
                                            } else {
                                                foreach ($conf_answers as $conf_answer_index => $conf_answer) {
                                                    $total_incorrect += $conf_answer_index * $conf_answer;
                                                    $total_incorrect_count += $conf_answer;

                                                }
                                            }

                                        }

                                        $total_all_count = $total_correct_count + $total_incorrect_count;
                                        $total_all = $total_correct + $total_incorrect;
                                        $conf_question['responses'][$phase][$question_id_key] =
                                            [
                                                'all_average' => $total_all_count != 0 ? $total_all / $total_all_count : 0,
                                                'correct_average' => $total_correct_count != 0 ? $total_correct / $total_correct_count : 0,
                                                'incorrect_average' => $total_incorrect_count != 0 ? $total_incorrect / $total_incorrect_count : 0
                                            ];

                                        break;
                                    }
                                }

                        }
                    }

                    $new_quiz_object->conf_question = $conf_question;
                    break;
                case 3:

                    $new_quiz_object->just_question = $acc_question;
                    break;
                case 4:
                    $new_other_question = $acc_question;

                    if ($acc_question['structure'] == 2 && ($acc_question['other_question_position'] == 1 || $acc_question['other_question_position'] == 3)) {
                        //prepare it as for the prep question
                        $new_other_question['view_type'] = 'outside-rating';

                        foreach ($quiz->acc_questions_responses as $acc_response) {
                            if ($acc_response['response_id'] == null && $acc_question['type'] == 4 && $acc_response['acc_question_id'] == $acc_question['id']) {
                                //prep question response
                                if (isset($new_other_question['responses'][$acc_response['answer_index']])) {
                                    $new_other_question['responses'][$acc_response['answer_index']]++;
                                } else {
                                    $new_other_question['responses'][$acc_response['answer_index']] = 1;
                                }
                            }
                        }


                    } elseif ($acc_question['structure'] == 3 && ($acc_question['other_question_position'] == 1 || $acc_question['other_question_position'] == 3)) {
                        //something simpler than justification
                        $new_other_question['responses'] = [];
                        $new_other_question['view_type'] = 'outside-text';
                        foreach ($quiz->acc_questions_responses as $acc_response) {
                            if ($acc_response['response_id'] == null && $acc_question['type'] == 4 && $acc_response['acc_question_id'] == $acc_question['id']) {
                                $new_other_question['responses'][] = $acc_response['answer_content'];
                            }
                        }
//                        if($acc_question['id'] == 93) {
//                        dd($new_other_question,$quiz->acc_questions_responses);
//                        }
                    } elseif ($acc_question['structure'] == 2 && $acc_question['other_question_position'] == 2) {
                        //prepare it the same as the confidence question
                        $new_other_question['view_type'] = 'inside-rating';

                        foreach ($new_quiz_object->questions as $question) {

                            foreach ($acc_question['responses'] as $phase => $question_id) {
                                foreach ($question_id as $question_id_key => $answers_index) {
//                                    $total_all = 0;
//                                    $total_all_count = 0;
                                    $total_correct = 0;
                                    $total_correct_count = 0;
                                    $total_incorrect = 0;
                                    $total_incorrect_count = 0;

                                    //get correct index
                                    if ($question_id_key == $question['id']) {
                                        foreach ($answers_index as $answer => $other_question_answers) {

                                            if ($answer == $question['right_answer']) {
                                                foreach ($other_question_answers as $other_question_answer_index => $other_question_answer) {
                                                    $total_correct += $other_question_answer_index * $other_question_answer;
                                                    $total_correct_count += $other_question_answer;

                                                }
                                            } else {
                                                foreach ($other_question_answers as $other_question_answer_index => $other_question_answer) {
                                                    $total_incorrect += $other_question_answer_index * $other_question_answer;
                                                    $total_incorrect_count += $other_question_answer;

                                                }
                                            }

                                        }

                                        $total_all_count = $total_correct_count + $total_incorrect_count;
                                        $total_all = $total_correct + $total_incorrect;
                                        $new_other_question['responses'][$phase][$question_id_key] =
                                            [
                                                'all_average' => $total_all_count != 0 ? $total_all / $total_all_count : 0,
                                                'correct_average' => $total_correct_count != 0 ? $total_correct / $total_correct_count : 0,
                                                'incorrect_average' => $total_incorrect_count != 0 ? $total_incorrect / $total_incorrect_count : 0
                                            ];
                                        break;
                                    }
                                }

                            }
                        }

                    } elseif ($acc_question['structure'] == 3 && $acc_question['other_question_position'] == 2) {
                        //prepare it the same as the justification question
                        $new_other_question = $acc_question;
                    }
                    if (isset($new_quiz_object->other_questions)) {
                        $new_quiz_object->other_questions[] = $new_other_question;
                    } else {
                        $new_quiz_object->other_questions = [$new_other_question];
                    }
                    break;
            }
        }
        return $new_quiz_object;
    }

    /**
     * @param $quiz
     * @return stdClass
     */

    public static function GetQuestionAnswerQuestion($question_id, $quiz_data, $answer_id, $is_acc_question)
    {
        if ($is_acc_question == false) {
            foreach ($quiz_data['questions'] as $question) {
                if ($question['id'] == $question_id) {
                    foreach ($question['answers'] as $index => $answer) {
                        if ($answer['id'] == $answer_id) {
                            return ['index' => $index + 1, 'content' => $answer['text']];
                        }
                    }
                    break;
                }
            }
        } else {
            foreach ($quiz_data['acc_questions'] as $acc_question) {
                if ($acc_question['id'] == $question_id) {
                    foreach ($acc_question['answers'] as $index => $answer) {
                        if ($answer['id'] == $answer_id) {
                            return ['index' => $index + 1, 'content' => $answer['text']];
                        }
                    }

                }
            }
        }
        return null;
    }

    public static function GetAccQuestionStructure($question_id, $quiz_data)
    {

        foreach ($quiz_data['acc_questions'] as $acc_question) {
            if ($acc_question['id'] == $question_id) {
                return $acc_question['structure'];
            }

        }

        return null;
    }

    public static function GetQuestionRightAnswer($question_id, $quiz_data)
    {
        foreach ($quiz_data['questions'] as $question) {
            if ($question['id'] == $question_id) {
                return $question['right_answer'];
            }
        }
    }

    /**
     * @param $session_id
     * @param $phase
     * @param $question_id
     * @param $answer_id
     * @param $conf_question_id
     * @param $conf_answer_id
     * @param $just_question_id
     * @param $just_answer_content
     * @param $other_question_ids
     * @param $other_question_answers_indexes
     * @param $other_question_answers_content
     * @param $duration
     * @param $participant_id
     * @param $quiz_data
     * @return mixed
     */
    public static function ParticipantSubmitQuestionAnswers($session_id, $phase, $question_id, $answer_id,
                                                            $conf_question_id, $conf_answer_id,
                                                            $just_question_id, $just_answer_content,
                                                            $other_question_ids, $other_question_answers_content,
                                                            $duration,
                                                            $participant_id, $quiz_data)
    {
        //submit answer for the main question of the quiz
        if ($question_id !== null && $answer_id !== null) {

            $quiz_data = json_decode($quiz_data, true);
            $is_duplicate_response = self::CheckDuplicateResponse($session_id, $phase, $question_id, $participant_id);
            if ($is_duplicate_response) {
                return ['status' => false, 'message' => 'duplicate-response'];
            }
            $answer_index = self::GetQuestionAnswerQuestion($question_id, $quiz_data, $answer_id, false);
            $correct_answer = 0;
            $correct_answer_index = self::GetQuestionRightAnswer($question_id, $quiz_data);
            if ($correct_answer_index == $answer_index['index']) {
                $correct_answer = 1;
            }

            $response_id = QuizRepository::ParticipantSubmitQuestionResponse($session_id, $phase, $question_id, $answer_index['index'], $duration, $participant_id, $correct_answer);
            self::AddParticipantScore($participant_id, Config::get('defines.SCORE_ANSWER_QUESTION'));
            if ($response_id['status'] == true) {

                if ($correct_answer == 1) {
                    if ($phase == Config::get('defines.QUIZ_REVISION_PHASE')) {
                        self::AddParticipantScore($participant_id, Config::get('defines.SCORE_CORRECT_ANSWER'));
                    }
                }
                $response_id = $response_id['id'];
            } else {
                return $response_id;
            }
        } else {
            $response_id = null;
        }
        //if submitting the answer for the main question has been successful then we submit the accompanying questions's answers
        if ($response_id !== null) {
            //submit conf_question
            if ($conf_question_id !== null && $conf_answer_id !== null) {
                $conf_answer = self::GetQuestionAnswerQuestion($conf_question_id, $quiz_data, $conf_answer_id, true);
                if ($conf_answer !== null) {
                    $result = QuizRepository::ParticipantSubmitAccQuestionResponse($session_id, $response_id, $conf_question_id, $conf_answer['index'], $conf_answer['content'], $phase, 'conf-question', $participant_id);
                    if ($result['status'] == false) {
                        return $result;
                    }
                }
            }
            //submit just_question
            if ($just_question_id !== null && $just_answer_content !== null) {
                $result = QuizRepository::ParticipantSubmitAccQuestionResponse($session_id, $response_id, $just_question_id, null, $just_answer_content, $phase, 'just-question', $participant_id);
                if ($result['status'] == false) {
                    return $result;
                }
            }
            //submit other_questions

            if ($other_question_ids !== null &&
                $other_question_answers_content !== null &&
                count($other_question_ids) > 0 &&
                count($other_question_ids) == count($other_question_answers_content)) {

                $other_question_ids_count = count($other_question_ids);
                for ($iter = 0; $iter < $other_question_ids_count; $iter++) {
                    $structure = self::GetAccQuestionStructure($other_question_ids[$iter], $quiz_data);

                    if ($structure == 3) {
                        //submit answer content since we have participant's text answer
                        $result = QuizRepository::ParticipantSubmitAccQuestionResponse($session_id, $response_id, $other_question_ids[$iter], null, $other_question_answers_content[$iter], $phase, 'other-question', $participant_id);
                    } else {
                        //submit answer index and content since we have participant's choice answer
                        $answer = self::GetQuestionAnswerQuestion($other_question_ids[$iter], $quiz_data, $other_question_answers_content[$iter], true);

                        $result = QuizRepository::ParticipantSubmitAccQuestionResponse($session_id, $response_id, $other_question_ids[$iter], $answer['index'], $answer['content'], $phase, 'other-question', $participant_id);
                    }
                    if ($result['status'] == false) {
                        return $result;
                    }
                }
            }
            return ['status' => true, 'message' => 'success'];
        } else {
            return ['status' => false, 'message' => 'unable to submit response'];

        }

    }

    public static function ParticipantSubmitAccAnswers($session_id, $question_id, $answer_id, $answer_content, $phase, $participant_id, $quiz_data)
    {
        $result = null;
        $quiz_data = json_decode($quiz_data, true);
        if ($question_id !== null) {
            if (self::CheckDuplicateAccQuestionResponse($session_id, $question_id, $participant_id)) {
                return ['status' => false, 'message' => 'duplicate-answer'];
            }
            if ($answer_id !== null) {
                //we have an acc question with options
                $answer = self::GetQuestionAnswerQuestion($question_id, $quiz_data, $answer_id, true);
                if ($answer !== null) {
                    $result = QuizRepository::ParticipantSubmitAccQuestionResponse($session_id, null, $question_id, $answer['index'], $answer_content, $phase, 'prep-question', $participant_id);
                    if ($result['status'] == false) {
                        return $result;
                    }
                }
            } else if ($answer_content !== null) {
                //the answer is text
                $result = QuizRepository::ParticipantSubmitAccQuestionResponse($session_id, null, $question_id, null, $answer_content, $phase, 'other-question', $participant_id);
                if ($result['status'] == false) {
                    return $result;
                }
            }
        }
        return ['status' => true, 'message' => 'success'];
    }

    public static function GetQuizPhase($quiz_id)
    {
        return QuizRepository::GetQuizPhase($quiz_id);
    }

    public static function GetQuizRevealAnswersStatus($quiz_id)
    {
        return QuizRepository::GetQuizRevealAnswersStatus($quiz_id);
    }

    private static function GetQuestionInitialPhaseResponses($session_id, $question_id)
    {
        return QuizRepository::GetQuestionInitialPhaseResponses($session_id, $question_id);

    }

    private static function GetQuestionAnswerInitialPhaseResponses($session_id, $question_id, $answer_index)
    {
        return QuizRepository::GetQuestionAnswerInitialPhaseResponses($session_id, $question_id, $answer_index);
    }

    public static function GetQuizSessions($quiz_id, $is_stopped = null)
    {
        return QuizRepository::GetQuizSessions($quiz_id, $is_stopped);
    }

    public static function GetParticipantResponsesBySession($participant_id, $session_data, $session_id)
    {
        $scores = ['initial' => 0, 'revision' => 0, 'total' => 0];
        $progress = QuizRepository::GetParticipantResults($participant_id, $session_id);
        if ($progress !== null) {
            foreach ($session_data['questions'] as &$question) {
                $scores['total']++;

                foreach ($progress->responses as $response) {
                    if ($response->question_id == $question['id']) {

                        if ($response->phase == 1) {
                            $question['initial_response'] = $response->answer_index;
                            if ($response->answer_index == $question['right_answer']) {
                                $scores['initial']++;
                            }
                        } else if ($response->phase == 2) {

                            $question['revision_response'] = $response->answer_index;
                            if ($response->answer_index == $question['right_answer']) {
                                $scores['revision']++;
                            }
                        }
                    }
                }
            }

            foreach ($progress->sessions as $session) {
                if ($session->session_id = $session_id) {
                    $session_data['tries'] = $session->tries;

                }
            }
            $session_data['scores'] = $scores;

            return $session_data;

        } else {
            return null;
        }
    }

    public static function GetParticipantSessions($user_id, $quiz_id)
    {
        return QuizRepository::GetParticipantSessions($user_id, $quiz_id);
    }

    public static function GetParticipantProgressSessionId($progress_id)
    {
        return QuizRepository::GetParticipantProgressSessionId($progress_id);
    }

    public static function CheckOngoingProgressOnSession($user_id, $session_id)
    {
        return QuizRepository::CheckOngoingProgressOnSession($user_id, $session_id);
    }

    public static function AddParticipantScore($participant_id, $reason)
    {
        QuizRepository::AddParticipantScore($participant_id, $reason);
    }

    private static function GetQuizStartIndex($quiz_id, $quiz_data)
    {
        //determine start index

        $start_index = 0;

        $before_other_questions_count = self::GetQuizOtherQuestionsCount($quiz_data['acc_questions'], 1);
        $start_index -= $before_other_questions_count;
        return $start_index;
    }

    public static function InitialPhaseResponse($session_id, $question_id, $participant_id)
    {
        return QuizRepository::InitialPhaseResponse($session_id, $question_id, $participant_id);
    }

    public static function AddQuizScheduling($quiz_id, $init_ph_start, $init_ph_stop,
                                             $rev_ph_start, $rev_ph_stop, $reveal_ans_start, $reveal_ans_stop): ?array
    {

        return QuizRepository::AddQuizScheduling($quiz_id, $init_ph_start, $init_ph_stop,
            $rev_ph_start, $rev_ph_stop, $reveal_ans_start, $reveal_ans_stop);
    }

    public static function DeactivateQuizScheduling($quiz_id): ?array
    {
        return QuizRepository::DeactivateQuizScheduling($quiz_id);
    }

    public static function ExtendQuizScheduling($quiz_id, $phase, $minutes_amount): ?array
    {
        $current_schedule = self::GetQuizSchedulingInfo($quiz_id);

        if ($minutes_amount < 0) {
            $result = ['status' => false, 'message' => 'invalid-amount'];
            return $result;
        }
        if ($current_schedule !== null) {
            switch ($phase) {
                case 1:
                    //we extend the initial phase  as a result the revision start date is delayed.
                    $current_schedule->init_end = Carbon::parse($current_schedule->init_end)->addMinutes($minutes_amount)->toDateTimeString();

                    $current_schedule->rev_start = Carbon::parse($current_schedule->rev_start)->addMinutes($minutes_amount)->toDateTimeString();


                    //Update the quiz scheduling
                    $result = self::AddQuizScheduling($quiz_id, $current_schedule->init_start, $current_schedule->init_end,
                        $current_schedule->rev_start, $current_schedule->rev_end,
                        $current_schedule->ans_start, $current_schedule->ans_end);

                    break;
                case 2:
                    //we extend the revision phase  as a result the  answers reveal phase start is delayed.
                    $current_schedule->rev_end = Carbon::parse($current_schedule->rev_end)->addMinutes($minutes_amount)->toDateTimeString();

                    $current_schedule->ans_start = Carbon::parse($current_schedule->ans_start)->addMinutes($minutes_amount)->toDateTimeString();
                    //Update the quiz scheduling
                    $result = self::AddQuizScheduling($quiz_id, $current_schedule->init_start, $current_schedule->init_end,
                        $current_schedule->rev_start, $current_schedule->rev_end,
                        $current_schedule->ans_start, $current_schedule->ans_end);
                    break;
                case 3:
                    //we extend the answers reveal phase.
                    $current_schedule->ans_end = Carbon::parse($current_schedule->ans_end)->addMinutes($minutes_amount)->toDateTimeString();
                    //Update the quiz scheduling
                    $result = self::AddQuizScheduling($quiz_id, $current_schedule->init_start, $current_schedule->init_end,
                        $current_schedule->rev_start, $current_schedule->rev_end,
                        $current_schedule->ans_start, $current_schedule->ans_end);
                    break;
                default:
                    //invalid phase, do nothing
                    $result = ['status' => false, 'message' => 'invalid-phase'];
                    break;
            }
            //format the dates for nice looking format
            $current_schedule->init_start = date('d M H:i', strtotime($current_schedule->init_start));
            $current_schedule->init_end = date('d M H:i', strtotime($current_schedule->init_end));

            $current_schedule->rev_start = date('d M H:i', strtotime($current_schedule->rev_start));
            $current_schedule->rev_end = date('d M H:i', strtotime($current_schedule->rev_end));

            $current_schedule->ans_start = date('d M H:i', strtotime($current_schedule->ans_start));
            $current_schedule->ans_end = date('d M H:i', strtotime($current_schedule->ans_end));
            $result['current_values'] = json_decode(json_encode($current_schedule), 1);
        } else {
            //Error no scheduling found
            $result = ['status' => false, 'message' => 'missing-scheduling'];
        }
        return $result;
    }

    public static function GetQuizParticipationLimit($quiz_id): int
    {
        return QuizRepository::GetQuizParticipationLimit($quiz_id);
    }

    public static function SetQuizParticipationLimit($quiz_id, $limit): ?array
    {
        return QuizRepository::SetQuizParticipationLimit($quiz_id, $limit);
    }

    public static function GetQuizSchedulingInfo($id)
    {
        $result = QuizRepository::GetQuizSchedulingInfo($id);
        if ($result !== null) {

            //check if the dates are past and if they are we remove them
//            $result->init_start = (isPast($result->init_start) ? '' : $result->init_start);
//            $result->init_end = (isPast($result->init_end) ? '' : $result->init_end);
//            $result->rev_start = (isPast($result->rev_start) ? '' : $result->rev_start);
//            $result->rev_end = (isPast($result->rev_end) ? '' : $result->rev_end);
//            $result->ans_start = (isPast($result->ans_start) ? '' : $result->ans_start);
//            $result->ans_end = (isPast($result->ans_end) ? '' : $result->ans_end);

            //format the dates for nice looking format

            $result->init_start = PrettyDateFormat($result->init_start);
            $result->init_end = PrettyDateFormat($result->init_end);

            $result->rev_start = PrettyDateFormat($result->rev_start);
            $result->rev_end = PrettyDateFormat($result->rev_end);

            $result->ans_start = PrettyDateFormat($result->ans_start);
            $result->ans_end = PrettyDateFormat($result->ans_end);

        }
        return $result;
    }

    public static function AutomaticQuizScheduling()
    {


        //get quizzes that need to be updated
        $result = ['message' => '', 'status' => true];
        $now = Carbon::now('Europe/Copenhagen')->setSeconds(0)->toDateTimeString();
        //for debug
        Log::channel('quiz-scheduling')->info('checking quizzes at ' . $now);
        $quizzes = QuizRepository::GetQuizzesForAutomaticQuizScheduling($now);
        foreach ($quizzes as $quiz) {
            if ($quiz->init_start == $now) {
                // we have to start the quiz
                try {
                    $result = self::ModifyQuizStatus($quiz->quiz_id, Config::get('defines.QUIZ_STATUS_RUNNING'));
                } catch (Exception $e) {
                    Log::channel('quiz-scheduling')->info('At time ' . $now . ' Failed to start the quiz with the id ' . $quiz->quiz_id . ' ERROR MESSAGE: ' . $e->getMessage() . ' TRACE: ' . $e->getTraceAsString() . ' IN FILE: ' . $e->getFile() . ' AT LINE:' . $e->getLine());
                }
            } else if ($quiz->rev_start == $now) {
                //we have to go to revision phase
                try {
                    $result = self::ModifyQuizActivePhase($quiz->quiz_id, Config::get('defines.QUIZ_REVISION_PHASE'));
                } catch (Exception $e) {
                    Log::channel('quiz-scheduling')->info('At time ' . $now . ' Failed to switch to revision phase at the quiz with the id ' . $quiz->quiz_id . ' ERROR MESSAGE: ' . $e->getMessage() . ' TRACE: ' . $e->getTraceAsString() . ' IN FILE: ' . $e->getFile() . ' AT LINE:' . $e->getLine());
                }
            } else if ($quiz->ans_start == $now) {
                //we have to go to answers reveal phase
                try {
                    $result = self::ModifyQuizRevealAnswersStatus($quiz->quiz_id, 1);
                } catch (Exception $e) {
                    Log::channel('quiz-scheduling')->info('At time ' . $now . ' Failed to switch to answers reveal phase at the quiz with the id ' . $quiz->quiz_id . ' ERROR MESSAGE: ' . $e->getMessage() . ' TRACE: ' . $e->getTraceAsString() . ' IN FILE: ' . $e->getFile() . ' AT LINE:' . $e->getLine());
                }
            } else if ($quiz->ans_end == $now) {
                //we have to go to stop the quiz
                try {
                    $result = self::ModifyQuizStatus($quiz->quiz_id, Config::get('defines.QUIZ_STATUS_STOPPED'));
                } catch (Exception $e) {
                    Log::channel('quiz-scheduling')->info('At time ' . $now . ' Failed to stop the quiz with the id ' . $quiz->quiz_id . ' ERROR MESSAGE: ' . $e->getMessage() . ' TRACE: ' . $e->getTraceAsString() . ' IN FILE: ' . $e->getFile() . ' AT LINE:' . $e->getLine());
                }
            }
        }
        return $result;
    }

    public static function QuizCheckAnonymousParticipation($quiz_id)
    {
        return QuizRepository::QuizCheckAnonymousParticipation($quiz_id);
    }

    public static function GetQuizPresentationInfo($session_id)
    {
        $result = QuizRepository::GetQuizPresentationInfo($session_id);

        if ($result !== null) {

            foreach ($result->questions as &$question) {

                //create arrays for each possible answer of a question
                $answer_index = 1;
                foreach ($question['answers'] as &$answer) {
                    $answer['index'] = $answer_index;
                    if (!isset($answer['init_resp'])) {
                        $answer['init_resp'] = 0;
                    }

                    if (!isset($answer['rev_resp'])) {
                        $answer['rev_resp'] = 0;
                    }
                    $answer_index++;
                }
                unset($answer);
                if (!isset($question['total_responses'][1])) {
                    $question['total_responses'][1] = 0;
                }

                if (!isset($question['total_responses'][2])) {
                    $question['total_responses'][2] = 0;
                }
            }
            unset($question);
            foreach ($result->responses as $response) {

                foreach ($result->questions as &$question) {

                    if ($response->question_id == $question['id']) {
                        //initialize/update total responses field
                        $question['total_responses'][$response->phase]++;

                        //update answer responses field
                        foreach ($question['answers'] as &$answer) {
                            if ($answer['index'] == $response->answer_index) {

                                if ($response->phase == 1) {
                                    if (isset($answer['init_resp'])) {
                                        $answer['init_resp']++;
                                    } else {
                                        $answer['init_resp'] = 1;
                                    }
                                } else {
                                    if (isset($answer['rev_resp'])) {
                                        $answer['rev_resp']++;
                                    } else {
                                        $answer['rev_resp'] = 1;
                                    }
                                }

                            }
                        }
                        unset($answer);


                    }
                }
                unset($question);

            }

            //prepare data for display and check for division by zero
            foreach ($result->questions as &$question) {
                foreach ($question['answers'] as &$answer) {
                    if ($answer['init_resp'] > 0) {
                        $answer['init_resp'] = $answer['init_resp'] / $question['total_responses'][1] * 100;
                    }
                    if ($answer['rev_resp'] > 0) {
                        $answer['rev_resp'] = $answer['rev_resp'] / $question['total_responses'][2] * 100;
                    }
                }
            }
        }


        return $result;
    }

    public static function GetSessionTimestamps(int $session_id)
    {
        $result = DB::table('quiz_session')
            ->select('qs_started_at as start', 'qs_enabled_rev_ph as revision_start',
                'qs_revealed_ans_at as reveal_start', 'qs_stopped_at as stop')
            ->where('qs_id', '=', $session_id)
            ->first();
        return $result;
    }

    private static function CheckDuplicateResponse($session_id, $phase, $question_id, $participant_id)
    {
        return QuizRepository::CheckDuplicateResponse($session_id, $phase, $question_id, $participant_id);
    }

    private static function CheckDuplicateAccQuestionResponse($session_id, $question_id, $participant_id)
    {
        return QuizRepository::CheckDuplicateAccQuestionResponse($session_id, $question_id, $participant_id);
    }

    public static function GetParticipantPastAttendance($session_id, $user_id)
    {
        return QuizRepository::GetParticipantPastAttendance($session_id, $user_id);
    }

    public static function GetScoresPageQuizzes($user_id)
    {
        return QuizRepository::GetScoresPageQuizzes($user_id);
    }

    public static function IsQuizAssessed($id)
    {
        return QuizRepository::IsQuizAssessed($id);
    }

    public static function AccQuestionDisplayInQuestion($acc_question, $question_id, $phase)
    {
        if(!is_array($acc_question['feedback'])) {
            $acc_question['feedback'] = explode(',',$acc_question['feedback']);
        }
        if(!is_array($acc_question['positions'])) {
            $acc_question['positions'] = explode(',',$acc_question['positions']);
        }
        if ($phase == Config::get('defines.QUIZ_INITIAL_PHASE')) {
            return in_array($question_id, $acc_question['feedback'], false);
        }

        if ($phase == Config::get('defines.QUIZ_REVISION_PHASE')) {
            return in_array($question_id, $acc_question['positions'], false);

        }
        return false;
    }

    public static function UpdateQuizTitle($quiz_id, $quiz_title)
    {
        return QuizRepository::UpdateQuizTitle($quiz_id, $quiz_title);
    }

    public static function GetQuizzesResponses($quizzes_ids, $user_id)
    {
        $result = [];
        $result['value'] = [];
        $result['status'] = true;
        $result['message'] = 'success';
        foreach ($quizzes_ids as $quiz_id) {
            //if the teacher created this quiz
            try {
                if (self::CheckQuizOwnership($quiz_id, $user_id)) {
                    $session = self::GetOpenSession($quiz_id);
                    if ($session !== null) {
                        $session_id = $session->qs_id;
                        $responses = QuizRepository::GetSessionResponsesByPhase($session_id);
                        $active_participants = $responses->unique('participant_id')->count();
                        $quiz_info = [
                            'id' => $quiz_id,
                            'init_correct_resp' => $responses->where('phase', '=', 1)->sum('correctness'),
                            'rev_correct_resp' => $responses->where('phase', '=', 2)->sum('correctness'),
                            'init_rep_count' => $responses->where('phase', '=', 1)->count(),
                            'rev_rep_count' => $responses->where('phase', '=', 2)->count(),
                            'active_participants'=>$active_participants
                        ];
                        $result['value'][] = $quiz_info;
                    }
                }
            } catch (\Exception $e) {
                $result['status'] = false;
                $result['message'] = $e->getMessage() . ' in file: ' . $e->getFile() . ' at line: ' . $e->getLine();
            }
        }
        return $result;
    }

    private static function GetOpenSession($quiz_id)
    {
        return QuizRepository::GetOpenSession($quiz_id);
    }
}
