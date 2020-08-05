<?php
/**
 * Created by PhpStorm.
 * User: au614698
 * Date: 12-11-2018
 * Time: 13:20
 */

namespace App\Repositories\Quiz;


use App\Services\ClassService;
use App\Services\QuestionAnswerService;
use App\Services\QuestionService;
use Carbon\Carbon;
use Config;
use DB;

class QuizRepository
{
    /**
     * @param $quiz_name
     * @param $quiz_desc
     * @param $quiz_questions
     * @param $user_id
     * @param $class_id
     * @param $allow_anon
     * @param $is_assessed
     * @param $create_from_duplication
     * @return array
     * @throws \Exception
     */
    public static function CreateNewQuizWithQuestions($quiz_name, $quiz_desc, $quiz_questions, $user_id, $class_id, $allow_anon, $is_assessed, $create_from_duplication)
    {
        $now = Carbon::now('Europe/Copenhagen')->toDateTimeString();
        $quiz_data = [
            'quiz_title' => shortenedString($quiz_name, 255, false),
            'quiz_description' => shortenedString($quiz_desc ?? '', 255, false),
            'quiz_allow_anonymous_participation' => $allow_anon,
            'quiz_created_on' => $now,
            'quiz_is_assessed'=>$is_assessed,
            'quiz_created_by' => $user_id,
            'quiz_message' => '',
            'quiz_active' => 1

        ];
        if ($create_from_duplication) {
            $new_question_ids = [];
        }
        try {
            DB::beginTransaction();

            $new_quiz_id = DB::table('quizzes')
                ->insertGetId($quiz_data);
            if ($new_quiz_id == 0 || $new_quiz_id == null) {
                return ['status' => false, 'message' => 'failed-inserting-quiz'];
            } else {

                if ($class_id != '' && $class_id !== null) {
                    $result = ClassService::AddQuizToClass($new_quiz_id, $class_id);
                    if ($result == null) {
                        return ['status' => false, 'message' => 'error-adding-quiz-class'];
                    }
                }
                //create questions
                if (is_array($quiz_questions) && count($quiz_questions) > 0) {
                    foreach ($quiz_questions as $question) {

                        $create_question_result = QuestionService::CreateNewQuestion($question['question_text'],
                            $question['question_correct_answer'], $user_id,
                            $question['question_index'], $question['image_link'] ?? '');
                        if ($create_question_result['value'] !== null) {
                            //create answers
                            foreach ($question['question_answers'] as $answer) {
                                $create_answer_result = QuestionAnswerService::CreateNewQuestionAnswer(
                                    $create_question_result['value'],
                                    $answer
                                );

                                if ($create_answer_result['value'] === null) {
                                    return $create_answer_result;

                                }
                            }
                            // question and answers were created successfully we now add the question to the quiz
                            DB::table('quiz_questions')
                                ->insert([
                                    'qq_quiz_id' => $new_quiz_id,
                                    'qq_question_id' => $create_question_result['value'],
                                    'qq_question_index' => $question['question_index'],
                                    'qq_created_on' => $now
                                ]);
                            //if this quiz creation is resulted from a quiz duplication we save the old questions
                            // ids and new questions ids so we can link them for the initial and revisions phase display
                            // in the accompanying questions
                            if ($create_from_duplication) {
                                $new_question_ids[$question['id']] = $create_question_result['value'];
                            }
                        } else {
                            return $create_question_result;
                        }

                    }
                }
            }

            DB::commit();

        } catch (\Exception $e) {
            DB::rollback();
            return ['status' => false, 'message' => $e->getMessage() . ' in file: ' . $e->getFile() . ' at line: ' . $e->getLine()];

        }
        if ($create_from_duplication) {
            return ['result' => ['status' => true, 'message' => 'success', 'quiz_id' => $new_quiz_id], 'question_ids' => $new_question_ids];
        }
        return ['status' => true, 'message' => 'success', 'quiz_id' => $new_quiz_id];

    }

    public static function GetTeacherQuizzes($user_id, $class_id)
    {

        $quizzes = DB::table('quizzes')
            ->leftJoin('class_quizzes', 'quiz_id', '=', 'cq_quiz_id')
            ->leftJoin('classes', 'class_id', '=', 'cq_class_id')
            ->where('quiz_created_by', '=', $user_id);
        if ($class_id !== null) {

            $quizzes->where('class_id', '=', $class_id);
        }
        $quizzes->select('quiz_id as id',
            'quiz_title as title',
            'quiz_description as description',
            'quiz_active as active',
            'class_name',
            DB::raw('DATE_FORMAT(quiz_created_on, \'%d-%m-%Y, %H:%i\') as date'))
            ->orderBy('quiz_created_on');

        $quizzes = $quizzes->paginate(10);
        return $quizzes;
    }

    public static function GetParticipantQuizzes($user_id, $paginate)
    {
        $quizzes = Db::table('class_users')
            ->where('cu_user_id', '=', $user_id)
            ->join('class_quizzes', 'cq_class_id', '=', 'cu_class_id')
            ->join('classes', 'class_id', '=', 'cq_class_id')
            ->join('quizzes', 'quiz_id', '=', 'cq_quiz_id')
            ->select('quiz_id as id', 'class_name', 'quiz_title as title');

        $user_session_quiz_id = session('enrolled-quiz-id', null);

        if ($user_session_quiz_id !== null) {
            $extra_quiz = DB::table('quizzes')
                ->where('quiz_id', '=', $user_session_quiz_id)
                ->select('quiz_id as id', DB::raw("'' as class_name"), 'quiz_title as title');
            $quizzes->union($extra_quiz);
        }
        if ($paginate == true) {
            $quizzes = $quizzes->paginate(10);
        } else {

            $quizzes = $quizzes->get();

            foreach ($quizzes as $quiz) {
                $quiz->session = DB::table('quiz_session')
                    ->leftJoin('participants_progress', static function ($join) use ($user_id) {
                        $join->on('pr_session_id', '=', 'qs_id');
                        $join->on('pr_user_id', '=', DB::raw($user_id));
                        $join->on('pr_finished', '=', DB::raw(1));

                    })
                    ->where('qs_quiz_id', '=', $quiz->id)
                    ->whereNull('qs_stopped_at')
                    ->select('qs_id as id', 'qs_reveal_answers as reveal_answers', 'pr_id as progress_id')
                    ->first();

            }
            unset($quiz);
            $quizzes = $quizzes->sortByDesc('session_id');

        }


        return $quizzes;
    }

    public static function GetTeacherQuizzesDropdown($user_id, $class_id)
    {

        $quizzes = DB::table('quizzes')
            ->leftJoin('class_quizzes', 'quiz_id', '=', 'cq_quiz_id')
            ->leftJoin('classes', 'class_id', '=', 'cq_class_id')
            ->where('quiz_created_by', '=', $user_id);
        if ($class_id !== null) {

            $quizzes->where('class_id', '=', $class_id);
        }
        $quizzes->select('quiz_id as id',
            'quiz_title as title',
            'quiz_description as description',
            'quiz_active as active',
            'class_name',
            DB::raw('DATE_FORMAT(quiz_created_on, \'%d-%m-%Y, %H:%i\') as date'))
            ->orderBy('quiz_title');

        return $quizzes->get();
    }

    public static function GetQuizIdFromSession($session_id)
    {
        $result = DB::table('quiz_session')
            ->where('qs_id', '=', $session_id)
            ->select('qs_quiz_id as quiz_id')
            ->pluck('quiz_id')
            ->first();
        return $result;
    }

    public static function GetQuizCreator($quiz_id)
    {

        $result = DB::table('quizzes');
        if (is_array($quiz_id)) {
            $result = $result->whereIn('quiz_id', $quiz_id);
        } else {
            $result = $result->where('quiz_id', $quiz_id);

        }
        $result = $result->select('quiz_id as id', 'quiz_created_by as user_id')
            ->get();

        return $result;
    }

    public static function GetQuizTitle($quiz_id)
    {
        $result = DB::table('quizzes')
            ->leftjoin('class_quizzes', 'cq_quiz_id', 'quiz_id')
            ->leftjoin('classes', 'class_id', '=', 'cq_class_id')
            ->where('quiz_id', '=', $quiz_id)
            ->select('quiz_id as id', 'quiz_title as title', 'quiz_description as description', 'class_name')
            ->first();
        return $result;
    }

    public static function GetQuizInfo($quiz_id)
    {
        $quiz_info = DB::table('quizzes')
            ->leftJoin('class_quizzes', 'cq_quiz_id', '=', 'quiz_id')
            ->leftJoin('classes', 'class_id', '=', 'cq_class_id')
            ->where('quiz_id', '=', $quiz_id)
            ->select('quiz_id as id', 'quiz_title as title', 'quiz_description as description',
                'quiz_active as active', 'class_id', 'class_name', 'quiz_created_by as user_id', 'quiz_allow_anonymous_participation as allow_anon','quiz_is_assessed as is_assessed')
            ->first();

        if ($quiz_info !== null) {
            $quiz_questions = DB::table('quiz_questions')
                ->join('questions', 'question_id', '=', 'qq_question_id')
                ->where('qq_quiz_id', '=', $quiz_id)
                ->orderBy('qq_question_index', 'asc')
                ->select('question_id as id', 'question_text as question_text', 'question_image_link as image_link', 'question_required as question_required',
                    'question_active as active', 'qq_question_index as question_index',
                    'question_right_answer as question_correct_answer')
                ->get();

            if (is_array($quiz_questions->toArray())) {
                foreach ($quiz_questions as &$question) {
                    $question->question_answers = DB::table('question_answers')
                        ->where('qa_question_id', '=', $question->id)
                        ->select('qa_id as id', 'qa_text as answer_text', 'qa_index as answer_index',
                            'qa_active as active', 'qa_index as answer_index')
                        ->orderBy('qa_index', 'asc')
                        ->get();
                }
            }
            $quiz_info->questions = $quiz_questions;

            return $quiz_info;
        } else {
            return null;
        }
    }

    public static function UpdateQuiz($quiz_id, $quiz_text, $questions, $u_id, $class_id, $allow_anon,$is_assessed, $quiz_description)
    {
        try {
            DB::beginTransaction();

            //update the quiz info
            DB::table('quizzes')
                ->where('quiz_id', '=', $quiz_id)
                ->update([
                    'quiz_title' => $quiz_text,
                    'quiz_allow_anonymous_participation' => $allow_anon,
                    'quiz_is_assessed'=>$is_assessed,
                    'quiz_description' => $quiz_description
                ]);
            //update class info
            if ($class_id !== null) {
                DB::table('class_quizzes')
                    ->updateOrInsert(['cq_quiz_id' => $quiz_id],
                        ['cq_class_id' => $class_id, 'cq_created_on' => Carbon::now('Europe/Copenhagen')->toDateTimeString()]);
            }
            $existing_questions = DB::table('quiz_questions')
                ->select('qq_question_id as id')
                ->where('qq_quiz_id', '=', $quiz_id)
                ->pluck('id');
            //remove from db quiz's questions that are no longer present in the $questions array
            foreach ($existing_questions as $existing_question) {
                $found_question = null;
                foreach ($questions as $question) {
                    if (isset($question['question_id']) && $question['question_id'] == $existing_question) {
                        $found_question = true;
                    }
                }
                if ($found_question == null) {
                    //if not found we remove the link between current quiz and current question
                    DB::table('quiz_questions')
                        ->where('qq_quiz_id', '=', $quiz_id)
                        ->where('qq_question_id', '=', $existing_question)
                        ->delete();
                }
            }

            //update questions
            foreach ($questions as $question) {
                if (isset($question['question_id']) && $question['question_id'] !== null) {
                    //update question
                    $update_question_result = QuestionService::UpdateQuestion($question['question_id'], $question['question_answers'],
                        $question['question_text'], $quiz_id, $question['question_index'],
                        $question['question_correct_answer'],
                        $question['image_link'] ?? '', $u_id);

                    if ($update_question_result['status'] == false) {
                        return $update_question_result;
                    }
                } else {
                    //create new question
                    $new_question = QuestionService::CreateNewQuestion($question['question_text'],
                        $question['question_correct_answer'], $u_id, $question['question_index'], $question['image_link'] ?? '');
                    if ($new_question['status'] == true) {

                        //add link for new question and current quiz

                        DB::table('quiz_questions')
                            ->insertGetId([
                                'qq_question_id' => $new_question['value'],
                                'qq_quiz_id' => $quiz_id,
                                'qq_question_index' => $question['question_index'],
                                'qq_created_on' => Carbon::now('Europe/Copenhagen')->toDateTimeString()
                            ]);

                        QuestionAnswerService::CreateNewQuestionAnswer($new_question['value'], $question['question_answers']);
                    } else {
                        return $new_question;
                    }

                }
            }

            DB::commit();

        } catch (\Exception $e) {
            DB::rollback();
            return ['status' => false, 'message' => $e->getMessage() . ' in file: ' . $e->getFile() . ' at line: ' . $e->getLine()];

        }
        return ['status' => true, 'message' => 'success'];

    }

    public static function DeleteQuiz($quiz_id, $just_unlink = false)
    {
        try {
            if ($just_unlink != true) {
                DB::table('quizzes')
                    ->where('quiz_id', '=', $quiz_id)
                    ->delete();
                self::DeleteQuizQuestions($quiz_id);
                self::DeleteQuizAccompanyingQuestions($quiz_id);
                self::DeleteQuizStartingMessage($quiz_id);
                self::DeleteQuizScheduling($quiz_id);
            }
            //remove link between quiz and class
            DB::table('class_quizzes')
                ->where('cq_quiz_id', '=', $quiz_id)
                ->delete();
        } catch (\Exception $e) {
            return ['status' => false, 'message' => $e->getMessage() . ' in file: ' . $e->getFile() . ' at line: ' . $e->getLine()];

        }
        return ['status' => true, 'message' => 'success'];
    }

    public static function DeleteQuizQuestions($quiz_id)
    {
        $questions_ids = DB::table('quiz_questions')
            ->where('qq_quiz_id', '=', $quiz_id)
            ->select('qq_question_id as question_id')
            ->pluck('question_id')->toArray();

        //delete question answers
        DB::table('question_answers')
            ->whereIn('qa_question_id', $questions_ids)
            ->delete();
        //delete questions
        DB::table('questions')
            ->whereIn('question_id', $questions_ids)
            ->delete();
        //delete questions links to the quiz
        DB::table('quiz_questions')
            ->where('qq_quiz_id', '=', $quiz_id)
            ->delete();
    }

    public static function DeleteQuizAccompanyingQuestions($quiz_id)
    {
        $acc_questions_ids = DB::table('accompanying_questions')
            ->where('aq_quiz_id', '=', $quiz_id)
            ->select('aq_id as aq_question_id')
            ->pluck('aq_question_id')->toArray();

        //delete acc question answers
        DB::table('accompanying_questions_answers')
            ->whereIn('aqa_question_id', $acc_questions_ids)
            ->delete();
        //delete acc question feedback positions
        DB::table('accompanying_questions_feedback')
            ->whereIn('aqf_question_id', $acc_questions_ids)
            ->delete();

        //delete acc question positions
        DB::table('accompanying_questions_positions')
            ->whereIn('aqp_acc_question_id', $acc_questions_ids)
            ->delete();
    }

    public static function DeleteQuizScheduling($quiz_id)
    {
        DB::table('quiz_scheduling')
            ->where('qsch_quiz_id', '=', $quiz_id)
            ->delete();
    }

    public static function DeleteQuizStartingMessage($quiz_id)
    {
        DB::table('quiz_additional_messages')
            ->where('qam_quiz_id', '=', $quiz_id)
            ->delete();
    }


    public static function GetDashboardTeacherQuizzes($user_id, $class_id)
    {
        $sessions = DB::table('quiz_session')
            ->select('qs_id', 'qs_quiz_id')
            ->whereNull('qs_stopped_at')
            ->orderBy('qs_id', 'desc');
        $quizzes = DB::table('quizzes')
            ->leftJoin('class_quizzes', 'quiz_id', '=', 'cq_quiz_id')
            ->leftJoin('classes', 'class_id', '=', 'cq_class_id')
            ->where('quiz_created_by', '=', $user_id);

        if ($class_id !== null) {

            $quizzes->where('class_id', '=', $class_id);
        }
        $quizzes->select('quiz_id as id',
            'quiz_title as title',
            'qs_id as session_id',
            'quiz_description as description',
            'quiz_active as active',
            DB::raw('COUNT(qq_id) as questions'),
            'class_name', 'class_id',
            DB::raw('DATE_FORMAT(quiz_created_on, \'%d-%m-%Y, %H:%i\') as date'))
            ->leftJoin('quiz_questions', 'qq_quiz_id', '=', 'quiz_id')
            ->leftJoinSub($sessions, 'sessions', 'qs_quiz_id', '=', 'quiz_id')
            ->orderBy('quiz_created_on')
            ->groupBy(['quiz_id',
                'qs_id',
                'quiz_title',
                'quiz_description',
                'quiz_active',
                'class_id',
                'class_name']);
        $quizzes = $quizzes->get();

        return $quizzes;
    }


    public static function UpdateQuizPrepQuestion($quiz_id, $prep_question)
    {

        $existing_row = DB::table('accompanying_questions')
            ->where('aq_quiz_id', '=', $quiz_id)
            ->where('aq_type', '=', 1)
            ->first();
        if ($existing_row !== null) {
            //update the existing row
            try {


                DB::table('accompanying_questions')
                    ->where('aq_id', '=', $existing_row->aq_id)
                    ->update([
                        'aq_question_text' => $prep_question['question_title'],
                        'aq_question_explanation' => $prep_question['question_explanation'],
                        'aq_type' => 1,
                        'aq_structure' => $prep_question['question_type']

                    ]);

            } catch (\Exception $e) {
                return ['status' => false, 'message' => $e->getMessage() . ' in file: ' . $e->getFile() . ' at line: ' . $e->getLine()];

            }

            //update the answers
            $result = self::UpdateAccompanyingQuestionOptions($existing_row->aq_id, $prep_question['question_options']);


        } else {
            //create new row
            $acc_question_id = DB::table('accompanying_questions')
                ->insertGetId(
                    [
                        'aq_quiz_id' => $quiz_id,
                        'aq_question_text' => $prep_question['question_title'],
                        'aq_question_explanation' => $prep_question['question_explanation'],
                        'aq_type' => 1,
                        'aq_structure' => $prep_question['question_type']
                    ]);
            $result = self::UpdateAccompanyingQuestionOptions($acc_question_id, $prep_question['question_options']);
        }

        return $result;
    }

    public static function UpdateQuizConfQuestion($quiz_id, $conf_question)
    {
        $existing_row = DB::table('accompanying_questions')
            ->where('aq_quiz_id', '=', $quiz_id)
            ->where('aq_type', '=', 2)
            ->first();
        if ($existing_row !== null) {
            //update the existing row
            try {
                DB::table('accompanying_questions')
                    ->where('aq_id', '=', $existing_row->aq_id)
                    ->update([
                        'aq_question_text' => $conf_question['question_title'],
                        'aq_question_explanation' => $conf_question['question_explanation'],
                        'aq_type' => 2,
                        'aq_structure' => $conf_question['question_type']
                    ]);

            } catch (\Exception $e) {
                return ['status' => false, 'message' => $e->getMessage() . ' in file: ' . $e->getFile() . ' at line: ' . $e->getLine()];

            }

            //update the answers
            $result = self::UpdateAccompanyingQuestionOptions($existing_row->aq_id, $conf_question['question_options']);


            //update feedback info
            self::UpdateAccompanyingQuestionFeedback($existing_row->aq_id, $conf_question['init_ph_display'], $conf_question['rev_ph_display']);

        } else {
            //create new row
            $acc_question_id = DB::table('accompanying_questions')
                ->insertGetId(
                    [
                        'aq_quiz_id' => $quiz_id,
                        'aq_question_text' => $conf_question['question_title'],
                        'aq_question_explanation' => $conf_question['question_explanation'],
                        'aq_type' => 2,
                        'aq_init_ph_display' => 1,
                        'aq_rev_ph_display' => 1,
                        'aq_structure' => $conf_question['question_type']
                    ]);
            //update the answers
            $result = self::UpdateAccompanyingQuestionOptions($acc_question_id, $conf_question['question_options']);

            //update feedback info
            self::UpdateAccompanyingQuestionFeedback($acc_question_id, $conf_question['init_ph_display'], $conf_question['rev_ph_display']);

        }

        return $result;
    }

    public static function UpdateQuizJustQuestion($quiz_id, $just_question)
    {
        $existing_row = DB::table('accompanying_questions')
            ->where('aq_quiz_id', '=', $quiz_id)
            ->where('aq_type', '=', 3)
            ->first();
        if ($existing_row !== null) {
            //update the existing row
            try {


                DB::table('accompanying_questions')
                    ->where('aq_id', '=', $existing_row->aq_id)
                    ->update([
                        'aq_question_text' => $just_question['question_title'],
                        'aq_question_explanation' => $just_question['question_explanation'],
                        'aq_type' => 3,
                        'aq_structure' => $just_question['question_type']
                    ]);

            } catch (\Exception $e) {
                return ['status' => false, 'message' => $e->getMessage() . ' in file: ' . $e->getFile() . ' at line: ' . $e->getLine()];

            }

            $result = ['status' => true, 'message' => 'success'];

            self::UpdateAccompanyingQuestionFeedback($existing_row->aq_id, $just_question['init_ph_display'], $just_question['rev_ph_display']);

            //update feedback info

        } else {
            //create new row
            $acc_question_id = DB::table('accompanying_questions')
                ->insertGetId(
                    [
                        'aq_quiz_id' => $quiz_id,
                        'aq_question_text' => $just_question['question_title'],
                        'aq_question_explanation' => $just_question['question_explanation'],
                        'aq_type' => 3,
                        'aq_init_ph_display' => 1,
                        'aq_rev_ph_display' => 0,
                        'aq_structure' => $just_question['question_type']
                    ]);

            self::UpdateAccompanyingQuestionFeedback($acc_question_id, $just_question['init_ph_display'], $just_question['rev_ph_display']);
            $result = ['status' => true, 'message' => 'success'];
        }


        return $result;
    }


    public static function DeleteOtherAccompanyingQuestion($id, $position, $type)
    {
        DB::table('accompanying_questions')
            ->where('aq_id', '=', $id)
            ->delete();
        if ($position == 2) {
            //delete locations and feedback
            DB::table('accompanying_questions_feedback')
                ->where('aqf_question_id', '=', $id)
                ->delete();

            DB::table('accompanying_questions_positions')
                ->where('aqp_question_id', '=', $id)
                ->delete();
        }
        if ($type == 1 || $type == 2) {
            //delete answers
            DB::table('accompanying_questions_answers')
                ->where('aqa_question_id', '=', $id)
                ->delete();
        }
    }

    public static function UpdateQuizOtherQuestion($quiz_id, array $other_questions)
    {
        try {
            DB::beginTransaction();
            $existing_rows = DB::table('accompanying_questions')
                ->where('aq_quiz_id', '=', $quiz_id)
                ->where('aq_type', '=', 4)
                ->get()
                ->toArray();
            if (count($existing_rows) > 0) {
                //update the existing row
                foreach ($existing_rows as $existing_row) {
                    $existing_row = json_decode(json_encode($existing_row), true);
                    $found = false;
                    foreach ($other_questions as $other_question) {
                        if (isset($other_question['id'])) {
                            if ($other_question['id'] == $existing_row['aq_id']) {
                                $found = true;
                                //we found the old entry for a question, we update the specific question

                                $data = [
                                    'aq_question_text' => $other_question['question_title'],
                                    'aq_question_explanation' => $other_question['question_explanation'],
                                    'aq_structure' => $other_question['question_type'],
                                    'aq_name' => $other_question['question_name'],
                                    'aq_position' => $other_question['question_position']
                                ];

                                if ($other_question['question_position'] == 2) {
                                    $data['aq_init_ph_display'] = convertBoolStringToInt($other_question['initial_phase_display']);
                                    $data['aq_rev_ph_display'] = convertBoolStringToInt($other_question['revision_phase_display']);
                                } else {
                                    $data['aq_init_ph_display'] = null;
                                    $data['aq_rev_ph_display'] = null;
                                }

                                DB::table('accompanying_questions')
                                    ->where('aq_id', '=', $other_question['id'])
                                    ->update($data);
                                //about structure
                                if ($existing_row['aq_structure'] != $other_question['question_type']) {
                                    //we have a different structure so we need to make some adjustments

                                    if ($other_question['question_type'] == 1 || $other_question['question_type'] == 2) {
                                        //we need to update the options as both the new version and the previous version of the questions had the structure with options
                                        self::UpdateAccompanyingQuestionOptions($other_question['id'], $other_question['question_options']);
                                    } else if (($existing_row['aq_structure'] == 1 || $existing_row['aq_structure'] == 2) &&
                                        $other_question['question_type'] == 3
                                    ) {
                                        //we had the structure with options but now it's a text field so we will delete the options
                                        DB::table('accompanying_questions_answers')
                                            ->where('aqa_question_id', '=', $other_question['id'])
                                            ->delete();
                                    }

                                }
                                if ($other_question['question_position'] == 2) {
                                    // add info regarding location and feedback
                                    self::UpdateAccompanyingQuestionFeedback($other_question['id'], $other_question['init_ph_display'] ?? [], $other_question['rev_ph_display'] ?? []);

                                } else if ($other_question['question_position'] != 2) {
                                    //delete info regarding locations and feedback
                                    self::UpdateAccompanyingQuestionFeedback($other_question['id'], [], []);
                                }
                            }
                        }
                    }
                    //delete existing row that is not present anymore
                    if ($found == false) {
                        self::DeleteOtherAccompanyingQuestion($existing_row['aq_id'], $existing_row['aq_position'], $existing_row['aq_structure']);

                    }

                }
                //add the new questions (the ones that don't have an id)
                foreach ($other_questions as $other_question) {
                    if (!isset($other_question['id']) || $other_question['id'] == null) {
                        $result = self::CreateNewOtherAccompanyingQuestion($other_question, $quiz_id);

                        if ($result['status'] == false) {
                            DB::rollBack();
                            return $result;
                        }
                    }
                }
            } else {
                foreach ($other_questions as $other_question) {

                    $result = self::CreateNewOtherAccompanyingQuestion($other_question, $quiz_id);

                    if ($result['status'] == false) {
                        DB::rollBack();
                        return $result;
                    }
                }
            }


            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return ['status' => false, 'message' => $e->getMessage() . ' in file: ' . $e->getFile() . ' at line: ' . $e->getLine()];

        }
        return ['status' => true, 'message' => 'success'];
    }


    public static function CreateNewOtherAccompanyingQuestion($question, $quiz_id)
    {

        $data = [
            'aq_quiz_id' => $quiz_id,
            'aq_question_text' => $question['question_title'],
            'aq_question_explanation' => $question['question_explanation'],
            'aq_type' => 4,
            'aq_position' => $question['question_position'],
            'aq_structure' => $question['question_type'],
            'aq_name' => $question['question_name'],
        ];

        if ($question['question_position'] == 2) {
            $data['aq_init_ph_display'] = convertBoolStringToInt($question['initial_phase_display']);
            $data['aq_rev_ph_display'] = convertBoolStringToInt($question['revision_phase_display']);
        }

        $id = DB::table('accompanying_questions')
            ->insertGetId($data);

        if ($question['question_type'] < 3) {
            //insert the options
            foreach ($question['question_options'] as $option) {
                $result = self::CreateAccompanyingQuestionOption($id, $option);
                if ($result['status'] == false) {
                    DB::rollBack();
                    return $result;
                }
            }

        }

        if ($question['question_position'] == 2) {
            self::UpdateAccompanyingQuestionFeedback($id, $question['init_ph_display'] ?? [], $question['rev_ph_display'] ?? []);
        }
        if ($id == null) {
            return ['status' => false, 'message' => 'failed to create new other question'];
        }
        return ['status' => true, 'message' => 'success'];
    }

    public static function UpdateAccompanyingQuestionOptions($question_id, $options)
    {
        try {
            DB::beginTransaction();
            $existing_options = DB::table('accompanying_questions_answers')
                ->select('aqa_id as id')
                ->where('aqa_question_id', '=', $question_id)
                ->pluck('id');
            //remove from db acc questions that are no longer present in the $questions array
            foreach ($existing_options as $existing_option) {
                $found_option = null;
                foreach ($options as $option) {
                    if (isset($option['id']) && $option['id'] == $existing_option) {
                        $found_option = true;
                    }
                }
                if ($found_option == null) {
                    DB::table('accompanying_questions_answers')
                        ->where('aqa_question_id', '=', $question_id)
                        ->where('aqa_id', '=', $existing_option)
                        ->delete();
                }
            }

            //update questions
            foreach ($options as $option) {
                if (isset($option['id']) && $option['id'] !== null) {
                    //update question
                    $result = self::UpdateAccompanyingQuestionOption($option);
                    if ($result['status'] == false) {
                        return $result;

                    }
                } else {
                    //create new question answer
                    $new_option = self::CreateAccompanyingQuestionOption($question_id, $option);
                    if ($new_option['status'] == false) {
                        return $new_option;

                    }

                }
            }
            DB::commit();

        } catch (\Exception $e) {
            DB::rollback();
            return ['status' => false, 'message' => $e->getMessage() . ' in file: ' . $e->getFile() . ' at line: ' . $e->getLine()];

        }
        return ['status' => true, 'message' => 'success'];

    }

    public static function UpdateAccompanyingQuestionFeedback($acc_question_id, $init_ph_display, $rev_ph_display)
    {
        //remove previous data
        DB::table('accompanying_questions_feedback')
            ->where('aqf_acc_question_id', '=', $acc_question_id)
            ->delete();
        $questions = [];

        if (count($init_ph_display) > 0) {
            foreach ($init_ph_display as $feedback_question) {
                $questions[] = ['aqf_question_id' => $feedback_question, 'aqf_acc_question_id' => $acc_question_id];
            }

            //insert up to date data
            DB::table('accompanying_questions_feedback')
                ->insert($questions);
        }

        //remove previous data

        DB::table('accompanying_questions_positions')
            ->where('aqp_acc_question_id', '=', $acc_question_id)
            ->delete();

        if (count($rev_ph_display) > 0) {

            $locations = [];
            foreach ($rev_ph_display as $feedback_location) {
                $locations[] = ['aqp_question_id' => $feedback_location, 'aqp_acc_question_id' => $acc_question_id];
            }

            DB::table('accompanying_questions_positions')
                ->insert($locations);
        }
    }

    public static function UpdateAccompanyingQuestionOption($option)
    {
        try {
            DB::table('accompanying_questions_answers')
                ->where('aqa_id', '=', $option['id'])
                ->update([
                    'aqa_text' => $option['text'],
                    'aqa_qaqa_index' => $option['index']
                ]);

            return ['status' => true, 'message' => 'success'];
        } catch (\Exception $e) {
            return ['status' => false, 'message' => $e->getMessage() . ' in file: ' . $e->getFile() . ' at line: ' . $e->getLine()];

        }
    }

    public static function RemoveAccompanyingQuestion($acc_question_ids, $type)
    {
        try {
            //delete accompanying questions

            DB::table('accompanying_questions')
                ->whereIn('aq_id', $acc_question_ids)
                ->delete();
            if ($type != 3) {
                //if the accompanying questions are not justification questions then we also need to delete the related answer options
                DB::table('accompanying_questions_answers')
                    ->whereIn('aqa_question_id', $acc_question_ids)
                    ->delete();
            }

            if ($type != 1) {
                //if the accompanying questions are not preparation questions then we also need to delete the positions (where the question are appearing)
                // and feedback(for what questions we save the feedback) info related
                DB::table('accompanying_questions_feedback')
                    ->whereIn('aqf_question_id', $acc_question_ids)
                    ->delete();

                DB::table('accompanying_questions_positions')
                    ->whereIn('aqp_question_id', $acc_question_ids)
                    ->delete();
            }

            return ['status' => true, 'message' => 'success'];
        } catch (\Exception $e) {
            return ['status' => false, 'message' => $e->getMessage() . ' in file: ' . $e->getFile() . ' at line: ' . $e->getLine() . ' trace :' . $e->getTraceAsString()];
        }
    }


    public static function CheckQuizHasAccompanyingQuestion($quiz_id, $acc_question_type)
    {
        $result = DB::table('accompanying_questions')
            ->where('aq_quiz_id', '=', $quiz_id)
            ->where('aq_type', '=', $acc_question_type)
            ->get()->pluck('aq_id')->toArray();

        return $result;
    }

    public static function CreateAccompanyingQuestionOption($acc_question_id, $option)
    {
        $option_data = [
            'aqa_text' => $option['text'],
            'aqa_index' => $option['index'],
            'aqa_question_id' => $acc_question_id,
        ];

        $new_option_id = DB::table('accompanying_questions_answers')
            ->insertGetId($option_data);

        if ($new_option_id !== null) {
            return ['status' => true, 'message' => 'success', 'value' => $new_option_id];
        } else {
            return ['status' => false, 'message' => 'failed-inserting-option', 'value' => $new_option_id];

        }
    }

    public static function GetQuizAccompanyingQuestions($id)
    {
        $accompanying_questions = DB::table('accompanying_questions')
            ->where('aq_quiz_id', '=', $id)
            ->select('aq_id as id',
                'aq_question_text as title',
                'aq_question_explanation as explanation',
                'aq_type as type',
                'aq_position as position',
                'aq_structure as structure',
                'aq_init_ph_display as init_display',
                'aq_rev_ph_display as rev_display',
                'aq_name as name'
            )
            ->get();

        foreach ($accompanying_questions as &$accompanying_question) {
            if ($accompanying_question->type != 3) {
                $accompanying_question->options = DB::table('accompanying_questions_answers')
                    ->where('aqa_question_id', '=', $accompanying_question->id)
                    ->select('aqa_id as id', 'aqa_text as text')
                    ->orderBy('aqa_index', 'asc')
                    ->get();
            }

            if ($accompanying_question->type != 1) {

                $accompanying_question->feedback = DB::table('accompanying_questions_feedback')
                    ->where('aqf_acc_question_id', '=', $accompanying_question->id)
                    ->select('aqf_question_id as questions_ids')
                    ->pluck('questions_ids')->toArray();

                $accompanying_question->positions = DB::table('accompanying_questions_positions')
                    ->where('aqp_acc_question_id', '=', $accompanying_question->id)
                    ->select('aqp_question_id as questions_ids')
                    ->pluck('questions_ids')->toArray();
            }
        }

        return $accompanying_questions;
    }

    public static function UpdateAdditionalMessage($quiz_id, $message, $message_title)
    {
        try {
            DB::table('quiz_additional_messages')
                ->updateOrInsert(['qam_quiz_id' => $quiz_id], ['qam_message' => $message, 'qam_message_title' => $message_title]);

            return ['status' => true, 'message' => 'success'];
        } catch (\Exception $e) {
            return ['status' => false, 'message' => $e->getMessage() . ' in file: ' . $e->getFile() . ' at line: ' . $e->getLine() . ' trace :' . $e->getTraceAsString()];
        }
    }

    public static function GetQuizMessage($id)
    {
        $result = DB::table('quiz_additional_messages')
            ->where('qam_quiz_id', '=', $id)
            ->select('qam_message as message', 'qam_message_title as message_title')
            ->first();

        return $result;
    }

    public static function GetQuizActiveSession($quiz_id)
    {
        $session = DB::table('quiz_session')
            ->where('qs_quiz_id', '=', $quiz_id)
            ->whereNull('qs_stopped_at')
            ->select('qs_id as id')
            ->first();
        return $session;
    }

    public static function StartQuizProgress($user_id, $quiz_id, $start_index)
    {

        $active_session = self::GetQuizActiveSession($quiz_id);
        if ($active_session !== null) {
            $data = [
                'pr_user_id' => $user_id,
                'pr_session_id' => $active_session->id,
                'pr_index' => $start_index,
                'pr_finished' => 0,
                'pr_phase' => Config::get('defines.QUIZ_INITIAL_PHASE'),
                'pr_displayed_message' => 0,
                'pr_updated_at' => Carbon::now('Europe/Copenhagen')->toDateTimeString(),
                'pr_started_at' => Carbon::now('Europe/Copenhagen')->toDateTimeString()
            ];
            try {
                DB::table('participants_progress')
                    ->insert([$data]);

                return ['status' => true, 'message' => 'success'];
            } catch (\Exception $e) {
                return ['status' => false, 'message' => $e->getMessage() . ' in file: ' . $e->getFile() . ' at line: ' . $e->getLine() . ' trace :' . $e->getTraceAsString()];
            }
        } else {
            return ['status' => false, 'message' => 'no active session'];

        }
    }

    public static function StartAnonQuizProgress($participant_id, $quiz_id, $start_index)
    {

        $active_session = self::GetQuizActiveSession($quiz_id);
        if ($active_session !== null) {
            $data = [
                'pr_user_id' => 0,
                'pr_session_id' => $active_session->id,
                'pr_index' => $start_index,
                'pr_finished' => 0,
                'pr_phase' => Config::get('defines.QUIZ_INITIAL_PHASE'),
                'pr_displayed_message' => 0,
                'pr_updated_at' => Carbon::now('Europe/Copenhagen')->toDateTimeString(),
                'pr_started_at' => Carbon::now('Europe/Copenhagen')->toDateTimeString()
            ];
            try {
                DB::table('participants_progress')
                    ->where('pr_id', '=', $participant_id)
                    ->update($data);

                return ['status' => true, 'message' => 'success'];
            } catch (\Exception $e) {
                return ['status' => false, 'message' => $e->getMessage() . ' in file: ' . $e->getFile() . ' at line: ' . $e->getLine() . ' trace :' . $e->getTraceAsString()];
            }
        } else {
            return ['status' => false, 'message' => 'no active session'];

        }
    }

    public static function UpdateQuizProgress($participant_id, $quiz_id, $index, $phase, $displayed_message = null, $finished = null)
    {
        $active_session = self::GetQuizActiveSession($quiz_id);


        if ($active_session !== null) {
            $data = [
                'pr_index' => $index,
                'pr_phase' => $phase,
                'pr_session_id' => $active_session->id,
                'pr_finished' => $finished !== null ? $finished : 0,
                'pr_displayed_message' => $displayed_message !== null ? $displayed_message : 0,
                'pr_updated_at' => Carbon::now('Europe/Copenhagen')->toDateTimeString()
            ];

            if ($finished == 1) {
                $data['pr_finished_at'] = Carbon::now('Europe/Copenhagen')->toDateTimeString();
            }
            try {
                DB::table('participants_progress')
                    ->where('pr_id', '=', $participant_id)
                    ->update($data);

                return ['status' => true, 'message' => 'success'];
            } catch (\Exception $e) {
                return ['status' => false, 'message' => $e->getMessage() . ' in file: ' . $e->getFile() . ' at line: ' . $e->getLine() . ' trace :' . $e->getTraceAsString()];
            }
        } else {
            return ['status' => false, 'message' => 'no active session found'];

        }
    }

    public static function GetSessionQuizData($session_id)
    {
        $result = DB::table('quiz_session')
            ->where('qs_id', '=', $session_id)
            ->select('qs_quiz_data as quiz_data')
            ->pluck('quiz_data')
            ->first();


        return $result;
    }

    public static function GetParticipantCurrentQuizInformation($user_id, $quiz_id, $unfinished)
    {
        $result = DB::table('participants_progress')
            ->join('quiz_session', 'qs_id', '=', 'pr_session_id')
            ->join('quizzes', 'qs_quiz_id', '=', 'quiz_id')
            ->where('pr_user_id', '=', $user_id)
            ->where('qs_quiz_id', '=', $quiz_id);
        if ($unfinished === true) {
            $result = $result->where('pr_finished', '=', '0')
                ->whereNull('qs_stopped_at');
        }
        $result = $result->select('pr_index as index',
            'pr_session_id as session_id',
            'pr_phase as phase',
            'pr_id as id',
            'qs_phase as session_phase',
            'qs_reveal_answers as reveal_answers',
            'qs_quiz_data as quiz_data',
            'qs_quiz_id as quiz_id',
            'qs_stopped_at',
            'quiz_title',
            'pr_displayed_message as displayed_message', 'pr_finished as finished')
            ->orderBy('pr_id', 'desc')
            ->first();

        return $result;
    }

    public static function GetAnonParticipantCurrentQuizInformation($participant_id, $session_id, bool $unfinished)
    {
        $result = DB::table('participants_progress')
            ->join('quiz_session', 'qs_id', '=', 'pr_session_id')
            ->join('quizzes', 'qs_quiz_id', '=', 'quiz_id')
            ->where('pr_id', '=', $participant_id)
            ->where('qs_id', '=', $session_id);
        if ($unfinished === true) {
            $result = $result->where('pr_finished', '=', '0')
                ->whereNull('qs_stopped_at');

        }
        $result = $result->select('pr_index as index',
            'pr_session_id as session_id',
            'pr_phase as phase',
            'pr_id as id',
            'qs_phase as session_phase',
            'qs_reveal_answers as reveal_answers',
            'qs_quiz_data as quiz_data',
            'qs_quiz_id as quiz_id',
            'qs_stopped_at',
            'quiz_title',
            'pr_displayed_message as displayed_message', 'pr_finished as finished')
            ->orderBy('pr_id', 'desc')
            ->first();

        return $result;
    }


    public static function GetQuizPreparationQuestion($quiz_id)
    {
        $prep_question = DB::table('accompanying_questions')
            ->where('aq_quiz_id', '=', $quiz_id)
            ->where('aq_type', '=', 1)
            ->select('aq_id as id', 'aq_question_text as text', 'aq_question_explanation as explanation', 'aq_structure as structure')
            ->first();

        if ($prep_question !== null) {
            $prep_question->answers = DB::table('accompanying_questions_answers')
                ->where('aqa_question_id', '=', $prep_question->id)
                ->select('aqa_text as text', 'aqa_id as id')
                ->orderBy('aqa_index', 'asc')
                ->get();
        }
        return $prep_question;
    }

    public static function CheckParticipantQuizEnrollment($user_id, $quiz_id)
    {
        //check if quiz is in a class first
        $quiz_is_in_class = DB::table('class_quizzes')
            ->where('cq_quiz_id', '=', $quiz_id)
            ->first();
        if ($quiz_is_in_class !== null) {
            $result = DB::table('class_users')
                ->join('class_quizzes', 'cq_class_id', '=', 'cu_class_id')
                ->where('cu_user_id', '=', $user_id)
                ->where('cq_quiz_id', '=', $quiz_id)
                ->first();
            return $result !== null;
        } else {
            //if quiz is not in any class then we consider the participant enrolled
            return true;
        }
    }

    public static function GetQuizQuestion($quiz_id, $index)
    {
        $result = DB::table('quiz_questions')
            ->join("questions", 'qq_question_id', '=', 'question_id')
            ->where('qq_question_index', '=', $index)
            ->where('qq_quiz_id', '=', $quiz_id)
            ->where('question_active', '=', 1)
            ->select('question_id as id', 'question_text as text', 'question_image_link as image_link', 'question_right_answer as right_answer',
                'question_required as required')->first();

        if ($result !== null) {
            $result->answers = DB::table('question_answers')
                ->select('qa_id as id', 'qa_text as text')
                ->where('qa_question_id', '=', $result->id)
                ->where('qa_active', '=', 1)
                ->orderBy('qa_index', 'asc')
                ->get();
        }

        return $result;

    }

    public static function GetDashboardQuizInfo($quiz_id)
    {
        $quiz = DB::table('quizzes')
            ->where('quiz_id', '=', $quiz_id)
            ->select('quiz_id as id', 'quiz_title as title', 'quiz_description as description', 'quiz_participation_limit as participation_limit','quiz_is_assessed as is_assessed')
            ->first();

        $result = DB::table('quiz_additional_messages')
            ->where('qam_quiz_id', '=', $quiz_id)
            ->select('qam_message_title as message_title', 'qam_message as message')
            ->first();
        if ($quiz !== null && $result !== null) {
            $quiz->message = $result->message;
            $quiz->message_title = $result->message_title;
        }

        return $quiz;

    }

    public static function GetQuizStatus($quiz_id)
    {
        $session = DB::table('quiz_session')
            ->where('qs_quiz_id', '=', $quiz_id)
            ->whereNull('qs_stopped_at')
            ->first();

        return $session !== null ? Config::get('defines.QUIZ_STATUS_RUNNING') : Config::get('defines.QUIZ_STATUS_STOPPED');
    }

    public static function GetQuizSessionInfo($quiz_id) {
        $session = DB::table('quiz_session')
            ->select('qs_id as id','qs_phase as phase','qs_reveal_answers as reveal_answers')
            ->where('qs_quiz_id', '=', $quiz_id)
            ->whereNull('qs_stopped_at')
            ->first();
        return $session;
    }
    public static function GetQuestionAccompanyingQuestions($quiz_id, $question_id)
    {

        $acc_questions_outside_from_quiz = DB::table('accompanying_questions')
            ->where('aq_quiz_id', '=', $quiz_id)
            ->whereIn('aq_type', [1, 4])
            ->select('aq_id as id', 'aq_question_text as text', 'aq_question_explanation as explanation', 'aq_type as type',
                'aq_structure as structure');

        $accompanying_questions = DB::table('accompanying_questions_positions')
            ->join('accompanying_questions', 'aq_id', '=', 'aqp_acc_question_id')
            ->where('aqp_question_id', '=', $question_id)
            ->where('aq_quiz_id', '=', $quiz_id)
            ->select('aq_id as id', 'aq_question_text as text', 'aq_question_explanation as explanation', 'aq_type as type',
                'aq_structure as structure')
            ->union($acc_questions_outside_from_quiz)
            ->get();

        foreach ($accompanying_questions as $accompanying_question) {
            if ($accompanying_question->structure == 1 || $accompanying_question->structure == 2) {
                $accompanying_question->answers = DB::table('accompanying_questions_answers')
                    ->where('aqa_question_id', '=', $accompanying_question->id)
                    ->orderBy('aqa_index', 'asc')
                    ->select('aqa_text as text', 'aqa_id as id')
                    ->get();
            }
        }
        return $accompanying_questions;
    }

    public static function GetQuizQuestionsCount($quiz_id)
    {
        $result = DB::table('quiz_questions')
            ->where('qq_quiz_id', '=', $quiz_id)
            ->count();
        return $result;
    }

    public static function GetQuizCurrentData($quiz_id)
    {
        $quiz = DB::table('quizzes')
            ->where('quiz_id', '=', $quiz_id)
            ->select('quiz_id as id', 'quiz_title as title','quiz_is_assessed as is_assessed')
            ->first();
        if ($quiz !== null) {
            $quiz->acc_questions = DB::table('accompanying_questions')
                ->leftJoin('accompanying_questions_positions', 'aq_id', '=', 'aqp_acc_question_id')
                ->leftJoin('accompanying_questions_feedback', 'aq_id', '=', 'aqf_acc_question_id')
                ->where('aq_quiz_id', '=', $quiz->id)
                ->select('aq_id as id', 'aq_type as type', 'aq_question_explanation as explanation', 'aq_question_text as text', 'aq_position as other_question_position', 'aq_name as name', 'aq_structure as structure', DB::raw("GROUP_CONCAT(DISTINCT aqp_question_id SEPARATOR ',') as positions"),
                    DB::raw("GROUP_CONCAT(DISTINCT aqf_question_id SEPARATOR ',') as feedback"), 'aq_init_ph_display as init_display', 'aq_rev_ph_display as aq_rev_ph_display')
                ->groupBy('aq_id')
                ->get();

            foreach ($quiz->acc_questions as &$acc_question) {
                $acc_question->positions = explode(',', $acc_question->positions);
                $acc_question->feedback = explode(',', $acc_question->feedback);
                if ($acc_question->structure != 3) {
                    $acc_question->answers = DB::table('accompanying_questions_answers')
                        ->where('aqa_question_id', '=', $acc_question->id)
                        ->select('aqa_id as id', 'aqa_text as text')
                        ->orderBy('aqa_index')
                        ->get();
                }
            }
            unset($acc_question);
            $quiz->questions = DB::table('quiz_questions')
                ->where('qq_quiz_id', '=', $quiz_id)
                ->join('questions', 'qq_question_id', '=', 'question_id')
                ->select('question_id as id', 'question_text as text', 'question_image_link as image_link', 'question_right_answer as right_answer', 'question_required as required')
                ->orderBy('qq_question_index')
                ->get();

            foreach ($quiz->questions as $question) {
                $question->answers = DB::table('question_answers')
                    ->where('qa_question_id', '=', $question->id)
                    ->select('qa_id as id', 'qa_text as text')
                    ->orderBy('qa_index')
                    ->get();
            }

        }
        return $quiz;

    }

    public static function GetMonitoringInfo($quiz_id,$is_update)
    {
        if($is_update === false) {
            $quiz = DB::table('quizzes')
                ->where('quiz_id', '=', $quiz_id)
                ->select('quiz_id as id', 'quiz_title as title')
                ->first();
        }
       else {
           $quiz = new \stdClass();

       }
        if ($quiz !== null) {
            $existing_session = DB::table('quiz_session')
                ->where('qs_quiz_id', '=', $quiz_id)
                ->whereNull('qs_stopped_at')
                ->select('qs_id as id', 'qs_phase as phase', 'qs_quiz_data as quiz_data', 'qs_reveal_answers as reveal_answers')
                ->first();
            if ($existing_session !== null) {
                $quiz->id = $quiz_id;
                $quiz->phase = $existing_session->phase;

                if($is_update === false) {
                    $quiz->status = 1;
                    $quiz->reveal_answers = $existing_session->reveal_answers;
                }
                $quiz->session_id = $existing_session->id;

                $quiz->data = json_decode($existing_session->quiz_data);
                $quiz->acc_questions = json_decode(json_encode($quiz->data->acc_questions), 1);
                $quiz->questions = json_decode(json_encode($quiz->data->questions), 1);

                $quiz->responses = DB::table('quiz_session')
                    ->where('qs_quiz_id', '=', $quiz_id)
                    ->whereNull('qs_stopped_at')
                    ->join('quiz_responses', 'qs_id', 'qr_session_id')
                    ->select('qr_id as id', 'qr_question_id as question_id', 'qr_answer_index as answer_index',
                        'qr_phase as phase')
                    ->orderBy('qr_question_id')
                    ->orderBy('qr_answer_index')
                    ->get()
                    ->toArray();

                $quiz->acc_questions_responses = DB::table('quiz_responses_acc_questions')
                    ->leftJoin('quiz_responses', 'qraq_response_id', '=', 'qr_id')
                    ->where('qraq_session_id', $existing_session->id)
                    ->select('qraq_response_id as response_id', 'qraq_acc_id as acc_question_id', 'qraq_acc_answer_index as answer_index', 'qraq_acc_answer_content as answer_content',
                        'qr_id as response_id', 'qr_phase as phase', 'qr_question_id as question_id', 'qr_answer_index as q_answer_index')
                    ->get()
                    ->toArray();

                    //total active participants
                    $responses_distinct_participants = DB::table('quiz_responses')
                        ->join('participants_progress', 'pr_id', '=', 'qr_qp_id')
                        ->where('qr_session_id', '=', $existing_session->id)
                        ->select(DB::raw('DISTINCT qr_qp_id as id'), 'pr_anonymous')
                        ->groupBy('qr_qp_id');
                    $acc_responses_distinct_participants = DB::table('quiz_responses_acc_questions')
                        ->join('participants_progress', 'pr_id', '=', 'qraq_qp_id')
                        ->where('qraq_session_id', '=', $existing_session->id)
                        ->select(DB::raw('DISTINCT qraq_qp_id as id'), 'pr_anonymous')
                        ->groupBy('qraq_qp_id');

                    $quiz->active_participants_count = $responses_distinct_participants->union($acc_responses_distinct_participants)->count();
                    $quiz->active_anon_participants_count = $responses_distinct_participants->union($acc_responses_distinct_participants)->get()->filter(function ($value, $key) {
                        return $value->pr_anonymous > 0;
                    })->count();

            } else {
                $quiz->status = 2;
                $quiz->phase = null;
                $quiz->session_id = null;
                $quiz->active_participants_count = 0;

            }
            $quiz->enrolled_participants_count = DB::table('class_quizzes')
                ->join('class_users', 'cq_class_id', '=', 'cu_class_id')
                ->join('users', 'cu_user_id', '=', 'u_id')
                ->where('cq_quiz_id', '=', $quiz_id)
                ->where('u_role', '=', 1)
                ->count();
        }
        return $quiz;
    }

    public static function StartQuiz($quiz_id) {
        //create a new session for this quiz
        $quiz_data = self::GetQuizCurrentData($quiz_id);
        try {
            DB::table('quiz_session')
                ->insertGetId([
                    'qs_phase' => 1,
                    'qs_reveal_answers' => 0,
                    'qs_quiz_id' => $quiz_id,
                    'qs_started_at' => Carbon::now('Europe/Copenhagen')->toDateTimeString(),
                    'qs_quiz_data' => json_encode($quiz_data)
                ]);
            return ['status' => true, 'message' => 'success'];
        } catch (\Exception $e) {

            return ['status' => false, 'message' => $e->getMessage() . ' in file: ' . $e->getFile() . ' at line: ' . $e->getLine() . ' trace: ' . $e->getTraceAsString()];

        }
    }

    public static function StopQuiz($quiz_id) {
        //stop the current active session
        $session = self::GetQuizActiveSession($quiz_id);
        if ($session !== null) {
            try {
                DB::table('quiz_session')
                    ->where('qs_id', '=', $session->id)
                    ->whereNull('qs_stopped_at')
                    ->update([
                        'qs_stopped_at' => Carbon::now('Europe/Copenhagen')->toDateTimeString()
                    ]);

            } catch (\Exception $e) {

                return ['status' => false, 'message' => $e->getMessage() . ' in file: ' . $e->getFile() . ' at line: ' . $e->getLine() . ' trace: ' . $e->getTraceAsString()];

            }
        }
        //deactivate the quiz scheduling
        try {
            DB::table('quiz_scheduling')
                ->where('qsch_quiz_id', '=', $quiz_id)
                ->update([
                    'qsch_active' => 0
                ]);
            return ['status' => true, 'message' => 'success'];

        } catch (\Exception $e) {

            return ['status' => false, 'message' => $e->getMessage() . ' in file: ' . $e->getFile() . ' at line: ' . $e->getLine() . ' trace: ' . $e->getTraceAsString()];
        }
    }

    public static function ModifyQuizStatus($quiz_id, $quiz_status)
    {
        if ($quiz_status == Config::get('defines.QUIZ_STATUS_RUNNING')) {
            if (is_array($quiz_id)) {
                foreach ($quiz_id as $quiz) {
                    if(self::GetQuizStatus($quiz) == Config::get('defines.QUIZ_STATUS_STOPPED')) {
                    self::StartQuiz($quiz);
                    }
                }
            } else {
                if(self::GetQuizStatus($quiz_id) == Config::get('defines.QUIZ_STATUS_STOPPED')) {
                    self::StartQuiz($quiz_id);
                }
            }
        } else {
            if (is_array($quiz_id)) {
                foreach ($quiz_id as $quiz) {
                    if(self::GetQuizStatus($quiz_id) == Config::get('defines.QUIZ_STATUS_RUNNING')) {
                        self::StopQuiz($quiz);
                    }
                }
            } else {
                if(self::GetQuizStatus($quiz_id) == Config::get('defines.QUIZ_STATUS_RUNNING')) {
                    self::StopQuiz($quiz_id);
                }
            }
        }
        return ['status' => true, 'message' => 'success'];

    }

    public static function ModifyQuizActivePhase($quiz_id, $quiz_phase)
    {

        $now = Carbon::now('Europe/Copenhagen')->toDateTimeString();
        $update_array = ['qs_phase' => $quiz_phase];
        if ($quiz_phase == 2) {
            $update_array['qs_enabled_rev_ph'] = $now;
        }
        try {
            if (is_array($quiz_id)) {
                foreach ($quiz_id as $quiz) {
                    DB::table('quiz_session')
                        ->where('qs_quiz_id', '=', $quiz)
                        ->whereNull('qs_stopped_at')
                        ->update($update_array);
                }
            } else {
                DB::table('quiz_session')
                    ->where('qs_quiz_id', '=', $quiz_id)
                    ->whereNull('qs_stopped_at')
                    ->update($update_array);
            }

            return ['status' => true, 'message' => 'success'];

        } catch (\Exception $e) {

            return ['status' => false, 'message' => $e->getMessage() . ' in file: ' . $e->getFile() . ' at line: ' . $e->getLine() . ' trace: ' . $e->getTraceAsString()];

        }
    }

    public static function ModifyQuizRevealAnswersStatus($quiz_id, $reveal_answers_status)
    {
        try {
            if (is_array($quiz_id)) {
                foreach ($quiz_id as $id) {
                    DB::table('quiz_session')
                        ->where('qs_quiz_id', '=', $id)
                        ->whereNull('qs_stopped_at')
                        ->update([
                            'qs_reveal_answers' => $reveal_answers_status,
                            'qs_revealed_ans_at' => Carbon::now('Europe/Copenhagen')->toDateTimeString(),
                        ]);
                }
            } else {
                DB::table('quiz_session')
                    ->where('qs_quiz_id', '=', $quiz_id)
                    ->whereNull('qs_stopped_at')
                    ->update([
                        'qs_reveal_answers' => $reveal_answers_status,
                        'qs_revealed_ans_at' => Carbon::now('Europe/Copenhagen')->toDateTimeString(),
                    ]);
            }
            return ['status' => true, 'message' => 'success'];

        } catch (\Exception $e) {

            return ['status' => false, 'message' => $e->getMessage() . ' in file: ' . $e->getFile() . ' at line: ' . $e->getLine() . ' trace: ' . $e->getTraceAsString()];

        }
    }

    public static function ParticipantSubmitQuestionResponse($session_id, $phase, $question_id, $answer_index, $duration, $participant_id,$correctness)
    {

        try {
            $response_id = DB::table('quiz_responses')
                ->insertGetId([
                    'qr_session_id' => $session_id,
                    'qr_phase' => $phase,
                    'qr_question_id' => $question_id,
                    'qr_answer_index' => $answer_index,
                    'qr_correctness'=>$correctness,
                    'qr_qp_id' => $participant_id,
                    'qr_duration' => $duration,
                    'qr_date' => Carbon::now('Europe/Copenhagen')->toDateTimeString()
                ]);

            return ['status' => true, 'message' => 'success', 'id' => $response_id];

        } catch (\Exception $e) {

            return ['status' => false, 'id' => null, 'message' => $e->getMessage() . ' in file: ' . $e->getFile() . ' at line: ' . $e->getLine() . ' trace: ' . $e->getTraceAsString()];

        }
    }

    public static function ParticipantSubmitAccQuestionResponse($session_id, $response_id, $question_id, $answer_index, $answer_content, $phase, $acc_question_type, $participant_id)
    {
        try {

            if ($acc_question_type != 'just-question' && $acc_question_type != 'other-question') {
                DB::table('quiz_responses_acc_questions')
                    ->insertGetId([
                        'qraq_session_id' => $session_id,
                        'qraq_response_id' => $response_id,
                        'qraq_acc_id' => $question_id,
                        'qraq_acc_answer_index' => $answer_index,
                        'qraq_acc_answer_content' => $answer_content,
                        'qraq_phase' => $phase,
                        'qraq_qp_id' => $participant_id
                    ]);
            } else if ($acc_question_type == 'just-question') {
                DB::table('quiz_responses_acc_questions')
                    ->insertGetId([
                        'qraq_session_id' => $session_id,
                        'qraq_response_id' => $response_id,
                        'qraq_acc_id' => $question_id,
                        'qraq_acc_answer_index' => null,
                        'qraq_acc_answer_content' => $answer_content,
                        'qraq_phase' => $phase,

                        'qraq_qp_id' => $participant_id
                    ]);
            } else if ($acc_question_type == 'other-question') {

                DB::table('quiz_responses_acc_questions')
                    ->insertGetId([
                        'qraq_session_id' => $session_id,
                        'qraq_response_id' => $response_id,
                        'qraq_acc_id' => $question_id,
                        'qraq_acc_answer_index' => $answer_index,
                        'qraq_acc_answer_content' => $answer_content,
                        'qraq_phase' => $phase,

                        'qraq_qp_id' => $participant_id
                    ]);
            }
            return ['status' => true, 'message' => 'success'];

        } catch (\Exception $e) {

            return ['status' => false, 'message' => $e->getMessage() . ' in file: ' . $e->getFile() . ' at line: ' . $e->getLine() . ' trace: ' . $e->getTraceAsString()];

        }

    }

    public static function GetQuizPhase($quiz_id)
    {
        $result = DB::table('quiz_session')
            ->where('qs_quiz_id', '=', $quiz_id)
            ->whereNull('qs_stopped_at')
            ->select('qs_phase as phase')
            ->pluck('phase')
            ->first();
        return $result;
    }

    public static function GetQuizRevealAnswersStatus($quiz_id)
    {
        $result = DB::table('quiz_session')
            ->where('qs_quiz_id', '=', $quiz_id)
            ->whereNull('qs_stopped_at')
            ->select('qs_reveal_answers as reveal_answers')
            ->pluck('reveal_answers')
            ->first();
        return $result;
    }

    public static function GetAccQuestionTextResponsesFromInitialPhase($session_id, $question_id, $acc_question_id, $no_responses_associated = false)
    {
        $responses = DB::table('quiz_responses_acc_questions')
            ->where('qraq_session_id', '=', $session_id)
            ->where('qraq_acc_id', '=', $acc_question_id)
            ->where('qr_question_id', '=', $question_id);

        if ($no_responses_associated == false) {
            $responses = $responses->leftJoin('quiz_responses', 'qraq_response_id', '=', 'qr_id');
        } else {
            $responses = $responses->leftJoin('quiz_responses', 'qraq_qp_id', '=', 'qr_qp_id');
        }
        $responses = $responses->select(DB::raw('DISTINCT qraq_id as id'), 'qr_answer_index as answer_index', 'qr_question_id as question_id', 'qraq_acc_answer_content as answer_content')
            ->orderBy(DB::raw('LENGTH(answer_content)'), 'desc')
            ->get()
            ->toArray();
        return $responses;
    }

    public static function GetAccQuestionAverageResponsesFromInitialPhase($session_id, $question_id, $acc_question_id, $no_responses_associated)
    {
        $responses = DB::table('quiz_responses_acc_questions')
            ->where('qraq_session_id', '=', $session_id)
            ->where('qraq_acc_id', '=', $acc_question_id)
            ->where('qr_question_id', '=', $question_id);
        if ($no_responses_associated == false) {
            $responses = $responses->leftJoin('quiz_responses', 'qraq_response_id', '=', 'qr_id');
        } else {
            $responses = $responses->leftJoin('quiz_responses', 'qraq_qp_id', '=', 'qr_qp_id');
        }

        $responses = $responses->select('qr_answer_index as answer_index', DB::raw('AVG(qraq_acc_answer_content) as responses'))
            ->groupBy('qr_answer_index')
            ->get()
            ->mapToGroups(function ($item, $key) {
                return [$item->answer_index => $item->responses];
            })
            ->toArray();
        return $responses;
    }

    public static function GetQuizSessions($quiz_id, $is_stopped)
    {
        $result = DB::table('quiz_session')
            ->leftjoin('participants_progress','pr_session_id','=','qs_id')
            ->where('qs_quiz_id', '=', $quiz_id);
        if ($is_stopped !== null) {
            if ($is_stopped == true) {
                $result = $result->whereNotNull('qs_stopped_at');
            } else {
                $result = $result->whereNull('qs_stopped_at');
            }
        }
        $result = $result->select('qs_id as id', 'qs_started_at as started_at', 'qs_stopped_at as stopped_at',
            DB::raw('COUNT(DISTINCT pr_id) as responses'))
            ->groupBy('qs_id')
            ->orderBy('stopped_at')
            ->paginate(10);

        return $result;
    }

    public static function GetQuestionAnswerInitialPhaseResponses($session_id, $question_id, $answer_index)
    {
        $result = DB::table('quiz_responses')
            ->where('qr_session_id', '=', $session_id)
            ->where('qr_question_id', '=', $question_id)
            ->where('qr_phase', '=', 1)
            ->where('qr_answer_index', '=', $answer_index)
            ->select('qr_id')
            ->count();
        return $result;
    }

    public static function GetQuestionInitialPhaseResponses($session_id, $question_id)
    {
        $result = DB::table('quiz_responses')
            ->where('qr_session_id', '=', $session_id)
            ->where('qr_question_id', '=', $question_id)
            ->where('qr_phase', '=', 1)
            ->select('qr_id')
            ->count();
        return $result;
    }

    public static function GetParticipantResults($participant_id, $session_id)
    {
        $sessions = DB::table('participants_progress')
            ->where('pr_id', '=', $participant_id)
            ->where('pr_finished', '=', true)
            ->select('pr_id as participant_id', 'pr_session_id as session_id', 'pr_started_at as started_at', DB::raw('COUNT(pr_id) as tries'))
            ->groupBy('participant_id')->get();
        $responses = DB::table('quiz_responses')
            ->where('qr_qp_id', '=', $participant_id)
            ->where('qr_session_id', '=', $session_id)
            ->select('qr_phase as phase', 'qr_question_id as question_id',
                'qr_answer_index as answer_index', 'qr_duration as duration', 'qr_date as date')
            ->get();
        $result = new \stdClass();
        $result->sessions = $sessions;
        $result->responses = $responses;
        return $result;
    }

    public static function GetParticipantSessions($user_id, $quiz_id)
    {
        $result = DB::table('participants_progress')
            ->join('quiz_session', 'qs_id', '=', 'pr_session_id')
            ->where('pr_user_id', '=', $user_id)
            ->where('qs_quiz_id', '=', $quiz_id)
            ->where('pr_finished', '=', 1)
            ->select('pr_id as progress_id', 'qs_id as session_id', 'pr_started_at')
            ->orderBy('pr_started_at', 'desc')
            ->get();

        return $result;
    }

    public static function GetParticipantProgressSessionId($progress_id)
    {
        $result = DB::table('participants_progress')
            ->where('pr_id', '=', $progress_id)
            ->select('pr_session_id as session_id')
            ->pluck('session_id')
            ->first();
        return $result;
    }

    public static function CheckOngoingProgressOnSession($user_id, $session_id)
    {
        $result = DB::table('participants_progress')
            ->where('pr_user_id', '=', $user_id)
            ->where('pr_session_id', '=', $session_id)
            ->where('pr_finished', '=', 0)
            ->select('pr_id as id')
            ->first();
        return $result;
    }

    public static function AddParticipantScore($participant_id, $reason)
    {
        DB::table('participant_scores')
            ->insert([
                'ps_participant_id' => $participant_id,
                'ps_reason' => $reason,
                'ps_value' => Config::get('defines.SCORE-' . $reason),
                'ps_date' => Carbon::now('Europe/Copenhagen')->toDateTimeString()
            ]);
    }

    public static function GetQuizOtherQuestionsCount($quiz_id, $position = null)
    {
        $other_acc_questions = DB::table('accompanying_questions')
            ->where('aq_quiz_id', '=', $quiz_id)
            ->where('aq_type', '=', '4');

        if ($position !== null) {
            $other_acc_questions = $other_acc_questions
                ->where('aq_position', '=', $position);
        }
        $result = $other_acc_questions
            ->count('aq_id');
        return $result;
    }

    public static function InitialPhaseResponse($session_id, $question_id, $participant_id)
    {
        //TODO Add possible responses from other questions (similar to conf question)
        $response = DB::table('quiz_responses')
            ->where('qr_qp_id', '=', $participant_id)
            ->where('qr_session_id', '=', $session_id)
            ->where('qr_question_id', '=', $question_id)
            ->select('qr_id as response_id',
                'qr_answer_index as response')
            ->first();

        if ($response != null) {
            $response->acc_responses = DB::table('quiz_responses_acc_questions')
                ->where('qraq_response_id', '=', $response->response_id)
                ->select('qraq_acc_answer_index as acc_question_response', 'qraq_acc_id as acc_question_id')
                ->get();
        }
        return $response;
    }

    public static function AddQuizScheduling($quiz_id, $init_ph_start, $init_ph_stop, $rev_ph_start, $rev_ph_stop, $reveal_ans_start, $reveal_ans_stop): ?array
    {
        try {

            $new_dates = [
                'qsch_init_ph_start' => DBDateFormat($init_ph_start),
                'qsch_init_ph_end' => DBDateFormat($init_ph_stop),
                'qsch_rev_ph_start' => DBDateFormat($rev_ph_start),
                'qsch_rev_ph_end' => DBDateFormat($rev_ph_stop),
                'qsch_rev_ans_start' => DBDateFormat($reveal_ans_start),
                'qsch_rev_ans_end' => DBDateFormat($reveal_ans_stop),
                'qsch_active' => 1
            ];
            DB::table('quiz_scheduling')
                ->updateOrInsert([
                    'qsch_quiz_id' => $quiz_id,
                ], $new_dates);
            return ['status' => true, 'message' => 'success'];

        } catch (\Exception $e) {

            return ['status' => false, 'message' => $e->getMessage() . ' in file: ' . $e->getFile() . ' at line: ' . $e->getLine() . ' trace: ' . $e->getTraceAsString()];
        }
    }

    public static function DeactivateQuizScheduling($quiz_id): ?array
    {
        try {
            DB::table('quiz_scheduling')
                ->where('qsch_quiz_id', '=', $quiz_id)
                ->update([
                    'qsch_active' => 0
                ]);
            return ['status' => true, 'message' => 'success'];

        } catch (\Exception $e) {

            return ['status' => false, 'message' => $e->getMessage() . ' in file: ' . $e->getFile() . ' at line: ' . $e->getLine() . ' trace: ' . $e->getTraceAsString()];
        }
    }

    public static function GetQuizParticipationLimit($quiz_id): int
    {
        $result = DB::table('quizzes')
            ->where('quiz_id', '=', $quiz_id)
            ->select('quiz_participation_limit as participation_limit')
            ->pluck('participation_limit')
            ->first();
        return $result;
    }

    public static function SetQuizParticipationLimit($quiz_id, $limit): ?array
    {
        try {

            DB::table('quizzes')
                ->where('quiz_id', '=', $quiz_id)
                ->update(['quiz_participation_limit' => $limit]);

            return ['status' => true, 'message' => 'success'];

        } catch (\Exception $e) {

            return ['status' => false, 'message' => $e->getMessage() . ' in file: ' . $e->getFile() . ' at line: ' . $e->getLine() . ' trace: ' . $e->getTraceAsString()];
        }
    }

    public static function GetQuizSchedulingInfo($quiz_id)
    {
        $result = DB::table('quizzes')
            ->where('quiz_id', '=', $quiz_id)
            ->leftJoin('quiz_scheduling', 'quiz_id', '=', 'qsch_quiz_id')
            ->select('qsch_init_ph_start as init_start',
                'qsch_id as id',
                'qsch_active as active',
                'qsch_init_ph_end as init_end',
                'qsch_rev_ph_start as rev_start',
                'qsch_rev_ph_end as rev_end',
                'qsch_rev_ans_start as ans_start',
                'qsch_rev_ans_end as ans_end',
                'quiz_participation_limit as participation_limit'
            )
            ->first();
        return $result;
    }


    public static function GetQuizzesForAutomaticQuizScheduling(string $now)
    {
        $result = DB::table('quiz_scheduling')
            ->where(function ($query) use ($now) {
                $query->where('qsch_init_ph_start', '=', $now)
                    ->orWhere('qsch_init_ph_end', '=', $now)
                    ->orWhere('qsch_rev_ph_start', '=', $now)
                    ->orWhere('qsch_rev_ph_end', '=', $now)
                    ->orWhere('qsch_rev_ans_start', '=', $now)
                    ->orWhere('qsch_rev_ans_end', '=', $now);
            })
            ->where('qsch_active', '=', 1)
            ->select(
                'qsch_init_ph_start as init_start',
                'qsch_init_ph_end as init_end',
                'qsch_rev_ph_start as rev_start',
                'qsch_rev_ph_end as rev_end',
                'qsch_rev_ans_start as ans_start',
                'qsch_rev_ans_end as ans_end',
                'qsch_quiz_id as quiz_id')->get();
        return $result;
    }

    public static function QuizCheckAnonymousParticipation($quiz_id)
    {
        $allow_anonymous = DB::table('quizzes')
            ->where('quiz_id', '=', $quiz_id)
            ->select('quiz_allow_anonymous_participation')
            ->pluck('quiz_allow_anonymous_participation')
            ->first();
        if ($allow_anonymous !== null) {
            return $allow_anonymous == 1;
        } else {
            return null;
        }
    }

    public static function GetQuizPresentationInfo($session_id)
    {
        $session = DB::table('quiz_session')
            ->where('qs_id', '=', $session_id)
            ->select('qs_id as id', 'qs_phase as phase', 'qs_quiz_data as quiz_data')
            ->first();
        if ($session !== null) {
            $session->data = json_decode($session->quiz_data, true);
            $session->questions = json_decode(json_encode($session->data['questions']), 1);
            $session->responses = DB::table('quiz_session')
                ->where('qs_id', '=', $session_id)
                ->join('quiz_responses', 'qs_id', 'qr_session_id')
                ->select('qr_id as id', 'qr_question_id as question_id', 'qr_answer_index as answer_index',
                    'qr_phase as phase')
                ->orderBy('qr_question_id')
                ->orderBy('qr_answer_index')
                ->get()
                ->toArray();
        }
        return $session;
    }

    public static function CheckDuplicateResponse($session_id, $phase, $question_id, $participant_id)
    {
        $result = DB::table('quiz_responses')
            ->where('qr_session_id', '=', $session_id)
            ->where('qr_phase', '=', $phase)
            ->where('qr_question_id', '=', $question_id)
            ->where('qr_qp_id', '=', $participant_id)
            ->first();
        return $result !== null;

    }

    public static function CheckDuplicateAccQuestionResponse($session_id, $question_id, $participant_id)
    {
        $result = DB::table('quiz_responses_acc_questions')
            ->where('qraq_session_id', '=', $session_id)
            ->where('qraq_acc_id', '=', $question_id)
            ->where('qraq_qp_id', '=', $participant_id)
            ->first();
        return $result !== null;
    }

    public static function GetParticipantPastAttendance($session_id, $user_id)
    {
        $result = DB::table('participants_progress')
            ->where('pr_user_id', '=', $user_id)
            ->where('pr_session_id', '=', $session_id)
            ->where('pr_finished', '=', 1)
            ->get()
            ->count();
        return $result;
    }

    public static function GetScoresPageQuizzes($user_id)
    {
        $quizzes = DB::table('quizzes')
            ->leftJoin('class_quizzes', 'quiz_id', '=', 'cq_quiz_id')
            ->leftJoin('classes', 'class_id', '=', 'cq_class_id')
            ->where('quiz_created_by', '=', $user_id);

        $quizzes = $quizzes->select('quiz_id as id',
            'quiz_title as title',
            'class_id')
            ->groupBy('quiz_id','class_id')
            ->get();
        foreach ($quizzes as $quiz) {
            $quiz->sessions = DB::table('quiz_session')
                ->where('qs_quiz_id','=',$quiz->id)
                ->select('qs_id as id','qs_started_at as started_at','qs_stopped_at as stopped_at')
                ->get();
        }
        return $quizzes;
    }

    public static function GetQuizScores($quiz_id, $session, $assessment)
    {
        try {
            $participant_info = 'u_id';
            if ($assessment === true) {
                $participant_info = 'CONCAT(u_first_name, \' \', u_last_name)';
            }
            DB::enableQueryLog();
            $responses = DB::table('quiz_responses')
                ->leftJoin('participants_progress', 'qr_qp_id', '=', 'pr_id')
                ->leftJoin('quiz_session', 'qs_id', 'qr_session_id')
                ->join('users', 'u_id', 'pr_user_id')

                ->select('qr_phase as phase',
                    'qr_session_id as session_id',
                    DB::raw($participant_info . ' as name'), 'qr_qp_id as participant_id', DB::raw('SUM(qr_correctness) as score'),'pr_started_at as started_at')
                ->groupBy('phase', 'qr_qp_id', 'session_id','started_at');

            if ($quiz_id !== null) {
                $responses = $responses->where('qs_quiz_id', '=', $quiz_id);
            }
            if ($session !== null) {
                $responses = $responses->where('qr_session_id', '=', $session);
            }

            $responses = $responses->get()->groupBy('participant_id');
            return ['status' => true, 'message' => 'success', 'content' => $responses];
        } catch (\Exception $e) {
            return ['status' => false, 'message' => $e->getMessage() . ' in file: ' . $e->getFile() . ' at line: ' . $e->getLine(), 'content' => null];

        }
    }

    public static function IsQuizAssessed($id)
    {
        $result = DB::table('quizzes')
            ->where('quiz_id','=',$id)
            ->select('quiz_is_assessed')
            ->first();
        if($result !== null) {
            return $result->quiz_is_assessed == 1;
        }
        return null;
    }

    public static function GetParticipantScores($id, $teacher_id)
    {
        try {
            $participant_info = 'u_id';

            $responses = DB::table('quiz_responses')
                ->leftJoin('participants_progress', 'qr_qp_id', '=', 'pr_id')
                ->leftJoin('quiz_session', 'qs_id', 'qr_session_id')
                ->leftJoin('quizzes','quiz_id','=','qs_quiz_id')
                ->join('users', 'u_id', 'pr_user_id')

                ->where('u_id','=',$id)
                ->where('quiz_created_by','=',$teacher_id)
                ->select('qr_phase as phase',
                    'qs_started_at as date',
                    'qr_session_id as session_id',
                     'quiz_id as name',
                     'qr_qp_id as participant_id', DB::raw('SUM(qr_correctness) as score'),'pr_started_at as started_at')
                ->groupBy('phase', 'qr_qp_id', 'session_id','started_at');

            $responses = $responses->get()->groupBy('participant_id');

            return ['status' => true, 'message' => 'success', 'content' => $responses];
        } catch (\Exception $e) {
            return ['status' => false, 'message' => $e->getMessage() . ' in file: ' . $e->getFile() . ' at line: ' . $e->getLine(), 'content' => null];

        }
    }

    public static function UpdateQuizTitle($quiz_id, $quiz_title)
    {
        try {
            DB::table('quizzes')
                ->where('quiz_id','=',$quiz_id)
                ->update(['quiz_title'=>$quiz_title]);
            return ['status' => true, 'message' => 'success'];
        } catch (\Exception $e) {
            return ['status' => false, 'message' => $e->getMessage() . ' in file: ' . $e->getFile() . ' at line: ' . $e->getLine(), 'content' => null];

        }
    }

    public static function GetOpenSession($quiz_id)
    {
        $session = DB::table('quiz_session')
            ->where('qs_quiz_id', '=', $quiz_id)
            ->whereNull('qs_stopped_at')
            ->first();

        return $session;
    }

    public static function GetSessionResponsesByPhase($session_id)
    {
        $result = DB::table('quiz_responses')
            ->where('qr_session_id','=',$session_id)
            ->select('qr_id as id','qr_phase as phase','qr_correctness as correctness','qr_date as date','qr_qp_id as participant_id')
            ->get();
        return $result;
    }


}
